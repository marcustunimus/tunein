<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property-read int $id
 * @property-read int $user_id
 *
 * @property-read User $author
 * @property-read EloquentCollection|PostFile[] $files
 *
 * @method static Builder|Post query()
 * @method Builder|Post comments()
 */
class Post extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = ['user_id', 'body', 'comment_on_post'];

    /**
     * @inheritDoc
     *
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'id' => 'int',
            'user_id' => 'int',
        ];
    }

    protected static function booted(): void
    {
        Post::addGlobalScope(function (Builder $builder) {
            return $builder->whereNull('comment_on_post');
        });

        static::deleting(function (Post $post) {
            $post->onDeleting();
        });
    }

    protected function scopeComments(Builder $builder): Builder
    {
        return $builder->withoutGlobalScopes()->whereNotNull('comment_on_post');
    }

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
        return $this->hasMany(Post::class, 'comment_on_post')->withoutGlobalScopes();
    }

    public function isLikedByUser(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function isBookmarkedByUser(User $user): bool
    {
        return $this->bookmarks()->where('user_id', $user->id)->exists();
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

    public function saveFiles(array $files): void
    {
        foreach ($files as $file) {
            $fileAttributes = [
                'post_id' => $this->id,
                'file' => null,
            ];

            $postFile = PostFile::create($fileAttributes);

            $fileName = $postFile->id . '_' . Str::random(32) . '.' . $file->extension();
            $postFile->file = $fileName;
            $postFile->save();
            $postFile->file = $file->storeAs($postFile->getFolderName(), $fileName, 'public');
        }
    }

    public function getFilesInfo(): array
    {
        $filesInfo = [];

        foreach ($this->files as $file) {
            $filesInfo[] = [
                'name' => $file->file,
                'path' => $file->getFullStoragePath(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];
        }

        return $filesInfo;
    }

    private function onDeleting(): void
    {
        $this->deleteFiles();
        $this->likes()->delete();
        $this->bookmarks()->delete();
    }
}
