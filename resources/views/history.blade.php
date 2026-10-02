<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Fav Dimsum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-orange-50 text-gray-800 font-sans min-h-screen">

    <!-- Header Navigation -->
    <header class="bg-white border-b border-orange-100 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 py-3 sm:py-4 flex items-center justify-between gap-3">
            <a href="/" class="flex items-center gap-2">
                <span class="text-xl font-black text-orange-600 tracking-tight">Fav Dimsum</span>
            </a>
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('cart.index') }}" class="px-3 sm:px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                    🛒 Keranjang
                </a>
                <a href="/" class="px-3 sm:px-4 py-2 text-xs font-bold text-white whitespace-nowrap bg-orange-600 hover:bg-orange-700 rounded-xl transition shadow-md">
                    + Pesan Lagi
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-black text-gray-800">Riwayat Pesanan Saya</h1>
            <p class="text-xs text-gray-500 mt-1">Daftar seluruh transaksi pemesanan dimsum yang telah dibuat.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-orange-50 border border-orange-200 text-orange-700 text-xs font-medium rounded-2xl flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        @forelse($transactions as $transaction)
            @php
                $status = $transaction->statusDetails();
                $steps = [
                    'pending' => 'Berhasil Dibuat',
                    'processing' => 'Disiapkan',
                    'ready' => 'Siap Diambil',
                    'completed' => 'Selesai',
                ];
                $currentStep = array_search($transaction->status, array_keys($steps), true);
                $currentStep = $currentStep === false ? 3 : $currentStep;
            @endphp
            <div class="bg-white rounded-3xl border border-orange-100 shadow-sm hover:shadow-md transition mb-6 overflow-hidden">
                
                <!-- Card Header -->
                <div class="p-4 sm:p-5 bg-gray-50/50 border-b border-gray-100 flex flex-wrap justify-between items-center gap-2">
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 block uppercase tracking-wider">Nomor Faktur</span>
                        <span class="text-xs font-extrabold text-gray-800 font-mono">{{ $transaction->invoice_number }}</span>
                        <span class="text-[11px] text-gray-400 block mt-0.5">
                            📅 {{ $transaction->created_at->translatedFormat('d M Y, H:i') }} WIB
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-orange-50 text-orange-700 border border-orange-200">
                            {{ $transaction->payment_method ?? 'Tunai' }}
                        </span>
                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg border {{ $status['classes'] }}">
                            {{ $status['label'] }}
                        </span>
                    </div>
                </div>

                <!-- Card Body (Detail Cabang & Info Pemesan) -->
                <div class="p-4 sm:p-5 border-b border-gray-100 text-xs space-y-2 bg-orange-50/20">
                    <div class="flex items-start text-gray-700 font-semibold">
                        <span class="w-24 shrink-0 text-gray-400 font-normal">Cabang Gerai:</span>
                        <span class="text-orange-600 font-bold">📍 {{ $transaction->branch->name ?? 'Pusat' }}</span>
                    </div>
                    @if($transaction->customer_name)
                        <div class="flex items-center text-gray-700">
                            <span class="w-24 text-gray-400 font-normal">Pemesan:</span>
                            <span class="font-medium">{{ $transaction->customer_name }} ({{ $transaction->customer_phone ?? '-' }})</span>
                        </div>
                    @endif
                    @if($transaction->customer_address)
                        <div class="flex items-start text-gray-700">
                            <span class="w-24 text-gray-400 font-normal shrink-0">Alamat:</span>
                            <span class="font-medium text-gray-600 line-clamp-1">{{ $transaction->customer_address }}</span>
                        </div>
                    @endif
                </div>

                <!-- Pelacakan status pesanan: pelanggan hanya melihat progresnya. -->
                <div class="p-4 sm:p-5 border-b border-gray-100 bg-white">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-4">Status Pesanan</p>
                    <div class="grid grid-cols-4 gap-1">
                        @foreach($steps as $stepKey => $stepLabel)
                            @php
                                $stepIndex = array_search($stepKey, array_keys($steps), true);
                                $isReached = $stepIndex <= $currentStep;
                                $isCurrent = $stepIndex === $currentStep;
                            @endphp
                            <div class="relative flex flex-col items-center text-center {{ !$loop->last ? 'after:absolute after:left-1/2 after:top-3 after:h-0.5 after:w-full after:bg-gray-200' : '' }} {{ $isReached && !$loop->last ? 'after:!bg-orange-500' : '' }}">
                                <span class="relative z-10 flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-bold {{ $isReached ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-500' }} {{ $isCurrent ? 'ring-4 ring-orange-100' : '' }}">
                                    {{ $isReached ? '✓' : $stepIndex + 1 }}
                                </span>
                                <span class="mt-2 max-w-[82px] text-[9px] leading-tight {{ $isCurrent ? 'font-bold text-orange-700' : ($isReached ? 'font-medium text-gray-700' : 'text-gray-400') }}">{{ $stepLabel }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Rincian Item Pesanan -->
                <div class="p-4 sm:p-5 space-y-3">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Rincian Menu</p>
                    @forelse($transaction->details as $detail)
                        <div class="flex justify-between items-start gap-3 text-xs">
                            <div class="min-w-0">
                                <p class="font-bold text-gray-800 break-words">{{ $detail->product->name ?? 'Menu Dimsum' }}</p>
                                <p class="text-[11px] text-gray-400">
                                    {{ $detail->qty ?? $detail->quantity }} x Rp {{ number_format($detail->price, 0, ',', '.') }}
                                </p>
                            </div>
                            <span class="shrink-0 text-right font-bold text-gray-700">
                                Rp {{ number_format($detail->subtotal ?? ($detail->price * ($detail->qty ?? $detail->quantity)), 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic">Detail menu tidak tersedia.</p>
                    @endforelse
                </div>

                <!-- Card Footer (Total Pembayaran) -->
                <div class="p-4 sm:p-5 bg-orange-50 border-t border-orange-100 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-600">Total Pembayaran</span>
                    <span class="text-base font-black text-orange-600">
                        Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                    </span>
                </div>

            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm">
                <div class="text-4xl mb-3">🥟</div>
                <h3 class="text-base font-bold text-gray-700">Belum Ada Riwayat Pesanan</h3>
                <p class="text-xs text-gray-400 mt-1 mb-5">Kamu belum pernah melakukan pemesanan dimsum.</p>
                <a href="/" class="inline-block bg-orange-600 hover:bg-orange-700 text-white font-bold px-6 py-2.5 rounded-xl text-xs transition">
                    Pesan Dimsum Sekarang
                </a>
            </div>
        @endforelse
    </main>

</body>
</html>
