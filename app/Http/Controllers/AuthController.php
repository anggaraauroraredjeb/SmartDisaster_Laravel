<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginPengguna(): View
    {
        return view('auth.login', ['admin' => false]);
    }

    public function showLoginAdmin(): View
    {
        return view('auth.login', ['admin' => true]);
    }

    public function showRegister(): View
    {
        return view('auth.registrasi');
    }

    public function loginPengguna(Request $request): RedirectResponse
    {
        return $this->attempt($request, false);
    }

    public function loginAdmin(Request $request): RedirectResponse
    {
        return $this->attempt($request, true);
    }

    private function attempt(Request $request, bool $admin): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email dan kata sandi wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'password.required' => 'Email dan kata sandi wajib diisi.',
        ]);

        $back = $admin ? 'login.admin' : 'login.pengguna';
        $user = User::where('email', strtolower(trim($data['email'])))->first();

        if (! $user || ! Hash::check($data['password'], $user->password_hash)) {
            return redirect()->route($back)->withInput($request->only('email'))->with('error', 'Email atau kata sandi tidak sesuai.');
        }

        if (($admin && ! $user->isAdmin()) || (! $admin && $user->isAdmin())) {
            $message = $admin
                ? 'Akun ini tidak memiliki akses ke portal Administrator.'
                : 'Silakan gunakan portal Admin untuk login.';

            return redirect()->route($back)->withInput($request->only('email'))->with('error', $message);
        }

        $request->session()->regenerate();
        $request->session()->put('user_id', $user->id);

        return redirect()->route($admin ? 'admin.dashboard' : 'pengguna.beranda');
    }

    public function register(Request $request): RedirectResponse
    {
        $email = $request->input('email');
        if (is_string($email)) {
            $request->merge(['email' => strtolower(trim($email))]);
        }

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'konfirmasi_password' => ['required', 'same:password'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.string' => 'Nama lengkap harus berupa teks.',
            'nama.max' => 'Nama lengkap maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.string' => 'Email harus berupa teks.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'email.max' => 'Email maksimal 100 karakter.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.string' => 'Kata sandi harus berupa teks.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'konfirmasi_password.required' => 'Konfirmasi kata sandi wajib diisi.',
            'konfirmasi_password.same' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        User::create([
            'nama_lengkap' => $data['nama'],
            'email' => strtolower($data['email']),
            'no_telepon' => '',
            'role' => 'mahasiswa',
            'password_hash' => Hash::make($data['password']),
        ]);

        return redirect()->route('login.pengguna')->with('success', 'Registrasi berhasil! Silakan masuk.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing.home');
    }
}
