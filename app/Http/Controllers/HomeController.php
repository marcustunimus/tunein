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
    public function index(Factory $auth)
    {
        $search = request()->query('search');

        $followingQuery = Following::query()->select('following_id')->where('user_id', '=', $auth->guard()->user()->id);

        if ($search) {
            $followingQuery = $followingQuery->whereHas('target.posts', function ($query) use ($search) {
                return $query->where('body', 'like', '%'.$search.'%');
            });
        }

        $posts = Post::query()->whereIn('user_id', $followingQuery)->orderByDesc('created_at')->get();

        $files = PostController::getPostsFiles($posts);

        $postLikes = PostController::getLikesOfPosts($posts);

        $userLikes = PostController::getUserLikedPosts($postLikes);

        $postBookmarks = PostController::getBookmarksOfPosts($posts);

        $userBookmarks = PostController::getUserBookmarkedPosts($postBookmarks);
        
        return view('home.index', [
            'posts' => $posts,
            'user' => $auth->guard()->user(),
            'files' => $files,
            'postLikes' => $postLikes,
            'userLikes' => $userLikes,
            'postBookmarks' => $postBookmarks,
            'userBookmarks' => $userBookmarks,
        ]);
    }
}