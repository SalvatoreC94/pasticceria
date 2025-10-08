<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ConfirmablePasswordController extends Controller
{
    public function show()
    {
        return view('auth.confirm-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required'],
        ]);

        if (! Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors([
                'password' => __('The provided password does not match our records.'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended();
    }
}
