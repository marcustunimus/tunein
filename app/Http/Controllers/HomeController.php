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

        $followingQuery = Following::query()->select('following_id')->where('user_id', '=', auth()->user()->id);

        if ($search) {
            $followingQuery = $followingQuery->whereHas('target.posts', function ($query) use ($search) {
                return $query->where('body', 'like', '%'.$search.'%');
            });
        }

        $posts = Post::query()->where('comment_on_post', '=', null)->whereIn('user_id', $followingQuery)->orWhere([['user_id', '=', auth()->user()->id], ['comment_on_post', '=', null]])->orderByDesc('created_at')->paginate(3)->withQueryString();

        $files = $this->getPostFilesForJs($posts->items());
        
        return view('home.index', [
            'posts' => $posts,
            'user' => auth()->user(),
            'files' => $files,
        ]);
    }
}
