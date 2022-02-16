<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.index');
    }

    public function attempt(Request $request, Factory $auth, Store $session): RedirectResponse
    {
        $attributes = $request->validate([
            'username' => ['required', 'string'],
            // 'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! $auth->guard()->attempt($attributes, $request->input('remember') === "on" ? true : false)) {
            throw ValidationException::withMessages([
                'username' => 'Your provided credentials could not be verified.'
                // 'email' => 'Your provided credentials could not be verified.'
            ]);
        }
        
        $session->regenerate();

        return redirect()->route('home')->with('message', 'Welcome, ' . $auth->guard()->user()->name . '!');
    }
}