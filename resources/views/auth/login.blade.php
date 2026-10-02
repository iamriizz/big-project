<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Fav Dimsum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-orange-50 px-4 py-8 flex items-center justify-center">
    <div class="w-full max-w-md bg-white p-6 sm:p-8 rounded-2xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-orange-600">Masuk</h2>
        @if ($errors->any())
    <div class="bg-orange-100 text-orange-600 p-3 rounded mb-4 text-sm">
        {{ $errors->first() }}
    </div>
@endif
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Email</label>
                <input type="email" name="email" required class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-600">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Kata Sandi</label>
                <div class="relative"><input id="password" type="password" name="password" required class="w-full p-2 pr-11 border rounded focus:outline-none focus:ring-2 focus:ring-orange-600"><button id="toggle-password" type="button" aria-label="Tampilkan kata sandi" aria-pressed="false" class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-lg text-gray-500 hover:text-orange-600 focus:outline-none focus:text-orange-600"><svg id="eye-open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12S5.25 5.25 12 5.25 21.75 12 21.75 12 18.75 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.75"/></svg><svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 5.45A9.7 9.7 0 0 1 12 5.25c6.75 0 9.75 6.75 9.75 6.75a18.4 18.4 0 0 1-3.2 4.1M6.1 6.1A18.4 18.4 0 0 0 2.25 12S5.25 18.75 12 18.75c1.1 0 2.1-.18 3-.5M9.7 9.7a3.25 3.25 0 0 0 4.6 4.6"/></svg></button></div>
            </div>
            <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded font-bold hover:bg-orange-700">Masuk</button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-600">
            Belum punya akun? <a href="{{ route('register') }}" class="text-orange-600 font-bold">Daftar</a>
        </p>
    </div>
<script>const passwordInput=document.getElementById("password");const passwordToggle=document.getElementById("toggle-password");const openEye=document.getElementById("eye-open");const closedEye=document.getElementById("eye-closed");passwordToggle.addEventListener("click",()=>{const hidden=passwordInput.type==="password";passwordInput.type=hidden?"text":"password";passwordToggle.setAttribute("aria-pressed",String(hidden));passwordToggle.setAttribute("aria-label",hidden?"Sembunyikan kata sandi":"Tampilkan kata sandi");openEye.classList.toggle("hidden",hidden);closedEye.classList.toggle("hidden",!hidden);});</script></body>
</html>