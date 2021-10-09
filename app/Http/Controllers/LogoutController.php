<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function destroy(Factory $auth): RedirectResponse
    {
        $auth->guard()->logout();

        return redirect()->route('welcome');
    }
}