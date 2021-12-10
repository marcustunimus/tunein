<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory;

class ExploreController extends Controller
{
    public function index(Factory $auth)
    {
        $search = request()->query('search');

        if ($search) {
            $posts = Post::where('body', 'like', '%'.$search.'%')->get();
        }
        else {
            $posts = Post::all();
        }

        $files = PostController::getPostsFiles($posts);

        $postLikes = PostController::getLikesOfPosts($posts);

        $userLikes = PostController::getUserLikedPosts($postLikes);
        
        return view('explore.index', [
            'posts' => $posts,
            'user' => $auth->guard()->user(),
            'files' => $files,
            'postLikes' => $postLikes,
            'userLikes' => $userLikes,
        ]);
    }
}