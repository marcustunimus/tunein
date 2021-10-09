<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index()
    {
        $search = request()->query('search');

        if ($search) {
            $posts = Post::where('body', 'like', '%'.$search.'%')->get();
        }
        else {
            $posts = Post::all();
        }
        
        return view('explore.index', [
            'posts' => $posts
        ]);
    }
}