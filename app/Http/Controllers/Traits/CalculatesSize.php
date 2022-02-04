<?php

namespace App\Http\Controllers\Traits;

use App\Models\Post;
use Illuminate\Http\UploadedFile;

trait CalculatesSize
{
    public function calculateUploadedFilesSize(UploadedFile|array $files): int
    {
        $size = 0;

        foreach ($files as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    public function calculatePostFilesSize(Post $post, array $skipFilesNames = []): int
    {
        $size = 0;

        foreach ($post->files as $postFile) {
            if (!in_array($postFile->file, $skipFilesNames, true)) {
                $size += $postFile->getSize();
            }
        }

        return $size;
    }
}