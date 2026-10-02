<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online - Katalog Produk</title>
    <style>
        :root { --bs-primary: #EA580C; --bs-primary-rgb: 220, 38, 38; --bs-danger: #C2410C; --bs-danger-rgb: 185, 28, 28; --bs-warning: #F97316; --bs-warning-rgb: 245, 158, 11; }
        .navbar.bg-dark { background-color: #C2410C !important; }
        .btn-primary { --bs-btn-bg: #EA580C; --bs-btn-border-color: #EA580C; --bs-btn-hover-bg: #C2410C; --bs-btn-hover-border-color: #C2410C; }
        .btn-outline-primary { --bs-btn-color: #EA580C; --bs-btn-border-color: #EA580C; --bs-btn-hover-bg: #EA580C; --bs-btn-hover-border-color: #EA580C; }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">Toko Online</a>
        <div class="d-flex align-items-center">
            @auth
                <a href="{{ route('cart.index') }}" class="btn btn-outline-light me-2 position-relative">
                    Keranjang
                    @php $cartCount = count(session('cart', [])); @endphp
                    @if($cartCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
            @endauth
        </div>
    </div>
</nav>

<!-- Konten Utama -->
<div class="container py-5">
    <h2 class="mb-4">Katalog Produk</h2>

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <!-- Grid Produk -->
    <div class="row g-4">
        @forelse($products as $product)
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted small mb-2">Kategori: {{ $product->category->name ?? '-' }}</p>
                        <h6 class="text-primary fw-bold mb-3">Rp {{ number_format($product->price, 0, ',', '.') }}</h6>
                        
                        <div class="mt-auto">
                            @auth
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">Jumlah</span>
                                        <input type="number" name="quantity" value="1" min="1" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">+ Tambah Keranjang</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">Masuk untuk Memesan</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center">Belum ada produk yang tersedia.</div>
            </div>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>