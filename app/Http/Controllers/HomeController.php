<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Stock;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{
    // Halaman Landing / Katalog Menu
    public function index()
    {
        $categories = Category::all();
        $products = Product::with('category')->get();
        $heroProducts = $products->sortByDesc("is_best_seller")->take(2)->values();
        $branches = Branch::query()->official()->orderBy('name')->get();
        $branchOperationalData = $branches->mapWithKeys(function (Branch $branch) {
            $key = Str::lower(Str::afterLast($branch->name, ' '));

            return [$key => [
                'id' => $branch->id,
                'isOpen' => $branch->isOpenAt(),
                'hours' => $branch->operatingHours(),
            ]];
        });

        return view('landing', compact('categories', 'products', 'heroProducts', 'branchOperationalData'));
    }

    // Halaman Keranjang Belanja (Sinkron dengan Database User)
    public function cart()
    {
        if (Auth::check()) {
            $userCart = Auth::user()->cart ? json_decode(Auth::user()->cart, true) : [];
            $sessionCart = session()->get('cart', []);

            // Jika session kosong setelah logout-login, ambil dari DB
            if (empty($sessionCart) && !empty($userCart)) {
                $cart = $userCart;
                session()->put('cart', $cart);
            } else {
                $cart = $sessionCart;
                // Simpan kondisi keranjang terbaru ke DB
                Auth::user()->update(['cart' => json_encode($cart)]);
            }
        } else {
            $cart = session()->get('cart', []);
        }

        return view('cart', compact('cart'));
    }

    // Tambah Item ke Keranjang (Simpan ke Session & DB)
    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $request->input('quantity', 1);
        } else {
            $cart[$id] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => $product->price,
                'quantity' => $request->input('quantity', 1),
                'image'    => $product->image ?? null,
            ];
        }

        session()->put('cart', $cart);

        // Simpan ke database jika user sudah login
        if (Auth::check()) {
            Auth::user()->update(['cart' => json_encode($cart)]);
        }

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan ke keranjang!');
    }

    // Halaman Checkout
    public function checkout(Request $request, $id = null)
    {
        // Checkout may only use the official branch records. Creating records
        // named only after a location would split dashboard sales totals.
        $branches = Branch::query()->official()->orderBy('name')->get();
        $selectedBranchId = old('branch_id', session('selected_branch_id'));

        if ($id) {
            $product = Product::findOrFail($id);
            $cartItems = [
                $product->id => [
                    'id'       => $product->id,
                    'name'     => $product->name,
                    'price'    => $product->price,
                    'quantity' => 1,
                ]
            ];
        } else {
            $cartItems = session()->get('cart', []);

            if ($request->has('items') && !empty($request->query('items'))) {
                $selectedIds = explode(',', $request->query('items'));
                $cartItems = array_intersect_key($cartItems, array_flip($selectedIds));
            }
        }

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Silakan pilih minimal 1 menu untuk di-checkout!');
        }

        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        return view('checkout', compact('cartItems', 'totalAmount', 'branches', 'selectedBranchId'));
    }

    // Memproses Pembuatan Transaksi Pesanan (Dengan Input Data Pemesan)
    public function order(Request $request)
    {
        // Validasi input data pemesan dari form checkout
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'required|email|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string',
            'branch_id'        => [
                'required',
                Rule::exists('branches', 'id')->whereIn('name', Branch::OFFICIAL_NAMES),
            ],
            'payment_method'   => 'required|string',
        ]);

        $cart = session()->get('cart', []);
        $cartItems = [];

        if ($request->has('selected_items') && !empty($request->input('selected_items'))) {
            $selectedIds = is_array($request->input('selected_items')) 
                ? $request->input('selected_items') 
                : explode(',', $request->input('selected_items'));
            
            $cartItems = array_intersect_key($cart, array_flip($selectedIds));
        } else {
            $cartItems = $cart;
        }

        if (empty($cartItems) && $request->has('product_id')) {
            $product = Product::findOrFail($request->product_id);
            $cartItems = [
                $product->id => [
                    'id'       => $product->id,
                    'name'     => $product->name,
                    'price'    => $product->price,
                    'quantity' => $request->input('quantity', 1),
                ]
            ];
        }

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada item untuk dipesan.');
        }

        // Fetch through the same official scope as the checkout selector, so a
        // crafted request cannot attach an order to an obsolete branch.
        $branch = Branch::query()->official()->findOrFail($validated['branch_id']);

        if (! $branch->isOpenAt()) {
            return redirect()->back()
                ->withInput()
                ->with('error', $branch->closedMessage());
        }

        $branchId = $branch->id;

        DB::transaction(function () use ($cartItems, $branchId, $request, $cart) {
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }

            // Simpan data transaksi beserta info pemesan
            $transaction = Transaction::create([
                'invoice_number'   => 'INV-' . date('YmdHis') . '-' . rand(100, 999),
                'branch_id'        => $branchId,
                'user_id'          => Auth::id(),
                'customer_name'    => $request->customer_name,
                'customer_email'   => $request->customer_email,
                'customer_phone'   => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'total_amount'     => $totalAmount,
                'payment_method'   => $request->payment_method,
                'status'           => Transaction::STATUS_PENDING,
            ]);

            foreach ($cartItems as $item) {
                $product = Product::find($item['id']);
                $qty = $item['quantity'];

                if ($product) {
                    $stock = Stock::firstOrCreate(
                        [
                            'product_id' => $product->id,
                            'branch_id'  => $branchId
                        ],
                        ['current_stock' => 50, 'sold_stock' => 0]
                    );

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id'     => $product->id,
                        'qty'            => $qty,
                        'quantity'       => $qty,
                        'price'          => $item['price'],
                        'subtotal'       => $item['price'] * $qty,
                    ]);

                    if (isset($stock->current_stock)) {
                        $stock->decrement('current_stock', $qty);
                    }
                    $stock->increment('sold_stock', $qty);
                }
            }

            if (!empty($cart)) {
                $updatedCart = array_diff_key($cart, $cartItems);
                session()->put('cart', $updatedCart);

                // Bersihkan / perbarui keranjang di database user
                if (Auth::check()) {
                    Auth::user()->update(['cart' => json_encode($updatedCart)]);
                }
            }
        });

        return redirect()->route('orders.history')->with('success', 'Pesanan berhasil dibuat!');
    }

    // Halaman Riwayat Pesanan
    public function history()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk melihat riwayat pesanan.');
        }

        $transactions = Transaction::where('user_id', Auth::id())
            ->with(['details.product', 'branch'])
            ->latest()
            ->get();

        return view('history', compact('transactions'));
    }
}
