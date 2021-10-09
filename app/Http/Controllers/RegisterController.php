<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function index()
    {
        return view('register.index');
    }

    public function store(Request $request, Factory $auth): RedirectResponse
    {
        $attributes = $request->validate([
            'name' => ['required', 'min:3', 'max:64'],
            'username' => ['required', 'min:3', 'max:32', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'max:255', 'confirmed'],
        ]);

        $user = User::create($attributes);

        $auth->guard()->login($user);

        return redirect()->route('home')->with('success', 'You have been registered.');
    }
}