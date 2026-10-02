<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pesanan - Fav Dimsum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-orange-50 text-gray-800 font-sans min-h-screen flex items-start sm:items-center justify-center p-3 sm:p-4">

    <div class="max-w-md w-full bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-orange-100 shadow-xl my-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-3 text-center">Halaman Pembayaran</h2>

        <!-- Tampilkan Pesan Error Validasi (jika ada) -->
        @if(session('error'))
            <div class="mb-4 p-3 bg-orange-100 border border-orange-200 text-orange-700 text-xs rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-orange-100 border border-orange-200 text-orange-700 text-xs rounded-xl">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('order') }}" method="POST">
            @csrf

            <!-- Hidden Input ID Item yang Dicentang -->
            <input type="hidden" name="selected_items" value="{{ implode(',', array_keys($cartItems)) }}">

            <!-- Data Pemesan (terisi otomatis dari akun login) -->
            <div class="mb-5 rounded-2xl border border-orange-100 bg-orange-50/60 p-4">
                <p class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-400">Data Pemesan</p>

                <div class="mb-4 rounded-xl border border-orange-100 bg-white p-3 text-xs">
                    <p class="font-bold text-gray-800">Pemesan: {{ auth()->user()->name }}</p>
                    <p class="mt-1 text-gray-500">{{ auth()->user()->email }}</p>
                </div>

                <input type="hidden" name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}">
                <input type="hidden" name="customer_email" value="{{ old('customer_email', auth()->user()->email) }}">

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-gray-600">No. WhatsApp / HP</label>
                        <input type="tel" name="customer_phone" value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" required
                            class="w-full rounded-xl border border-gray-200 bg-white p-2.5 text-xs font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-600"
                            placeholder="08123456789">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-[11px] font-semibold text-gray-600">Alamat Lengkap</label>
                        <textarea name="customer_address" rows="2" required
                            class="w-full rounded-xl border border-gray-200 bg-white p-2.5 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-600"
                            placeholder="Masukkan alamat domisili / pengiriman">{{ old('customer_address') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Pilihan Cabang / Outlet -->
            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Pilih Cabang Gerai</label>
                <select name="branch_id" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-600 font-semibold text-gray-700">
                    <option value="" disabled {{ $selectedBranchId ? '' : 'selected' }}>-- Pilih Cabang --</option>
                    @foreach($branches as $branch)
                        @php($isOpen = $branch->isOpenAt())
                        <option value="{{ $branch->id }}" {{ (string) $selectedBranchId === (string) $branch->id ? 'selected' : '' }} {{ !$isOpen ? 'disabled' : '' }}>
                            {{ $isOpen ? '🟢' : '🔴' }} 📍 {{ str_starts_with($branch->name, 'Fav Dimsum') ? $branch->name : 'Fav Dimsum ' . $branch->name }} — {{ $isOpen ? 'GERAI BUKA' : 'GERAI TUTUP' }} ({{ $branch->operatingHours() }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Detail Pesanan -->
            <div class="mb-5">
                <p class="text-xs font-bold text-gray-400 uppercase mb-2">Detail Pesanan</p>
                <div class="space-y-3 max-h-48 overflow-y-auto pr-1">
                    @foreach($cartItems as $item)
                        <div class="flex justify-between items-start gap-3 bg-orange-50 p-3 rounded-xl text-sm">
                            <div>
                                <p class="font-bold text-gray-800">{{ $item['name'] }}</p>
                                <p class="text-xs text-gray-400">{{ $item['quantity'] }} porsi x Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                            <span class="font-bold text-orange-600">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Total Pembayaran -->
            <div class="flex justify-between items-center border-t border-b py-3 mb-5">
                <span class="font-bold text-gray-700">Total Pembayaran</span>
                <span class="text-lg font-black text-orange-600">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
            </div>

            <!-- Metode Pembayaran (Tunai & DANA saja) -->
            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Metode Pembayaran</label>
                <select id="payment_method" name="payment_method" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-600 font-semibold text-gray-700">
                    <option value="Tunai (Cash)">💵 Tunai (di Gerai)</option>
                    <option value="DANA">📱 Transfer DANA</option>
                </select>
            </div>

            <!-- Informasi Transfer DANA (Muncul Otomatis Jika DANA Dipilih) -->
            <div id="dana-info" class="hidden mb-6 p-4 bg-orange-50 border border-orange-200 rounded-2xl text-xs text-orange-900">
                <p class="font-bold mb-1">💳 Instruksi Pembayaran DANA:</p>
                <p>Silakan transfer ke nomor DANA berikut:</p>
                <p class="font-extrabold text-sm text-orange-600 my-1">0812-XXXX-XXXX (a.n Fav Dimsum)</p>
                <p class="text-[11px] text-orange-700">Tunjukkan bukti transfer ke kasir gerai saat mengambil pesanan.</p>
            </div>

            <!-- Tombol Aksi -->
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3.5 rounded-2xl shadow-lg transition text-sm">
                Konfirmasi Pesanan
            </button>
            <a href="{{ route('cart.index') }}" class="block text-center text-xs text-gray-400 hover:text-gray-600 mt-4 font-medium">
                &larr; Kembali ke Keranjang
            </a>
        </form>
    </div>

    <script>
        // Tampilkan/sembunyikan nomor DANA sesuai pilihan metode pembayaran
        const paymentSelect = document.getElementById('payment_method');
        const danaInfo = document.getElementById('dana-info');

        paymentSelect.addEventListener('change', function () {
            if (this.value === 'DANA') {
                danaInfo.classList.remove('hidden');
            } else {
                danaInfo.classList.add('hidden');
            }
        });
    </script>

</body>
</html>