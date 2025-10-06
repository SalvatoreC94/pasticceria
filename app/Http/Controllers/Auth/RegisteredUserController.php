<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone'                 => ['nullable', 'string', 'max:30'],
            'address.via'           => ['required', 'string', 'max:140'],
            'address.civico'        => ['required', 'string', 'max:20'],
            'address.cap'           => ['required', 'regex:/^\d{5}$/'],
            'address.citta'         => ['required', 'string', 'max:100'],
            'address.prov'          => ['required', 'string', 'size:2'],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'             => $data['name'],
            'email'            => $data['email'],
            'phone'            => $data['phone'] ?? null,
            'shipping_address' => $data['address'],
            'password'         => Hash::make($data['password']),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
