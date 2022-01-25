<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read string $file
 */
class PostFile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = ['post_id', 'file'];

    // protected $with = ['post'];

    public $timestamps = false;

    public function post()
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

    public function getDirectoryPath(): string
    {
        return 'public/post_files';
    }

    public function getFullPath(): string
    {
        return $this->getDirectoryPath() . '/' . $this->file;
    }
}
