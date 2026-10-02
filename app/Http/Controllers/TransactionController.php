<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function order(Request $request)
    {
        // 1. Validasi Data dari Form Checkout
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

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        // 2. Filter Item yang Dicentang
        $selectedIds = $request->input('selected_items') 
            ? explode(',', $request->input('selected_items')) 
            : array_keys($cart);

        $orderItems = array_filter($cart, function ($key) use ($selectedIds) {
            return in_array((string)$key, $selectedIds);
        }, ARRAY_FILTER_USE_KEY);

        if (empty($orderItems)) {
            return redirect()->back()->with('error', 'Pilih minimal satu produk untuk di-checkout.');
        }

        // 3. Hitung Total Pembayaran
        $totalAmount = 0;
        foreach ($orderItems as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        // 4. Cari Data Cabang Berdasarkan ID yang Dipilih
        $branch = Branch::query()->official()->findOrFail($validated['branch_id']);

        if (! $branch->isOpenAt()) {
            return redirect()->back()
                ->withInput()
                ->with('error', $branch->closedMessage());
        }

        DB::beginTransaction();
        try {
            // 5. Simpan Transaksi (WAJIB menyimpan branch_id)
            $transaction = Transaction::create([
                'invoice_number' => 'INV-'.now()->format('YmdHis').'-'.random_int(100, 999),
                'user_id'        => auth()->id(),
                'branch_id'      => $branch->id,
                'customer_name'  => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'total_amount'   => $totalAmount,
                'payment_method' => $request->payment_method,
                'status'         => Transaction::STATUS_PENDING,
            ]);

            // 6. Simpan Detail Item Pesanan
            foreach ($orderItems as $productId => $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $productId,
                    'quantity'       => $item['quantity'],
                    'price'          => $item['price'],
                ]);

                // Hapus item yang sudah dipesan dari session keranjang
                unset($cart[$productId]);
            }

            session()->put('cart', $cart);
            DB::commit();

            return redirect()->route('cart.index')->with('success', 'Pesanan berhasil dibuat! ID Nota: #' . $transaction->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }
}
