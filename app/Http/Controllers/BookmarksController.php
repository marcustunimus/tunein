<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory;

class BookmarksController extends Controller
{
    public function index(Factory $auth)
    {
        $search = request()->query('search');

        $bookmarksQuery = Bookmark::query()->select('post_id')->where('user_id', '=', $auth->guard()->user()->id);

        if ($search) {
            $bookmarksQuery = $bookmarksQuery->whereHas('post', function ($query) use ($search) {
                    return $query->where('body', 'like', '%'.$search.'%');
                });
        }

        $posts = Post::query()->whereIn('user_id', $bookmarksQuery)->get();
        
        return view('bookmarks.index', [
            'bookmarks' => $posts
        ]);
    }
}