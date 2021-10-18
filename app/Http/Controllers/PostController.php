<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostFile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Post $post)
    {
        return view('post.index', ['post' => $post]);
    }

    public function edit(Post $post)
    {
        $files = [];

        foreach (PostFile::query()->select('file')->where('post_id', '=', $post->id)->get() as $postFile) {
            array_push($files, 
                $postFile->file, 
                Storage::mimeType('public/post_files/' . $postFile->file), 
                Storage::size('public/post_files/' . $postFile->file)
            );
        }

        return view('post.edit', [
            'post' => $post,
            'files' => implode('|', $files)
        ]);
    }

    public function store()
    {
        $postAttributes = array_merge($this->validatePostBody(), [
            'user_id' => auth()->user()->id
        ]);

        $files = request('uploadedFiles') ? $this->validateFiles(new Request($this->filterUploadedFiles(request('uploadedFiles')))) : [];

        Post::create($postAttributes);

        if (count($files)) {
            $lasestPostId = Post::latest('id')->first()->id;

            $this->createPostFiles($lasestPostId, $files);
        }

        return redirect('home');
    }

    public function update(Post $post)
    {
        $postAttributes = $this->validatePostBody(new Post());

        $files = request('uploadedFiles') ? $this->validateFiles(new Request($this->filterUploadedFiles(request('uploadedFiles')))) : [];

        $post->update($postAttributes);

        $this->removePostFiles($post->id, explode('/', request('removedPostFiles')));

        if (count($files)) {
            $this->createPostFiles($post->id, $files);
        }

        return redirect('home');
    }

    public function destroy(Post $post)
    {
        $postFiles = PostFile::query()->where('post_id', '=', $post->id)->get();

        $postFilesNames = [];

        foreach ($postFiles as $postFile) {
            array_push($postFilesNames, $postFile->file);
        }

        Storage::delete(array_map(function ($value) {
            return 'public/post_files/' . $value;
        }, $postFilesNames));

        $postFiles = PostFile::query()->where('post_id', '=', $post->id)->delete();

        $post->delete();

        return redirect('home');
    }

    protected function validatePostBody(?Post $post = null): array
    {
        $post ??= new Post();

        return request()->validate([
            'body' => ['required', 'max:2000'],
        ]);
    }

    protected function filterUploadedFiles(array $files): array
    {
        $files = collect($files);
        
        $files = $files->unique(function (UploadedFile $file) {
            return $file->getClientOriginalName().$file->getMimeType().$file->getSize();
        })->values()->toArray();

        return ['file' => $files];
    }

    protected function removePostFiles(int $postId, array $removedPostFiles) {
        if (! $removedPostFiles) {
            return;
        }

        PostFile::query()->select()->where('post_id', '=', $postId)->whereIn('file', $removedPostFiles)->delete();

        Storage::delete(array_map(function ($value) {
            return 'public/post_files/' . $value;
        }, $removedPostFiles));
    }

    protected function validateFiles(Request $request): array
    {
        $allowedImageExtensions = ['png', 'jpeg', 'gif'];
        $allowedVideoExtensions = ['mp4', 'webm'];

        return $request->validate([
            'file.*' => ['mimes:' . implode(',', array_merge($allowedImageExtensions, $allowedVideoExtensions)), 'nullable']
        ], [
            'file.*.mimes' => 'The files uploaded must be of one of the following types: ' . implode(',', array_merge($allowedImageExtensions, $allowedVideoExtensions))
        ]);
    }

    protected function createPostFiles(int $postID, array $files): void
    {
        foreach($files as $allFiles) {
            foreach ($allFiles as $file) {
                $fileAttributes = [
                    'post_id' => $postID,
                    'file' => null,
                ];
            
                $postFile = PostFile::create($fileAttributes);

                $filename = $postFile->id . '_' . Str::random(32) . '.' . $file->extension();
                $postFile->file = $filename;
                $postFile->save();
                $postFile->file = $file->storeAs('post_files', $filename, 'public');
            }
        }
    }
}