<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\CalculatesSize;
use App\Http\Controllers\Traits\TransformsPostFiles;
use App\Http\Controllers\Traits\TransformsPostLikes;
use App\Http\Controllers\Traits\ValidatesPostAttributes;
use App\Http\Controllers\Traits\ValidatesPostFiles;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ValidatesPostAttributes;
    use TransformsPostFiles;
    use TransformsPostLikes;
    use ValidatesPostFiles;
    use CalculatesSize;

    public function index(Post $post)
    {
        $comments = $post->subPosts()->orderBy('created_at')->paginate(3)->withQueryString();

        $files = $this->getPostFilesForJs([$post]);

        $commentsFiles = $this->getPostFilesForJs($comments->items());

        return view('post.index', [
            'post' => $post,
            'comments' => $comments,
            'user' => auth()->user(),
            'files' => $files,
            'commentsFiles' => $commentsFiles,
        ]);
    }

    public function edit(Post $post)
    {
        return view('post.edit', [
            'post' => $post,
            'files' => $this->getPostFilesForJs([$post]),
        ]);
    }

    public function store(Request $request)
    {
        $commentIdAttribute = $this->getPostCommentIdAttribute($request);
        $name = 'uploadedFiles'.$commentIdAttribute;

        $postAttributes = [
            'user_id' => auth()->user()->id,
            'body' => $this->getPostBodyAttribute($request, 'body'.$commentIdAttribute),
            'comment_on_post' => $commentIdAttribute,
        ];

        $files = $this->getPostFiles($request, $name);

        if (count($files)) {
            $this->validateMaxPostFilesSize($this->calculateUploadedFilesSize($files[$name]), $commentIdAttribute);
        }

        $post = Post::create($postAttributes);

        $post = $post->fresh();

        if (count($files)) {
            $post->saveFiles($files[$name]);
        }

        if ($commentIdAttribute !== null) {
            return redirect()->back()->with('message', 'The comment has been created.')->with('postId', $commentIdAttribute);
        } else {
            return redirect()->route('home')->with('message', 'The post has been created.');
        }
    }

    public function update(Post $post, Request $request)
    {
        $postAttributes = [
            'user_id' => auth()->user()->id,
            'body' => $this->getPostBodyAttribute($request, 'body'),
        ];

        $name = 'uploadedFiles';

        $files = $this->getPostFiles($request, $name);

        if (count($files)) {
            $this->validateMaxPostFilesSize($this->calculatePostFilesSize($post, explode('/', $request->input('removedPostFiles'))) + $this->calculateUploadedFilesSize($files[$name]));
        }

        $post->update($postAttributes);

        $post->deleteFilesByNames(explode('/', $request->input('removedPostFiles')));

        if (count($files)) {
            $post->saveFiles($files[$name]);
        }

        return redirect()->back()->with('message', 'The post has been edited.');
    }

    public function destroy(Post $post)
    {
        foreach ($post->subPosts as $postComment) {
            $postComment->delete();
        }

        $post->delete();

        if (url()->previous() !== route('post.edit', $post->id)) {
            return redirect()->back()->with('message', 'The post has been deleted.');
        } else {
            return redirect()->route('home')->with('message', 'The post has been deleted.');
        }
    }
}
