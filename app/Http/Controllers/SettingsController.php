<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ValidatesPasswordChange;
use App\Http\Controllers\Traits\ValidatesProfileRelatedImages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    use ValidatesProfileRelatedImages;
    use ValidatesPasswordChange;

    public function index(User $user)
    {
        return view('profile.settings', [
            'user' => $user
        ]);
    }

    public function store(Request $request, User $user)
    {
        $genders = ['Male', 'Female', 'Unspecified'];

        $attributes = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:64'],
            'username' => ['required', 'string', 'min:3', 'max:32', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'gender' => ['required', Rule::in($genders)],
            'password_current' => ['nullable', 'string'],
            'password' => ['string', 'min:8', 'max:255', 'confirmed', 'nullable'],
            'password_confirmation' => ['nullable', 'string']
        ]);

        $profilePicture = $this->validateImage($request, 'uploadedProfilePictureFile', 'profile', $request->file('profilePictureRemove') === "on");
        $backgroundPicture = $this->validateImage($request, 'uploadedBackgroundPictureFile', 'background', $request->file('backgroundPictureRemove') === "on");

        $this->validatePasswordChange($user, $attributes);

        $profileAttributes = [
            'name' => $attributes['name'],
            'username' => $attributes['username'],
            'email' => $attributes['email'],
            'gender' => $attributes['gender'] !== 'Unspecified' ? $attributes['gender'] : null,
        ];

        if ($attributes['password'] !== null) {
            $profileAttributes['password'] = $attributes['password'];
        }

        $user->update($profileAttributes);

        if ($request->input('profilePictureRemove') === "on") {
            $user->deleteProfilePicture();
        }
        elseif ($profilePicture['uploadedProfilePictureFile'] !== null) {
            $user->changeProfilePicture($profilePicture['uploadedProfilePictureFile']);
        }

        if ($request->input('backgroundPictureRemove') === "on") {
            $user->deleteBackgroundPicture();
        }
        elseif ($backgroundPicture['uploadedBackgroundPictureFile'] !== null) {
            $user->changeBackgroundPicture($backgroundPicture['uploadedBackgroundPictureFile']);
        }

        return redirect()->back()->with('message', 'The settings have been saved.');
    }
}
