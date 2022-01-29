<?php

namespace App\Http\Controllers\Traits;

use App\Models\Like;
use App\Models\Post;

trait TransformsPostLikes
{
    public function getPostLikesForJs(Post $post): array
    {
        return collect($post->likes)->map(function (Like $like) {
            return [
                'profile_picture' => $like->user->profile_picture,
                'username' => $like->user->username,
            ];
        })->toArray();
    }
}