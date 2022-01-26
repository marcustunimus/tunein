<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read int $id
 * @property-read int $user_id
 *
 * @property-read User $author
 * @property-read EloquentCollection|PostFile[] $files
 */
class Post extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = ['user_id', 'body', 'comment_on_post'];

    protected $casts = [
        'id' => 'int',
        'user_id' => 'int',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(PostFile::class, 'post_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function subPosts(): HasMany
    {
        return $this->hasMany(Post::class, 'comment_on_post');
    }

    public function isLikedByUser(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function isBookmarkedByUser(User $user): bool
    {
        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    public function remove(): bool|null
    {
        $this->deleteFiles();
        $this->likes()->delete();
        $this->bookmarks()->delete();
        return $this->delete();
    }

    public function deleteFiles(): bool|null
    {
        foreach ($this->files as $file) {
            $file->deleteFileInStorage();
        }

        return $this->files()->delete();
    }

    public function deleteFilesByNames(array $names): bool|null
    {
        foreach ($this->files as $file) {
            if (in_array($file->file, $names, true)) {
                $file->deleteFileInStorage();
            }
        }

        return $this->files()->whereIn('file', $names)->delete();
    }

    public function saveFiles(array $files)
    {
        
    }
}
