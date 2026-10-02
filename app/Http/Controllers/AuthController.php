<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class AuthController extends Controller
{
    public function editProfile()
    {
        return view('auth.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->update(array_filter([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'] ?? null,
        ], fn ($value) => $value !== null));

        return back()->with('success', 'Profil berhasil diperbarui.');
    }


    // Tampilkan Form Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses Login (Arahkan Owner & Admin ke Panel Filament /admin)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Restore data keranjang dari database ke session
            if ($user->cart) {
                $dbCart = json_decode($user->cart, true) ?? [];
                $sessionCart = session()->get('cart', []);
                
                // Penggabungan keranjang dari session dan database
                $mergedCart = array_replace_recursive($dbCart, $sessionCart);
                
                session()->put('cart', $mergedCart);
                $user->update(['cart' => json_encode($mergedCart)]);
            } elseif (session()->has('cart')) {
                // Jika DB kosong tetapi session lokal ada item, simpan ke DB
                $user->update(['cart' => json_encode(session()->get('cart'))]);
            }

            // Jika Owner atau Admin, otomatis masuk ke dashboard /admin
            if ($user->isOwner() || $user->isAdmin()) {
                return redirect()->intended('/admin');
            }

            // Jika Pelanggan biasa, arahkan ke Halaman Utama
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Tampilkan Form Register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proses Registrasi Pelanggan Baru
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user', // Diselaraskan dengan enum role di User.php
        ]);

        Auth::login($user);

        // Simpan keranjang session ke akun baru jika ada
        if (session()->has('cart')) {
            $user->update(['cart' => json_encode(session()->get('cart'))]);
        }

        return redirect()->route('home')->with('success', 'Pendaftaran berhasil! Selamat datang.');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        // Simpan keranjang saat ini ke database sebelum session dihancurkan
        if (Auth::check()) {
            $cart = session()->get('cart', []);
            Auth::user()->update(['cart' => json_encode($cart)]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}