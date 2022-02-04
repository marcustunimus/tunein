<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait ValidatesProfileRelatedImages
{
    public function validateImage(Request $request, string $name, string $type, bool $cancel): array
    {
        if ($cancel) {
            return [
                $name => null
            ];
        }

        $allowedImageExtensions = ['png', 'jpeg'];

        $image = $request->validate([
            $name => ['mimes:' . implode(',', $allowedImageExtensions), 'nullable']
        ], [
            $name . '.mimes' => 'The files uploaded must be of one of the following types: ' . implode(',', $allowedImageExtensions)
        ]);

        if ($image == null) {
            return [
                $name => null
            ];
        }

        if ($type === 'profile') {
            $this->validateProfilePictureSize($image[$name]->getSize());
        }
        elseif ($type === 'background') {
            $this->validateBackgroundPictureSize($image[$name]->getSize());
        }

        return $image;
    }

    public function validateProfilePictureSize(int $size): void
    {
        $maxFileSize = 2; // Mbs

        if ($size > $maxFileSize * 1024 * 1024) {
            throw ValidationException::withMessages(['max_picture_size' => 'The maximum post files size must not be larger than ' . $maxFileSize . ' Mbs.']);
        }
    }

    public function validateBackgroundPictureSize(int $size): void
    {
        $maxFileSize = 5; // Mbs

        if ($size > $maxFileSize * 1024 * 1024) {
            throw ValidationException::withMessages(['max_picture_size' => 'The maximum post files size must not be larger than ' . $maxFileSize . ' Mbs.']);
        }
    }
}
