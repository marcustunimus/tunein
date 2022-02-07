<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TransformsPostLikes;
use App\Models\Post;
use App\Models\Like;

class LikeController extends Controller
{
    use TransformsPostLikes;

    public function index(Post $anyPost)
    {
        return json_encode($this->getPostLikesForJs($anyPost));
    }

    public function store(Post $anyPost): string|false
    {
        if (auth()->user() == null) {
            return json_encode("Login");
        }

        $likeAttributes = [
            'user_id' => auth()->user()->id,
            'post_id' => $anyPost->id
        ];

        if (! $anyPost->isLikedByUser(auth()->user())) {
            Like::create($likeAttributes);
        }

        return json_encode("Liked");
    }

    public function destroy(Post $anyPost): string|false
    {
        if (auth()->user() == null) {
            return json_encode("Login");
        }

        if ($anyPost->isLikedByUser(auth()->user())) {
            $anyPost->likes()->where('user_id', auth()->user()->id)->first()->delete();
        }

        return json_encode("Unliked");
    }
}
