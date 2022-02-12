<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TransformsPostFiles;
use App\Models\Post;
use App\Models\Following;

class HomeController extends Controller
{
    use TransformsPostFiles;

    public function index()
    {
        $search = request()->query('search');

        $followings = Following::query()->select('following_id')->where('user_id', '=', auth()->user()->id);

        if ($search) {
            $posts = Post::query()
                ->whereIn('user_id', $followings)
                ->where('body', 'like', '%'.$search.'%')
                ->orWhere('user_id', auth()->user()->id)
                ->where('body', 'like', '%'.$search.'%')
                ->orderByDesc('created_at')
                ->paginate(3)
                ->withQueryString();
        }
        else {
            $posts = Post::query()
                ->whereIn('user_id', $followings)
                ->orWhere('user_id', auth()->user()->id)
                ->orderByDesc('created_at')
                ->paginate(3)
                ->withQueryString();
        }

        $files = $this->getPostFilesForJs($posts->items());
        
        return view('home.index', [
            'posts' => $posts,
            'user' => auth()->user(),
            'files' => $files,
        ]);
    }
}
