<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read string $file
 */
class PostFile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = ['post_id', 'file'];

    public $timestamps = false;

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function getSize(): int
    {
        return Storage::size($this->getFullPath());
    }

    public function getMimeType(): string
    {
        return Storage::mimeType($this->getFullPath());
    }

    public function getFolderName(): string
    {
        return 'post_files';
    }

    public function getDirectoryPath(): string
    {
        return 'public/' . $this->getFolderName();
    }

    public function getFullPath(): string
    {
        return $this->getDirectoryPath() . '/' . $this->file;
    }

    public function getStoragePath(): string
    {
        return 'storage/' . $this->getFolderName();
    }

    public function getFullStoragePath(): string
    {
        return $this->getStoragePath() . '/' . $this->file;
    }

    public function deleteFileInStorage(): bool
    {
        return Storage::delete($this->getFullPath());
    }
}
