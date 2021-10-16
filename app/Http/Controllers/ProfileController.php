<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory;

class ProfileController extends Controller
{
    public function index(Factory $auth)
    {
        $search = request()->query('search');

        if ($search) {
            $posts = $auth->guard()->user()->posts()->where('body', 'like', '%'.$search.'%')->get();
        }
        else {
            $posts = $auth->guard()->user()->posts;
        }
        
        return view('profile.index', [
            'posts' => $posts,
            'user' => $auth->guard()->user()
        ]);
    }
}