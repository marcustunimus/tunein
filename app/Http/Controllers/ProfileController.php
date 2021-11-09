<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\PostFile;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Support\Facades\Storage;

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

        $files = [];

        foreach ($posts as $post) {
            $postFiles = [];

            foreach ($post->files as $postFile) {
                array_push($postFiles, 
                    $postFile->file, 
                    Storage::mimeType('public/post_files/' . $postFile->file), 
                    Storage::size('public/post_files/' . $postFile->file)
                );
            }

            $files[$post->id] = implode('|', $postFiles);
        }

        $postLikes = PostController::getLikesOfPosts($posts);

        $userLikes = PostController::getUserLikedPosts($postLikes);
        
        return view('profile.index', [
            'posts' => $posts,
            'user' => $auth->guard()->user(),
            'files' => $files,
            'postLikes' => $postLikes,
            'userLikes' => $userLikes
        ]);
    }
}