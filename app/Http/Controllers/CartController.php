<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    // Menampilkan Halaman Keranjang
    public function index()
    {
        $cart = session()->get('cart', []);
        $selectedBranch = session('selected_branch_id')
            ? Branch::query()->official()->find(session('selected_branch_id'))
            : null;
        $selectedBranchIsOpen = ! $selectedBranch || $selectedBranch->isOpenAt();

        return view('cart', compact('cart', 'selectedBranch', 'selectedBranchIsOpen'));
    }

    // Menambah Produk Baru ke Keranjang dari Halaman Menu
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'branch_id' => [
                'nullable',
                Rule::exists('branches', 'id')->whereIn('name', Branch::OFFICIAL_NAMES),
            ],
        ]);

        if (! empty($validated['branch_id'])) {
            $branch = Branch::query()->official()->findOrFail($validated['branch_id']);

            if (! $branch->isOpenAt()) {
                return redirect()->back()->with('error', $branch->closedMessage());
            }

            session()->put('selected_branch_id', $branch->id);
        }

        $productId = $request->input('product_id');
        $product = Product::findOrFail($productId);

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => $product->price,
                'quantity' => 1,
                'image'    => $product->image ?? null,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', $product->name . ' berhasil ditambahkan ke keranjang!');
    }

    // Method Update Jumlah (Tambah / Kurang) dari Halaman Keranjang
    public function update(Request $request)
    {
        $cart = session()->get('cart', []);
        $productId = $request->input('product_id') ?? $request->input('id');
        $action = $request->input('action'); // 'increase' atau 'decrease'

        if ($productId && isset($cart[$productId])) {
            if ($action === 'increase' || $request->has('increase')) {
                $cart[$productId]['quantity']++;
            } elseif ($action === 'decrease' || $request->has('decrease')) {
                if ($cart[$productId]['quantity'] > 1) {
                    $cart[$productId]['quantity']--;
                } else {
                    unset($cart[$productId]);
                }
            } elseif ($request->has('quantity')) {
                $qty = (int) $request->input('quantity');
                if ($qty > 0) {
                    $cart[$productId]['quantity'] = $qty;
                } else {
                    unset($cart[$productId]);
                }
            }

            session()->put('cart', $cart);
        }
        if ($request->expectsJson()) {
            $item = $cart[$productId] ?? null;

            return response()->json([
                'quantity' => $item['quantity'] ?? 0,
                'subtotal' => $item ? $item['price'] * $item['quantity'] : 0,
                'cart_count' => collect($cart)->sum('quantity'),
            ]);
        }


        return redirect()->back();
    }

    // Menghapus Item dari Keranjang
    public function remove(Request $request, $id = null)
    {
        $cart = session()->get('cart', []);
        $productId = $id ?? $request->input('product_id');

        if ($productId && isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }
}
