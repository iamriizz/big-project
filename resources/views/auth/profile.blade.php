<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun - Fav Dimsum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-orange-50 p-4 text-gray-800 sm:p-8">
    @php($user = auth()->user())
    <main class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-orange-600">Akun Staf</p>
                <h1 class="mt-1 text-2xl font-bold sm:text-3xl">Pengaturan Akun</h1>
            </div>
            <a href="{{ $user->isOwner() || $user->isAdmin() ? url('/admin') : route('home') }}" class="rounded-xl border border-orange-200 bg-white px-4 py-2 text-sm font-semibold text-orange-600">Kembali</a>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-xl border border-green-100 bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-100 bg-red-50 p-3 text-sm text-red-700">Periksa kembali data yang diisi.</div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <section class="rounded-3xl border border-orange-100 bg-white p-5 shadow-sm sm:p-7">
                <div class="mb-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-orange-600">Profil Saya</p>
                    <h2 class="mt-1 text-xl font-bold">Informasi Profil</h2>
                </div>

                <div class="mb-5 grid gap-3 rounded-2xl bg-orange-50 p-4 sm:grid-cols-2">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wide text-gray-400">Peran / Role</p>
                        <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $user->isOwner() ? 'bg-amber-100 text-amber-800' : 'bg-orange-100 text-orange-700' }}">
                            {{ $user->isOwner() ? 'Pemilik (Owner)' : ($user->isAdmin() ? 'Admin Cabang' : 'Pelanggan') }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wide text-gray-400">Cabang Tugas</p>
                        <p class="mt-2 text-sm font-bold text-gray-800">{{ $user->branch?->name ?? ($user->isOwner() ? 'Seluruh Cabang Fav Dimsum' : 'Belum ditugaskan') }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold">Nama Lengkap</label>
                        <input name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border border-gray-200 p-3 focus:border-orange-500 focus:outline-none">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">No. WhatsApp / HP</label>
                        <input name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-xl border border-gray-200 p-3 focus:border-orange-500 focus:outline-none" placeholder="08123456789">
                        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-semibold">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-xl border border-gray-200 p-3 focus:border-orange-500 focus:outline-none">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-orange-100 bg-white p-5 shadow-sm sm:p-7">
                <div class="mb-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-orange-600">Keamanan</p>
                    <h2 class="mt-1 text-xl font-bold">Ubah Password</h2>
                    <p class="mt-1 text-sm text-gray-500">Kosongkan semua kolom password jika tidak ingin mengubahnya.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-semibold">Password Saat Ini</label>
                        <input type="password" name="current_password" autocomplete="current-password" class="w-full rounded-xl border border-gray-200 p-3 focus:border-orange-500 focus:outline-none">
                        @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">Password Baru</label>
                        <input type="password" name="password" autocomplete="new-password" class="w-full rounded-xl border border-gray-200 p-3 focus:border-orange-500 focus:outline-none">
                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password" class="w-full rounded-xl border border-gray-200 p-3 focus:border-orange-500 focus:outline-none">
                    </div>
                </div>
            </section>

            <button class="w-full rounded-2xl bg-orange-600 px-4 py-3.5 font-bold text-white shadow-lg hover:bg-orange-700">Simpan Perubahan</button>
        </form>
    </main>
</body>
</html>
