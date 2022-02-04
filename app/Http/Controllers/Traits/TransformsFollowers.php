<?php

namespace App\Http\Controllers\Traits;

use App\Models\User;
use App\Models\Following;

trait TransformsFollowers
{
    public function getFollowersForJs(User $user): array
    {
        return collect($user->followers)->map(function (Following $following) {
            return [
                'profile_picture' => $following->user->profile_picture,
                'profile_picture_path' => $following->user->getProfilePictureFullStoragePath(),
                'username' => $following->user->username,
            ];
        })->toArray();
    }
}
