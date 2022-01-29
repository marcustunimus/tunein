<?php

namespace App\Http\Controllers\Traits;

use App\Models\Post;
use App\Models\PostFile;
use Illuminate\Support\Collection;

trait TransformsPostFiles
{
    public function getPostFilesForJs(Collection|array $posts): array
    {
        return collect($posts)->keyBy('id')->map(function (Post $post) {
            return $post->files->map(function (PostFile $file) {
                return [
                    'name' => $file->file,
                    'path' => $file->getFullStoragePath(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            });
        })->toArray();
    }
}