<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function adminLoginForm()
    {
        return view('authentication.admin.login');
    }
    public function adminLoginSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (auth()->guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.index');
        }

        return redirect()->back()
            ->withErrors(['email' => 'Invalid credentials'])
            ->withInput($request->only('email'));
    }

    public function adminLogout(Request $request)
    {
        auth()->guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('calendar.admin.login.form');
    }
}
