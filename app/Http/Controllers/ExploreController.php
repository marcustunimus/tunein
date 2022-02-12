<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TransformsPostFiles;
use App\Models\Post;
use Illuminate\Contracts\Auth\Factory;

class ExploreController extends Controller
{
    use TransformsPostFiles;

    public function index(Factory $auth)
    {
        $search = request()->query('search');

        if ($search) {
            $posts = Post::query()->where('body', 'like', '%'.$search.'%')->latest()->paginate(3)->withQueryString();
        }
        else {
            $posts = Post::query()->orderByDesc('created_at')->paginate(3)->withQueryString();
        }

        $files = $this->getPostFilesForJs($posts->items());

        return view('explore.index', [
            'posts' => $posts,
            'user' => $auth->guard()->user(),
            'files' => $files,
        ]);
    }
}
