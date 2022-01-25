<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // protected $with = ['author'];

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
}
