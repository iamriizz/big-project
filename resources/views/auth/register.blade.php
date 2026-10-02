<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Fav Dimsum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-orange-50 px-4 py-8 flex items-center justify-center">
    <div class="w-full max-w-md bg-white p-6 sm:p-8 rounded-2xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-orange-600">Daftar Akun Baru</h2>
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-600">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Email</label>
                <input type="email" name="email" required class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-600">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Kata Sandi</label>
                <input type="password" name="password" required class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-600">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" required class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-600">
            </div>
            <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded font-bold hover:bg-orange-700">Daftar</button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-600">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-orange-600 font-bold">Masuk</a>
        </p>
    </div>
</body>
</html>