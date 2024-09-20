<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'profile_picture',
        'background_picture',
        'gender'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function setPasswordAttribute(string $password): static
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_DEFAULT);

        return $this;
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function following(): HasMany
    {
        return $this->hasMany(Following::class);
    }

    public function followers():HasMany
    {
        return $this->hasMany(Following::class, 'following_id');
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    public function isFollowedBy(User $user): bool
    {
        return $this->followers()->where('user_id', $user->id)->exists();
    }

    private function generateFileName($image): string
    {
        return $this->id.'_'.Str::random(32).'.'.$image->extension();
    }

    // The methods related to the profile picture of the user.

    public function changeProfilePicture($image): bool
    {
        $profilePictureName = $this->generateFileName($image);

        $image->storeAs($this->getProfilePicturesFolderName(), $profilePictureName, 'public');

        $this->deleteProfilePicture();

        return $this->update([
            'profile_picture' => $profilePictureName,
        ]);
    }

    public function deleteProfilePicture(): bool
    {
        Storage::delete($this->getProfilePictureFullPath());

        return $this->update([
            'profile_picture' => null,
        ]);
    }

    public function getProfilePicturesFolderName(): string
    {
        return 'profile_pictures';
    }

    public function getProfilePicturesDirectoryPath(): string
    {
        return 'public/' . $this->getProfilePicturesFolderName();
    }

    public function getProfilePictureFullPath(): string
    {
        return $this->getProfilePicturesDirectoryPath() . '/' . $this->profile_picture;
    }

    public function getProfilePictureStoragePath(): string
    {
        return 'storage/' . $this->getProfilePicturesFolderName();
    }

    public function getProfilePictureFullStoragePath(): string
    {
        return $this->getProfilePictureStoragePath() . '/' . $this->profile_picture;
    }

    // The methods related to the background picture of the user.

    public function changeBackgroundPicture($image): bool
    {
        $backgroundPictureName = $this->generateFileName($image);

        $image->storeAs($this->getBackgroundPicturesFolderName(), $backgroundPictureName, 'public');

        $this->deleteBackgroundPicture();

        return $this->update([
            'background_picture' => $backgroundPictureName,
        ]);
    }

    public function deleteBackgroundPicture(): bool
    {
        Storage::delete($this->getBackgroundPictureFullPath());

        return $this->update([
            'background_picture' => null,
        ]);
    }

    public function getBackgroundPicturesFolderName(): string
    {
        return 'profile_backgrounds';
    }

    public function getBackgroundPicturesDirectoryPath(): string
    {
        return 'public/' . $this->getBackgroundPicturesFolderName();
    }

    public function getBackgroundPictureFullPath(): string
    {
        return $this->getBackgroundPicturesDirectoryPath() . '/' . $this->background_picture;
    }

    public function getBackgroundPictureStoragePath(): string
    {
        return 'storage/' . $this->getBackgroundPicturesFolderName();
    }

    public function getBackgroundPictureFullStoragePath(): string
    {
        return $this->getBackgroundPictureStoragePath() . '/' . $this->background_picture;
    }
}
