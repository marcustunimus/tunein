<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TransformsFollowers;
use App\Models\User;
use App\Models\Following;

class FollowController extends Controller
{
    use TransformsFollowers;

    public function index(User $user): string|false
    {
        return json_encode($this->getFollowersForJs($user));
    }

    public function store(User $user): string|false
    {
        if (auth()->user() == null) {
            return json_encode("Login");
        }

        if ($user->id === auth()->user()->id) {
            return json_encode("FollowDenied");
        }

        $followAttributes = [
            'user_id' => auth()->user()->id,
            'following_id' => $user->id
        ];

        if (! auth()->user()->isFollowing($user)) {
            Following::create($followAttributes);
        }

        return json_encode("Followed");
    }

    public function destroy(User $user): string|false
    {
        if (auth()->user() == null) {
            return json_encode("Login");
        }

        if (auth()->user()->isFollowing($user)) {
            auth()->user()->following()->where('following_id', $user->id)->first()->delete();
        }

        return json_encode("Unfollowed");
    }
}
