<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TransformsPostLikes;
use App\Models\Post;
use App\Models\Like;

class LikeController extends Controller
{
    use TransformsPostLikes;

    public function index(Post $post)
    {
        return json_encode($this->getPostLikesForJs($post));
    }

    public function store(Post $post): string|false
    {
        if (auth()->user() == null) {
            return json_encode("Login");
        }

        $likeAttributes = [
            'user_id' => auth()->user()->id,
            'post_id' => $post->id
        ];

        if (! $post->isLikedByUser(auth()->user())) {
            Like::create($likeAttributes);
        }

        return json_encode("Liked");
    }

    public function delete(Post $post): string|false
    {
        // TODO: Add delete functionality.

        $post->likes()->where('user_id', auth()->user()->id)->first()->delete();

        return json_encode("Unliked");
    }
}
