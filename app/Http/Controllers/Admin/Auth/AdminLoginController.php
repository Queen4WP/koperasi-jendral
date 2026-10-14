<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    /**
     * Tampilkan formulir login Administrator.
     */
    public function create()
    {
        return view('admin.auth.login');
    }

    /**
     * Proses autentikasi akun Administrator ke guard admin.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $user = Auth::guard('admin')->user();

            // Validasi peran khusus Administrator
            if ($user->role !== 'admin') {
                Auth::guard('admin')->logout();

                return back()->withErrors([
                    'email' => 'Akses ditolak. Hanya akun Administrator KOPIN yang diizinkan masuk ke portal ini.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kombinasi email atau password administrator tidak cocok.',
        ])->onlyInput('email');
    }

    /**
     * Keluar dari sesi Administrator (hanya mengeluarkan guard admin).
     */
    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil keluar dari Portal Administrator.');
    }
}
