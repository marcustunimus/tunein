<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TransformsPostFiles;
use App\Models\User;

class ProfileController extends Controller
{
    use TransformsPostFiles;

    public function index(User $user)
    {
        $search = request()->query('search');

        if ($search) {
            $posts = $user->posts()->where('body', 'like', '%'.$search.'%')->latest()->paginate(10)->withQueryString();
        }
        else {
            $posts = $user->posts()->latest()->paginate(10)->withQueryString();
        }

        $files = $this->getPostFilesForJs($posts->items());
        
        return view('profile.index', [
            'user' => $user,
            'posts' => $posts,
            'files' => $files,
        ]);
    }
}
