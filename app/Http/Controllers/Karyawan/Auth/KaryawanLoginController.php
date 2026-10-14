<?php

namespace App\Http\Controllers\Karyawan\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaryawanLoginController extends Controller
{
    public function create()
    {
        return view('karyawan.auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('karyawan')->attempt($credentials, $remember)) {
            $user = Auth::guard('karyawan')->user();

            if ($user->role !== 'karyawan') {
                Auth::guard('karyawan')->logout();

                return back()->withErrors([
                    'email' => 'Akses ditolak. Hanya akun Anggota Karyawan KOPIN yang diizinkan masuk ke portal ini.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('karyawan.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kombinasi email atau password karyawan tidak cocok.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::guard('karyawan')->logout();

        return redirect()->route('karyawan.login')->with('success', 'Anda telah berhasil keluar dari Portal Karyawan.');
    }
}
