<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fav Dimsum - Hangat, Lezat & Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap');
        .font-display { font-family: 'Playfair Display', Georgia, serif; }
        .font-body { font-family: 'DM Sans', sans-serif; }
        .product-card { overflow: hidden; }
        html { scroll-behavior: smooth; }
        @keyframes fade-up { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes gentle-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        @keyframes warm-pulse { 0%, 100% { box-shadow: 0 8px 18px rgba(239, 68, 68, .3); } 50% { box-shadow: 0 10px 28px rgba(251, 191, 36, .55); } }
        @keyframes overlay-shift { 0%, 100% { opacity: .94; } 50% { opacity: .78; } }
        .hero-overlay { animation: overlay-shift 6s ease-in-out infinite; }
        .hero-copy { animation: fade-up .7s ease-out both; }
        .hero-cta { animation: warm-pulse 2.4s ease-in-out infinite; }
        .hero-previews { animation: fade-up .7s .18s ease-out both; }
        .hero-previews > a { transition: transform .3s ease, box-shadow .3s ease; }
        .hero-previews > a:hover { animation: none; transform: translateY(-8px) scale(1.02); box-shadow: 0 18px 32px rgba(0, 0, 0, .35); }
        .hero-previews > a img { transition: transform .4s ease; }
        .hero-previews > a:hover img { transform: scale(1.08); }
        .hero-previews > a:first-child { animation: gentle-float 4.4s ease-in-out .3s infinite; }
        .hero-previews > a:nth-child(2) { animation: gentle-float 4s ease-in-out .8s infinite; }
        .reveal, .reveal-section { opacity: 0; transform: translateY(24px); transition: opacity .65s ease, transform .65s ease; }
        .reveal.is-visible, .reveal-section.is-visible { opacity: 1; transform: translateY(0); }
        .product-card { transition: transform .25s ease, box-shadow .25s ease; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 18px 30px rgba(234, 88, 12, .16); }
        .product-card img { transition: transform .35s ease; }
        .product-card:hover img { transform: scale(1.06); }
        .cart-add-button, .filter-btn, .branch-btn { transition: transform .2s ease, box-shadow .2s ease; }
        .cart-add-button:hover, .filter-btn:hover, .branch-btn:hover { transform: translateY(-2px); }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important; transition-duration: .01ms !important; } }
        @media (max-width: 639px) { .hero-content { min-height: 100svh; padding: 7.5rem 1.25rem 5rem; } .hero-copy, .hero-previews { width: 100%; margin-left: 0; } .hero-copy h1 { font-size: clamp(2.75rem, 13vw, 4rem); line-height: .98; } .hero-previews { margin-top: 2.5rem; } .hero-card { height: 10.5rem; } .hero-card h2 { font-size: 1.45rem; } #menu { padding-top: 4rem; } #halal > div { padding: 1.5rem; } #halalDialog { width: calc(100vw - 2rem); max-height: calc(100svh - 2rem); } .reveal, .reveal-section { opacity: 1; transform: none; } }
    </style>
</head>
<body class="bg-white text-slate-800 font-body antialiased overflow-x-hidden flex flex-col min-h-screen">

    <!-- Navigation Header -->
    <header class="absolute inset-x-0 top-0 z-50 w-full bg-transparent">
        <div class="mx-auto flex min-h-20 max-w-7xl items-center justify-between gap-3 py-3 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3 justify-self-start">
                <img src="{{ asset('images/logofav.jpeg') }}" alt="Logo Fav Dimsum" class="h-10 w-10 rounded-full object-cover ring-2 ring-white/80 shadow-md">
                <span class="font-display text-xl sm:text-2xl font-bold tracking-tight text-white">FavDimsum</span>
            </a>

            <nav class="hidden items-center gap-7 text-sm font-semibold text-white lg:flex">
                <a href="#home" class="relative py-2 after:absolute after:inset-x-0 after:-bottom-0.5 after:h-0.5 after:rounded-full after:bg-amber-400">Beranda</a>
                <a href="#menu" class="py-2 hover:text-amber-300">Menu Utama</a>
                <a href="#halal" class="py-2 hover:text-amber-300">Sertifikat Halal</a>
                <a href="#cabang" class="py-2 hover:text-amber-300">Cabang</a>
            </nav>

            <div class="flex shrink-0 items-center gap-2 sm:gap-4 justify-self-end">
                @guest
                    <a href="{{ route('login') }}" class="hidden text-xs font-bold text-white hover:text-amber-300 sm:block">Masuk</a>
                @endguest

                @auth
                    @if (auth()->user()->isOwner() || auth()->user()->isAdmin())
                        <a href="{{ url('/admin') }}" class="hidden text-xs font-bold text-white hover:text-amber-300 sm:block">Dashboard</a>
                    @else
                        <a href="{{ route('orders.history') }}" class="hidden max-w-28 truncate text-xs font-bold text-white hover:text-amber-300 sm:block">{{ auth()->user()->name }}</a>
                    @endif
                    <button type="button" onclick="if (confirm('Apakah Anda yakin ingin keluar?')) document.getElementById('logout-form').submit()" class="hidden text-xs font-bold text-white hover:text-amber-300 sm:block">Keluar</button>
                    <a href="{{ route('profile.edit') }}" aria-label="Pengaturan Akun" class="hidden rounded-full border border-white/50 bg-white/10 p-2 text-sm text-white backdrop-blur-sm hover:bg-white/20 sm:inline-flex">⚙</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @endauth

                @php($cartCount = collect(session('cart', []))->sum('quantity'))
                <a href="{{ route('cart.index') }}" aria-label="Keranjang Belanja" class="relative inline-flex rounded-full border border-white/50 bg-white/10 p-2 text-lg text-white backdrop-blur-sm hover:bg-white/20">
                    <span aria-hidden="true">🛒</span>
                    @if ($cartCount > 0)
                        <span class="absolute -right-1 -top-1 min-w-5 rounded-full bg-red-500 px-1 text-center text-[10px] font-bold leading-5 text-white">{{ $cartCount }}</span>
                    @endif
                </a>
                <a href="#menu" class="hidden sm:inline-block rounded-full bg-amber-400 px-3 sm:px-5 py-2.5 text-xs font-bold text-stone-900 shadow-lg transition hover:bg-amber-300">Pesan Online</a>
                <details class="relative lg:hidden">
                    <summary aria-label="Buka menu navigasi" class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-full border border-white/50 bg-white/10 text-xl text-white backdrop-blur-sm hover:bg-white/20">☰</summary>
                    <nav class="absolute right-0 top-12 z-50 w-56 overflow-hidden rounded-2xl border border-orange-100 bg-white p-2 text-sm font-semibold text-gray-700 shadow-2xl">
                        <a href="#home" class="block rounded-xl px-4 py-3 hover:bg-orange-50 hover:text-orange-700">Beranda</a>
                        <a href="#menu" class="block rounded-xl px-4 py-3 hover:bg-orange-50 hover:text-orange-700">Menu Utama</a>
                        <a href="#halal" class="block rounded-xl px-4 py-3 hover:bg-orange-50 hover:text-orange-700">Sertifikat Halal</a>
                        <a href="#cabang" class="block rounded-xl px-4 py-3 hover:bg-orange-50 hover:text-orange-700">Cabang</a>
                        <a href="{{ route('profile.edit') }}" class="mt-1 block rounded-xl bg-orange-600 px-4 py-3 text-center text-white hover:bg-orange-700">Masuk / Akun</a>
                    </nav>
                </details>
            </div>
        </div>
    </header>

    {{-- Flash notification is deliberately outside the header so it never affects navbar layout. --}}
    @if (session('success'))
        <div id="flash-toast-success" role="status" aria-live="polite" class="fixed bottom-5 right-5 z-[9999] flex max-w-[calc(100vw-2.5rem)] items-center gap-3 rounded-xl bg-orange-500 px-5 py-3 text-white shadow-2xl sm:max-w-md">
            <span class="text-lg" aria-hidden="true">✅</span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('flash-toast-success')?.remove()" aria-label="Tutup notifikasi" class="ml-2 font-bold opacity-75 hover:opacity-100">✕</button>
        </div>
    @endif

    @if (session('error'))
        <div id="flash-toast-error" role="alert" aria-live="assertive" class="fixed bottom-5 right-5 z-[9999] flex max-w-[calc(100vw-2.5rem)] items-center gap-3 rounded-xl bg-orange-600 px-5 py-3 text-white shadow-2xl sm:max-w-md">
            <span class="text-lg" aria-hidden="true">⚠️</span>
            <span class="text-sm font-medium">{{ session('error') }}</span>
            <button type="button" onclick="document.getElementById('flash-toast-error')?.remove()" aria-label="Tutup notifikasi" class="ml-2 font-bold opacity-75 hover:opacity-100">✕</button>
        </div>
    @endif

    <script>
        setTimeout(() => {
            document.getElementById('flash-toast-success')?.remove();
            document.getElementById('flash-toast-error')?.remove();
        }, 4000);
    </script>

    <main class="flex-grow">
        <!-- Hero Section -->
        <section id="home" class="relative min-h-screen w-full overflow-hidden bg-cover bg-[position:right_center] bg-no-repeat" style="background-image: url('{{ asset('images/landing.jpg') }}')">
            <div class="hero-overlay pointer-events-none absolute inset-y-0 left-0 w-full bg-gradient-to-r from-black/80 via-black/40 to-transparent md:w-2/3"></div>

            <div class="hero-content relative z-10 flex min-h-screen flex-col justify-center px-8 py-28 text-left text-white">
                <div class="hero-copy ml-8 w-full max-w-xl md:ml-16">
                    <p class="text-[10px] font-bold uppercase tracking-[0.28em] text-amber-300">Segar, Hangat, dan Dibuat Setiap Hari</p>
                    <h1 class="font-display mt-5 text-5xl font-bold leading-[0.92] sm:text-6xl lg:text-7xl">Kehangatan Dimsum<br>Autentik<br>Dalam Setiap Gigitan</h1>
                    <p class="mt-5 max-w-lg text-sm leading-relaxed text-white/90 sm:text-base">Dibuat dari bahan pilihan segar dengan resep spesial khas Fav Dimsum. Nikmati kelezatan kukus dan goreng bersama keluarga.</p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="#menu" class="hero-cta rounded-full bg-red-500 px-6 py-3 text-sm font-bold text-white shadow-lg">Pesan Sekarang</a>
                        <a href="#menu" class="rounded-full border border-amber-400 px-6 py-3 text-sm font-bold text-amber-200">Lihat Menu</a>
                    </div>
                </div>

                <div class="hero-previews mt-12 ml-8 grid w-full max-w-3xl gap-4 md:ml-16 md:grid-cols-2">
                    @foreach($heroProducts as $heroProduct)
                        <a href="#menu" class="hero-card relative h-44 overflow-hidden rounded-2xl border border-white/70 text-left shadow-xl">
                            <img src="{{ $heroProduct->image ? asset('storage/' . ltrim($heroProduct->image, '/')) : asset('images/' . ($loop->first ? 'chilioil.jpeg' : 'ayam.jpeg')) }}" alt="{{ $heroProduct->name }}" class="h-full w-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-5">
                                <p class="text-[9px] font-bold uppercase tracking-wide text-amber-300">{{ $loop->first ? "🔥 Favorit Pelanggan" : "Pilihan Renyah" }}</p>
                                <h2 class="font-display mt-1 text-2xl font-bold text-white">{{ $heroProduct->name }}</h2>
                                <p class="text-xs text-white/80">Cita rasa premium khas Fav Dimsum.</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="pointer-events-none absolute inset-x-0 bottom-0 z-20 h-40 bg-gradient-to-b from-transparent to-white"></div>
        </section>
        <!-- Menu List Section -->
        <section id="menu" class="reveal-section max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-12">
            <div class="text-center mb-10">
                <p class="text-[11px] font-bold uppercase tracking-[.18em] text-orange-600">Pilih Berdasarkan Kategori</p>
                <h3 class="font-display mt-2 text-3xl sm:text-4xl font-bold text-gray-900">Pilih Kategori Favoritmu</h3>
                <div class="mx-auto mt-3 h-0.5 w-10 bg-orange-500"></div>
                <p class="mx-auto mt-4 max-w-xl text-sm text-gray-500">Jelajahi aneka dimsum segar dan minuman pilihan untuk melengkapi momen santaimu.</p>
            </div>

            <div class="flex flex-wrap justify-center gap-2 mb-10">
                <button data-filter="all" class="filter-btn bg-orange-600 text-white text-xs font-semibold px-4 py-2 rounded-full cursor-pointer shadow hover:shadow-lg">
                    Semua
                </button>
                @foreach($categories as $category)
                    @if(!in_array(Str::lower($category->name), ["topping & sauce", "dimsum"]))
                    <button data-filter="{{ Str::lower($category->name) }}" class="filter-btn bg-gray-100 hover:bg-orange-50 hover:text-orange-600 text-gray-700 text-xs font-semibold px-4 py-2 rounded-full cursor-pointer">
                        {{ $category->name }}
                    </button>
                    @endif
                @endforeach
            </div>

            <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    @if(!in_array(Str::lower($product->name), ["dimsum mentai (4 pcs)", "extra chilli oil", "extra chili oil"]))
                    <div data-category="{{ Str::lower(Str::lower($product->name) === "dimsum original (4 pcs)" ? "Dimsum Original" : ($product->category->name ?? "")) }}" class="product-item product-card reveal bg-white rounded-2xl p-4 shadow-sm hover:shadow-xl border border-orange-100 flex flex-col justify-between">
                        <div>
                            <div class="w-full h-44 bg-orange-50 rounded-xl overflow-hidden mb-4 relative">
                                <img src="{{ asset('images/' . match (true) { Str::contains(Str::lower($product->name), 'udang') => 'udang.jpeg', Str::contains(Str::lower($product->name), 'cumi') => 'cumi.jpeg', Str::contains(Str::lower($product->name), 'ayam') => 'ayam.jpeg', Str::contains(Str::lower($product->name), 'beef') => 'beef.jpg', Str::contains(Str::lower($product->name), 'bakar (isi 3 pcs)') => 'bakar3.jpg', Str::contains(Str::lower($product->name), 'bakar (isi 4 pcs)') => 'bakar4.jpeg', Str::contains(Str::lower($product->name), 'bakar (isi 6 pcs)') => 'bakar6.jpg', Str::contains(Str::lower($product->name), 'bakar (isi 8 pcs)') => 'bakar8.jpeg', Str::contains(Str::lower($product->name), 'goreng (isi 3 pcs)') => 'goreng3.jpg', Str::contains(Str::lower($product->name), 'goreng (isi 4 pcs)') => 'goreng4.jpeg', Str::contains(Str::lower($product->name), 'goreng (isi 6 pcs)') => 'goreng6.jpeg', Str::contains(Str::lower($product->name), 'goreng (isi 8 pcs)') => 'goreng8.jpg', Str::contains(Str::lower($product->name), 'original') => 'oroginal.jpeg', Str::contains(Str::lower($product->name), 'mentai (isi 3 pcs)') => 'mentai3.jpeg', Str::contains(Str::lower($product->name), 'mentai (isi 6 pcs)') => 'mentai6.jpeg', Str::contains(Str::lower($product->name), 'mentai (isi 8 pcs)') => 'mentai8.jpeg', Str::contains(Str::lower($product->name), 'mentai') => 'mentai.jpeg', Str::contains(Str::lower($product->name), ['chili', 'chilli']) => 'cilioli.jpeg', Str::contains(Str::lower($product->name), 'tartar (isi 3 pcs)') => 'tartar3.jpg', Str::contains(Str::lower($product->name), 'tartar (isi 4 pcs)') => 'tartar4.jpeg', Str::contains(Str::lower($product->name), 'tartar') => 'tartar.jpeg', Str::contains(Str::lower($product->name), 'mix') => 'mixplater.jpeg', Str::contains(Str::lower($product->name), 'bolognese') => 'bolognese.jpeg', Str::contains(Str::lower($product->name), 'keju') => 'keju.jpeg', default => Str::lower($product->name) . '.jpeg', }) }}"
                                     onerror="if (this.src.endsWith('.jpeg')) { this.src='{{ asset('images/' . Str::lower($product->name) . '.jpg') }}'; } else { this.src='{{ asset('images/favxdimsum.jpeg') }}'; }"
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover">
                                
                                <span class="absolute top-3 left-3 bg-orange-100 text-orange-800 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                                    {{ Str::lower($product->name) === "dimsum original (4 pcs)" ? "Dimsum Original" : ($product->category->name ?? "Dimsum") }}
                                </span>
                            </div>
                            <h4 class="font-bold text-gray-900 text-base mb-1">{{ $product->name }}</h4>
                            <p class="text-xs text-gray-500 mb-3 line-clamp-2">Cita rasa premium khas Fav Dimsum.</p>
                        </div>

                        <div class="pt-3 border-t flex items-center justify-between mt-2">
                            <span class="font-black text-orange-600 text-base">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </span>

                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="branch_id" class="selected-branch-input" value="">
                                <button type="submit" class="cart-add-button bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold px-4 py-2 rounded-full shadow hover:shadow-lg flex items-center gap-1">
                                    <span>+</span> Keranjang
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </section>

        <!-- SECTION SERTIFIKAT HALAL RESMI -->
        <section id="halal" class="reveal-section max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-gradient-to-r from-orange-600 to-orange-500 rounded-3xl p-8 sm:p-12 text-white shadow-2xl relative overflow-hidden border border-orange-700/50">
                <div class="absolute -right-16 -bottom-16 w-80 h-80 bg-orange-500/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                    <div class="lg:col-span-7">
                        <span class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-orange-200 mb-4 bg-orange-950/80 border border-orange-500/50 px-4 py-1.5 rounded-full">
                            <span>🛡️</span> SERTIFIKASI RESMI BPJPH
                        </span>
                        
                        <h3 class="text-3xl sm:text-4xl font-extrabold text-white mb-4 leading-tight">
                            Terjamin 100% Halal, Aman & Thayyib
                        </h3>
                        
                        <p class="text-orange-50 text-sm sm:text-base mb-6 leading-relaxed">
                            Seluruh olahan bahan baku, proses produksi, hingga penyajian Fav Dimsum telah secara resmi memenuhi ketentuan perundang-undangan Jaminan Produk Halal Republik Indonesia.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-orange-950/60 backdrop-blur-md p-4 sm:p-5 rounded-2xl border border-orange-500/40 text-xs">
                            <div>
                                <p class="text-orange-300 font-medium mb-1">Nomor Sertifikat Halal:</p>
                                <p class="text-white font-mono font-bold text-sm tracking-wide">ID33410030239281025</p>
                            </div>
                            <div>
                                <p class="text-orange-300 font-medium mb-1">Diterbitkan Oleh:</p>
                                <p class="text-white font-semibold">BPJPH Kementerian Agama RI</p>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5 flex flex-col items-center justify-center">
                        <button type="button" onclick="document.getElementById('halalDialog').showModal()" class="group relative cursor-pointer bg-white p-3 rounded-2xl shadow-xl max-w-xs text-left w-full border-0 outline-none">
                            <div class="overflow-hidden rounded-xl h-64 bg-orange-50 relative">
                                <img src="{{ asset('images/halal.jpeg') }}" 
                                     onerror="if(this.src.endsWith('.jpeg')){this.src='{{ asset('images/halal.jpg') }}';}"
                                     alt="Sertifikat Halal Fav Dimsum" 
                                     class="w-full h-full object-cover object-top block">
                                <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 flex items-center justify-center pointer-events-none">
                                    <span class="bg-white/90 backdrop-blur-sm text-gray-900 text-xs font-bold px-4 py-2 rounded-full shadow">
                                        🔍 Klik untuk Perbesar
                                    </span>
                                </div>
                            </div>
                            <p class="text-center text-[11px] font-bold text-gray-600 mt-2.5">
                                Dokumen Sertifikat Halal Resmi
                            </p>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION CABANG & MAPS TERDEKAT -->
        <section id="cabang" class="reveal-section max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xl border border-orange-100">
                <div class="text-center max-w-2xl mx-auto mb-8">
                    <span class="inline-block text-xs font-bold uppercase tracking-widest text-orange-600 mb-2 bg-orange-50 px-3 py-1 rounded-full border border-orange-100">
                        📍 LOKASI CABANG FAV DIMSUM
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900">
                        Pilih Cabang Terdekat Dari Lokasimu
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 mt-2">
                        Kunjungi gerai resmi Fav Dimsum terdekat untuk menikmati dimsum hangat atau mengambil pesananmu.
                    </p>
                </div>

                <div class="flex flex-wrap justify-center gap-2 sm:gap-3 mb-8">
                    <button onclick="switchBranch('karangmoncol')" id="btn-karangmoncol" 
                        class="branch-btn bg-orange-600 text-white text-xs sm:text-sm font-bold px-5 py-2.5 rounded-full shadow flex items-center gap-1.5 cursor-pointer">
                        <span>🏠</span> Cabang Karangmoncol
                    </button>
                    <button onclick="switchBranch('pengadegan')" id="btn-pengadegan" 
                        class="branch-btn bg-gray-100 text-gray-700 hover:bg-orange-50 hover:text-orange-600 text-xs sm:text-sm font-bold px-5 py-2.5 rounded-full border border-orange-200 flex items-center gap-1.5 cursor-pointer">
                        <span>🏠</span> Cabang Pengadegan
                    </button>
                    <button onclick="switchBranch('bobotsari')" id="btn-bobotsari" 
                        class="branch-btn bg-gray-100 text-gray-700 hover:bg-orange-50 hover:text-orange-600 text-xs sm:text-sm font-bold px-5 py-2.5 rounded-full border border-orange-200 flex items-center gap-1.5 cursor-pointer">
                        <span>🏠</span> Cabang Bobotsari
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                    <div class="lg:col-span-5 bg-orange-50 p-6 sm:p-8 rounded-2xl border border-orange-100 flex flex-col justify-between">
                        <div>
                            <div id="branch-status" class="flex items-center gap-2 mb-3" role="status" aria-live="polite">
                                <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                                <span class="text-xs font-bold text-orange-700 uppercase tracking-wide">🟢 GERAI BUKA</span>
                            </div>
                            
                            <h4 id="branch-title" class="text-xl font-black text-gray-900 mb-2">
                                Fav Dimsum - Karangmoncol
                            </h4>
                            
                            <div class="space-y-3 my-4 text-xs sm:text-sm text-gray-600 leading-relaxed">
                                <p class="flex items-start gap-2.5">
                                    <span class="text-base">📍</span>
                                    <span id="branch-address">
                                        Jl. Kopral Yasan, Gerbolang, Pepedan, Kec. Karangmoncol, Kabupaten Purbalingga, Jawa Tengah 53355
                                    </span>
                                </p>
                                <p class="flex items-center gap-2.5">
                                    <span class="text-base">🏷️</span>
                                    <span>Kode Plus: <strong id="branch-code" class="text-gray-900 font-mono">MFF6+RG9</strong></span>
                                </p>
                                <p class="flex items-center gap-2.5">
                                    <span class="text-base">⏰</span>
                                    <span>Jam Operasional: <strong id="branch-hours" class="text-gray-900 font-bold">10.00 - 21.00 WIB</strong></span>
                                </p>
                            </div>
                        </div>

                        <a id="branch-map-link" 
                           href="https://maps.google.com/?q=MFF6%2BRG9+Purbalingga" 
                           target="_blank" 
                           class="mt-6 w-full bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs sm:text-sm px-6 py-3 rounded-xl shadow-md flex items-center justify-center gap-2 text-center">
                            <span>🗺️</span> Buka Petunjuk Arah di Google Maps &rarr;
                        </a>
                        <a id="branch-order-link" href="#menu" class="mt-3 w-full bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs sm:text-sm px-6 py-3 rounded-xl shadow-md flex items-center justify-center gap-2 text-center">
                            Pesan Sekarang
                        </a>
                        <p id="branch-order-note" class="mt-2 text-center text-xs text-gray-500"></p>
                    </div>

                    <div class="lg:col-span-7 rounded-2xl overflow-hidden border border-orange-100 shadow-inner min-h-[320px]">
                        <iframe id="branch-iframe" 
                            src="https://maps.google.com/maps?q=MFF6%2BRG9+Purbalingga&t=&z=16&ie=UTF8&iwloc=&output=embed" 
                            class="w-full h-full min-h-[320px] border-0" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER SECTION -->
    <footer class="bg-orange-950 text-white border-t border-orange-500 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12">
                <div class="md:col-span-5 space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logofav.jpeg') }}" alt="Logo Fav Dimsum" class="w-10 h-10 object-cover rounded-full shadow-md">
                        <div>
                             <h3 class="text-lg font-bold text-white leading-tight">Fav Dimsum</h3>
                             <p class="text-xs text-orange-500 font-medium">Hangat & Segar Setiap Hari</p>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-orange-100 leading-relaxed max-w-sm">
                        Menyajikan kehangatan dan kelezatan dimsum ayam premium dengan racikan sambal minyak cabai khas. Dibuat segar setiap hari untuk menemani setiap momen berhargamu.
                    </p>
                    <div class="pt-2 flex items-center gap-3 text-xs text-orange-300 font-semibold bg-orange-950/60 w-fit px-3 py-1.5 rounded-full border border-orange-800/50">
                        <span>🛡️</span> Sertifikat Halal: ID33410030239281025
                    </div>
                    <div class="pt-1 flex flex-wrap gap-2">
                        <a href="https://www.instagram.com/fav.dim.sum?stkn=Mm1hY2syMzZtYmRq" target="_blank" rel="noopener noreferrer" aria-label="Instagram Fav Dimsum" class="inline-flex items-center gap-2 rounded-full border border-orange-800 bg-orange-900/50 px-3 py-2 text-xs font-semibold text-orange-100 transition hover:border-orange-400 hover:text-orange-300">◎ Instagram</a>
                        <a href="https://www.tiktok.com/%40jeru.lokal.x.fav?_r=1&amp;_t=ZS-99sufJLF1ko" target="_blank" rel="noopener noreferrer" aria-label="TikTok Jeru Lokal x Fav" class="inline-flex items-center gap-2 rounded-full border border-orange-800 bg-orange-900/50 px-3 py-2 text-xs font-semibold text-orange-100 transition hover:border-orange-400 hover:text-orange-300">♪ TikTok</a>
                    </div>
                </div>

                <div class="md:col-span-3 space-y-3">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-orange-400">Navigasi</h4>
                    <ul class="space-y-2 text-xs sm:text-sm text-orange-100">
                        <li><a href="#menu" class="hover:text-orange-400">Katalog Menu</a></li>
                        <li><a href="#halal" class="hover:text-orange-400">Sertifikasi Halal</a></li>
                        <li><a href="#cabang" class="hover:text-orange-400">Lokasi Gerai</a></li>
                        <li><a href="{{ route('orders.history') }}" class="hover:text-orange-400">Cek Riwayat Pesanan</a></li>
                    </ul>
                </div>

                <div class="md:col-span-4 space-y-3">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-orange-400">Jam Operasional Gerai</h4>
                    <ul class="space-y-2 text-xs sm:text-sm text-orange-100">
                        <li class="flex justify-between border-b border-orange-900 pb-1.5">
                            <span>Gerai Karangmoncol</span>
                            <strong class="text-white">10.00 - 21.00 WIB</strong>
                        </li>
                        <li class="flex justify-between border-b border-orange-900 pb-1.5">
                            <span>Gerai Pengadegan</span>
                            <strong class="text-white">10.00 - 17.00 WIB</strong>
                        </li>
                        <li class="flex justify-between border-b border-orange-900 pb-1.5">
                            <span>Gerai Bobotsari</span>
                            <strong class="text-white">14.00 - 21.00 WIB</strong>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-orange-900/80 text-center text-xs text-orange-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p>&copy; {{ date('Y') }} Fav Dimsum Indonesia. Hak cipta dilindungi.</p>
                <p class="text-orange-300">Dibuat dengan ❤️ untuk Pencinta Dimsum Hangat & Segar.</p>
            </div>
        </div>
    </footer>

    <!-- Dialog Foto Sertifikat Halal -->
<dialog id="halalDialog" class="p-0 rounded-2xl max-w-xl w-full backdrop:bg-black/80 shadow-2xl border-0 overflow-hidden m-auto">
        <div class="bg-orange-700 text-white p-4 flex items-center justify-between">
            <span class="font-bold text-sm">📜 Sertifikat Halal Resmi - Fav Dimsum</span>
            <form method="dialog">
                <button class="w-8 h-8 rounded-full bg-orange-800 hover:bg-orange-500 font-bold text-white flex items-center justify-center">✕</button>
            </form>
        </div>

        <div class="p-4 bg-orange-50 flex justify-center items-center">
            <img src="{{ asset('images/halal.jpeg') }}"
                 onerror="if(this.src.endsWith('.jpeg')){this.src='{{ asset('images/halal.jpg') }}';}"
                 alt="Sertifikat Halal"
                 class="w-full h-auto max-h-[70vh] object-contain rounded-lg">
        </div>

        <div class="p-3 bg-white text-center border-t text-xs text-gray-500 font-medium">
            Nomor Sertifikat: <strong class="text-gray-800 font-mono">ID33410030239281025</strong>
        </div>
    </dialog>
<script>
        document.addEventListener('DOMContentLoaded', function () {
            // Filter logic
            const filterBtns = document.querySelectorAll('.filter-btn');
            const productItems = document.querySelectorAll('.product-item');
            const revealElements = document.querySelectorAll(".reveal, .reveal-section");
            const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
            if ("IntersectionObserver" in window && !reduceMotion) {
                const revealObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add("is-visible");
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12 });
                revealElements.forEach(element => revealObserver.observe(element));
            } else {
                revealElements.forEach(element => element.classList.add("is-visible"));
            }


            filterBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    const filter = this.getAttribute('data-filter');

                    filterBtns.forEach(b => {
                        b.className = 'filter-btn bg-gray-100 hover:bg-orange-50 hover:text-orange-600 text-gray-700 text-xs font-semibold px-4 py-2 rounded-full cursor-pointer';
                    });
                    this.className = 'filter-btn bg-orange-600 text-white text-xs font-semibold px-4 py-2 rounded-full cursor-pointer shadow hover:shadow-lg';

                    productItems.forEach(item => {
                        const itemCategory = item.getAttribute('data-category');
                        if (filter === 'all' || itemCategory === filter) {
                            item.style.display = 'flex';
                            item.classList.remove('is-visible');
                            requestAnimationFrame(() => item.classList.add('is-visible'));
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
        });

        const branchOperationalData = @json($branchOperationalData);

        function switchBranch(branchKey) {
            const branches = {
                'karangmoncol': {
                    title: 'Fav Dimsum - Karangmoncol',
                    address: 'Jl. Kopral Yasan, Gerbolang, Pepedan, Kec. Karangmoncol, Kabupaten Purbalingga, Jawa Tengah 53355',
                    code: 'MFF6+RG9',
                    hours: '10.00 - 21.00 WIB',
                    mapQuery: 'MFF6%2BRG9+Purbalingga'
                },
                'pengadegan': {
                    title: 'Fav Dimsum - Pengadegan',
                    address: 'Area Sawah/Kebun, Pengadegan, Kec. Pengadegan, Kabupaten Purbalingga, Jawa Tengah 53393',
                    code: 'JGFM+Q8',
                    hours: '10.00 - 17.00 WIB',
                    mapQuery: 'JGFM%2BQ8+Purbalingga'
                },
                'bobotsari': {
                    title: 'Fav Dimsum - Bobotsari',
                    address: 'Jl. Kolonel Sugiri, Dusun 2, Bobotsari, Kec. Bobotsari, Kabupaten Purbalingga, Jawa Tengah 53353',
                    code: 'PG4W+5H',
                    hours: '14.00 - 21.00 WIB',
                    mapQuery: 'PG4W%2B5H+Purbalingga'
                }
            };

            const data = branches[branchKey];
            const operational = branchOperationalData[branchKey];
            if (!data || !operational) return;

            document.getElementById('branch-title').innerText = data.title;
            document.getElementById('branch-address').innerText = data.address;
            document.getElementById('branch-code').innerText = data.code;
            document.getElementById('branch-hours').innerText = operational.hours;

            const status = document.getElementById('branch-status');
            const orderLink = document.getElementById('branch-order-link');
            const orderNote = document.getElementById('branch-order-note');
            const isOpen = operational.isOpen;

            status.innerHTML = isOpen
                ? '<span class="w-3 h-3 rounded-full bg-orange-500"></span><span class="text-xs font-bold text-orange-700 uppercase tracking-wide">🟢 GERAI BUKA</span>'
                : '<span class="w-3 h-3 rounded-full bg-orange-500"></span><span class="text-xs font-bold text-orange-700 uppercase tracking-wide">🔴 GERAI TUTUP</span>';

            orderLink.className = isOpen
                ? 'mt-3 w-full bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs sm:text-sm px-6 py-3 rounded-xl shadow-md flex items-center justify-center gap-2 text-center'
                : 'mt-3 w-full bg-gray-200 text-gray-500 font-bold text-xs sm:text-sm px-6 py-3 rounded-xl cursor-not-allowed flex items-center justify-center gap-2 text-center pointer-events-none';
            orderLink.title = isOpen ? '' : 'Gerai Sedang Tutup';
            orderLink.setAttribute('aria-disabled', String(!isOpen));
            orderLink.textContent = isOpen ? 'Pesan Sekarang' : 'Gerai Sedang Tutup';
            orderNote.textContent = isOpen ? '' : 'Gerai Sedang Tutup — pemesanan tersedia kembali pada jam operasional.';

            document.querySelectorAll('.selected-branch-input').forEach(input => input.value = operational.id);
            document.querySelectorAll('.cart-add-button').forEach(button => {
                button.disabled = !isOpen;
                button.title = isOpen ? '' : 'Gerai Sedang Tutup';
                button.classList.toggle('opacity-50', !isOpen);
                button.classList.toggle('cursor-not-allowed', !isOpen);
            });
            document.getElementById('branch-map-link').href = `https://maps.google.com/?q=${data.mapQuery}`;
            document.getElementById('branch-iframe').src = `https://maps.google.com/maps?q=${data.mapQuery}&t=&z=16&ie=UTF8&iwloc=&output=embed`;

            document.querySelectorAll('.branch-btn').forEach(btn => {
                btn.className = 'branch-btn bg-gray-100 text-gray-700 hover:bg-orange-50 hover:text-orange-600 text-xs sm:text-sm font-bold px-5 py-2.5 rounded-full border border-orange-200 flex items-center gap-1.5 cursor-pointer';
            });
            const activeBtn = document.getElementById(`btn-${branchKey}`);
            if (activeBtn) {
                activeBtn.className = 'branch-btn bg-orange-600 text-white text-xs sm:text-sm font-bold px-5 py-2.5 rounded-full shadow flex items-center gap-1.5 cursor-pointer';
            }
        }

        switchBranch('karangmoncol');
    </script>

</body>
</html>
