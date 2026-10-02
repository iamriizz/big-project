<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Keranjang Belanja - Fav Dimsum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-orange-50 text-gray-800 font-sans min-h-screen flex flex-col justify-between">

    <!-- Header Navigation -->
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-orange-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-20 py-3 flex items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-orange-600 flex items-center justify-center text-white font-black text-lg shadow-md">
                    FD
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900 leading-tight">Fav Dimsum</h1>
                    <p class="text-xs text-orange-600 font-medium">Hangat & Segar Setiap Hari</p>
                </div>
            </a>

            <a href="{{ route('home') }}" class="shrink-0 text-xs font-semibold text-gray-600 hover:text-orange-600 border border-gray-300 px-3 sm:px-4 py-2 rounded-full transition">
                &larr; Kembali ke Menu
            </a>
        </div>
    </header>

    <!-- Content Keranjang -->
    <main class="max-w-4xl mx-auto px-4 py-10 w-full flex-1">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b">Keranjang Belanja</h1>

        @if(session('success'))
            <div class="mb-6 p-4 text-orange-700 bg-orange-50 border border-orange-200 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(count($cart) > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-orange-100 overflow-hidden mb-8">
                
                <!-- Baris Opsi Pilih Semua (Belum tercentang secara default) -->
                <div class="p-4 sm:px-6 bg-gray-50 border-b flex items-center gap-3">
                    <input type="checkbox" id="select-all" class="w-4 h-4 text-orange-600 accent-orange-600 rounded cursor-pointer">
                    <label for="select-all" class="text-xs font-bold text-gray-700 cursor-pointer select-none">Pilih Semua Menu</label>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($cart as $id => $item)
                        @php($subtotal = $item['price'] * $item['quantity'])
                        <div class="cart-item p-4 sm:p-6 flex items-start sm:items-center justify-between gap-3 sm:gap-4" data-product-id="{{ $id }}" data-price="{{ $item['price'] }}">
                            <div class="flex min-w-0 items-start sm:items-center gap-3.5 flex-1">
                                <input type="checkbox" class="item-checkbox w-4 h-4 text-orange-600 accent-orange-600 rounded cursor-pointer" value="{{ $id }}" data-subtotal="{{ $subtotal }}">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 text-sm sm:text-base mb-1 break-words">{{ $item['name'] }}</h3>
                                    <p class="text-xs text-gray-500 mb-3">Harga: Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                    <div class="flex items-center gap-2">
                                        <button type="button" data-cart-action="decrease" class="quantity-button w-8 h-8 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 flex items-center justify-center font-bold text-sm transition disabled:opacity-40" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>−</button>
                                        <span class="quantity-display text-xs font-bold text-gray-800 bg-orange-50 px-3 py-1.5 rounded-lg border border-orange-200 min-w-[70px] text-center">{{ $item['quantity'] }} Porsi</span>
                                        <button type="button" data-cart-action="increase" class="quantity-button w-8 h-8 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 flex items-center justify-center font-bold text-sm transition">+</button>
                                    </div>
                                </div>
                            </div>
                            <div class="shrink-0 text-right flex flex-col items-end gap-2">
                                <span class="item-subtotal font-extrabold text-orange-600 text-sm sm:text-base">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                <form action="{{ route('cart.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <button type="submit" class="text-xs font-semibold text-gray-400 hover:text-orange-600 transition">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-6 bg-gray-50 border-t flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div>
                        <span class="text-xs text-gray-500 block">Total Pembayaran:</span>
                        <span id="total-display" class="text-2xl font-black text-gray-900">Rp 0</span>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <a href="{{ route('home') }}" class="w-1/2 sm:w-auto text-center border border-gray-300 text-gray-700 hover:bg-gray-100 text-sm font-semibold px-5 py-2.5 rounded-full transition">Tambah Menu</a>
                        <a id="btn-checkout" href="#" title="{{ $selectedBranch && !$selectedBranchIsOpen ? 'Gerai Sedang Tutup' : '' }}" class="w-1/2 sm:w-auto text-center bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold px-6 py-2.5 rounded-full shadow transition opacity-50 pointer-events-none">Lanjut ke Pembayaran &rarr;</a>
                    </div>
                </div>
            </div>
        @else
            <!-- Tampilan Jika Keranjang Kosong -->
            <div class="bg-white rounded-2xl p-12 text-center border border-orange-100 shadow-sm">
                <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-2xl">
                    🛒
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Keranjang Kamu Masih Kosong</h3>
                <p class="text-gray-500 text-sm mb-6">Yuk, pilih dimsum favoritmu dan tambahkan ke keranjang!</p>
                <a href="{{ route('home') }}" class="bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold px-6 py-3 rounded-full shadow transition inline-block">
                    Lihat Katalog Menu
                </a>
            </div>
        @endif
    </main>

    <footer class="text-center py-6 text-xs text-gray-400 border-t">
        &copy; {{ date('Y') }} Fav Dimsum. Hak cipta dilindungi.
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('select-all');
            const totalDisplay = document.getElementById('total-display');
            const btnCheckout = document.getElementById('btn-checkout');
            const selectedBranchIsOpen = @json($selectedBranchIsOpen);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const updateUrl = "{{ route('cart.update') }}";

            const formatRupiah = value => 'Rp ' + Number(value).toLocaleString('id-ID');
            const itemCheckboxes = () => [...document.querySelectorAll('.item-checkbox')];

            function calculateTotal() {
                const checkboxes = itemCheckboxes();
                let total = 0;
                const selectedIds = [];

                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        total += Number(checkbox.dataset.subtotal);
                        selectedIds.push(checkbox.value);
                    }
                });

                totalDisplay.textContent = formatRupiah(total);
                if (selectAll) selectAll.checked = checkboxes.length > 0 && selectedIds.length === checkboxes.length;

                if (selectedIds.length === 0 || !selectedBranchIsOpen) {
                    btnCheckout.classList.add('opacity-50', 'pointer-events-none');
                    btnCheckout.href = '#';
                } else {
                    btnCheckout.classList.remove('opacity-50', 'pointer-events-none');
                    btnCheckout.href = "{{ route('checkout') }}?items=" + selectedIds.join(',');
                }
            }

            function renderItem(row, quantity, subtotal) {
                row.querySelector('.quantity-display').textContent = `${quantity} Porsi`;
                row.querySelector('.item-subtotal').textContent = formatRupiah(subtotal);
                row.querySelector('.item-checkbox').dataset.subtotal = subtotal;
                row.querySelector('[data-cart-action="decrease"]').disabled = quantity <= 1;
                calculateTotal();
            }

            async function changeQuantity(button) {
                const row = button.closest('.cart-item');
                const quantityDisplay = row.querySelector('.quantity-display');
                const previousQuantity = Number(quantityDisplay.textContent.replace(/\D/g, ''));
                const nextQuantity = previousQuantity + (button.dataset.cartAction === 'increase' ? 1 : -1);
                if (nextQuantity < 1) return;

                const price = Number(row.dataset.price);
                const buttons = row.querySelectorAll('.quantity-button');
                buttons.forEach(control => control.disabled = true);
                renderItem(row, nextQuantity, price * nextQuantity);

                try {
                    const response = await fetch(updateUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: new URLSearchParams({ product_id: row.dataset.productId, action: button.dataset.cartAction }),
                    });
                    if (!response.ok) throw new Error('Gagal memperbarui keranjang.');
                    const data = await response.json();
                    renderItem(row, data.quantity, data.subtotal);
                } catch (error) {
                    renderItem(row, previousQuantity, price * previousQuantity);
                    alert('Kuantitas belum dapat diperbarui. Silakan coba lagi.');
                } finally {
                    buttons.forEach(control => {
                        control.disabled = control.dataset.cartAction === 'decrease' && Number(row.querySelector('.quantity-display').textContent.replace(/\D/g, '')) <= 1;
                    });
                }
            }

            selectAll?.addEventListener('change', () => {
                itemCheckboxes().forEach(checkbox => checkbox.checked = selectAll.checked);
                calculateTotal();
            });
            document.addEventListener('change', event => {
                if (event.target.matches('.item-checkbox')) calculateTotal();
            });
            document.querySelectorAll('.quantity-button').forEach(button => {
                button.addEventListener('click', () => changeQuantity(button));
            });

            calculateTotal();
        });
    </script>

</body>
</html>
