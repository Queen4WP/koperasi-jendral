<?php

namespace App\Http\Controllers\Finance\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceLoginController extends Controller
{
    public function create()
    {
        return view('finance.auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('finance')->attempt($credentials, $remember)) {
            $user = Auth::guard('finance')->user();

            if ($user->role !== 'finance') {
                Auth::guard('finance')->logout();

                return back()->withErrors([
                    'email' => 'Akses ditolak. Hanya staf Finance KOPIN yang diizinkan masuk ke portal ini.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('finance.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kombinasi email atau password staf finance tidak cocok.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::guard('finance')->logout();

        return redirect()->route('finance.login')->with('success', 'Anda telah berhasil keluar dari Portal Finance.');
    }
}
