<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Following;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\Factory;

class HomeController extends Controller
{
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

        $files = PostController::getPostsFiles($posts->items());

        return view('home.index', [
            'posts' => $posts,
            'user' => auth()->user(),
            'files' => $files,
        ]);
    }
}
