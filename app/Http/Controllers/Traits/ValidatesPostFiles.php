<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

trait ValidatesPostFiles
{
    public function getPostFiles(Request $request, string $name): array
    {
        if (! $request->has($name)) {
            return [];
        }

        $files = $this->validateFiles($request, $name);

        return $files;
    }

    public function validateFiles(Request $request, string $name): array
    {
        $request->merge([
            $name => $this->filterUploadedFiles($request->file($name)),
        ]);

        $allowedImageExtensions = ['png', 'jpeg', 'gif'];
        $allowedVideoExtensions = ['mp4', 'webm'];

        return $request->validate([
            $name.'.*' => ['mimes:'.implode(',', array_merge($allowedImageExtensions, $allowedVideoExtensions)), 'nullable']
        ], [
            $name.'.*.mimes' => 'The files uploaded must be of one of the following types: '.implode(',', array_merge($allowedImageExtensions, $allowedVideoExtensions))
        ]);
    }

    protected function filterUploadedFiles(array|null $files): array
    {
        $files = collect($files)->unique(function (UploadedFile $file) {
            return $file->getClientOriginalName() . $file->getMimeType() . $file->getSize();
        })->values()->toArray();

        return $files;
    }

    protected function validateMaxPostFilesSize(int $size, string|null $name = ""): void
    {
        $maxPostSize = 40; // Mbs

        if ($size > $maxPostSize * 1024 * 1024) {
            throw ValidationException::withMessages(['max_post_size'.$name => 'The maximum post files size must not be larger than ' . $maxPostSize . ' Mbs.']);
        }
    }
}
