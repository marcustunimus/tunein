<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use App\Models\Post;
use App\Models\PostFile;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Post $post)
    {
        return view('post.index', ['post' => $post]);
    }

    public function edit(Post $post)
    {
        return view('post.edit', ['post' => $post]);
    }

    public function store()
    {
        $postAttributes = array_merge($this->validatePostBody(), [
            'user_id' => auth()->user()->id
        ]);

        $files = request('file') ? $this->validateFiles(new Request($this->filterFiles(request('file')))) : [];

        ddd($files);

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



        // foreach($post->files()->select('file') as $filePath) {
        //     File::delete(storage_path('app/'.$filePath));
        //     File::delete()
        // }

        $post->update($postAttributes);

        return back()->with('success', 'Post Updated');
    }

    // public function destroy(Post $post)
    // {
    //     $post->delete();

    //     return back()->with('success', 'Post Deleted');
    // }

    protected function validatePostBody(?Post $post = null): array
    {
        $post ??= new Post();

        return request()->validate([
            'body' => ['required', 'max:2000'],
        ]);
    }

    protected function filterFiles(array $files): array
    {
        $files = collect($files);
        $files = $files->unique(function (UploadedFile $file) {
            return $file->getClientOriginalName().$file->getMimeType().$file->getSize();
        })->values()->toArray();

        return ['file' => $files];
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
                $postFile->file = $file->storeAs('post_files', $filename);
            }
        }
    }


}