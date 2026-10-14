<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        if (Auth::guard('web')->attempt($request->only('email', 'password'))) {
            $user = Auth::guard('web')->user();

            $request->session()->regenerate();

            if ($user->role === 'kasir') {
                Auth::guard('kasir')->login($user);
                return redirect()->intended(route('kasir.dashboard'));
            }

            if ($user->role === 'finance') {
                Auth::guard('finance')->login($user);
                return redirect()->intended(route('finance.dashboard'));
            }

            if ($user->role === 'karyawan') {
                Auth::guard('karyawan')->login($user);
                return redirect()->intended(route('karyawan.dashboard'));
            }

            if ($user->role === 'admin') {
                Auth::guard('admin')->login($user);
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('member.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        return redirect('/');
    }
}
