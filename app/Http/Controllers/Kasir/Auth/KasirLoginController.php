<?php

namespace App\Http\Controllers\Kasir\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasirLoginController extends Controller
{
    public function create()
    {
        return view('kasir.auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('kasir')->attempt($credentials, $remember)) {
            $user = Auth::guard('kasir')->user();

            if ($user->role !== 'kasir') {
                Auth::guard('kasir')->logout();

                return back()->withErrors([
                    'email' => 'Akses ditolak. Hanya staf Kasir KOPIN yang diizinkan masuk ke portal ini.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('kasir.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kombinasi email atau password kasir tidak cocok.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::guard('kasir')->logout();

        return redirect()->route('kasir.login')->with('success', 'Anda telah berhasil keluar dari Portal Kasir.');
    }
}
