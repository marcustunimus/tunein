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
        $comments = $post->subPosts()->orderBy('created_at')->paginate(10)->withQueryString();

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

    public function edit(Post $anyPost)
    {
        return view('post.edit', [
            'post' => $anyPost,
            'files' => $this->getPostFilesForJs([$anyPost]),
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

    public function update(Post $anyPost, Request $request)
    {
        $postAttributes = [
            'user_id' => auth()->user()->id,
            'body' => $this->getPostBodyAttribute($request, 'body'),
        ];

        $name = 'uploadedFiles';

        $files = $this->getPostFiles($request, $name);

        if (count($files)) {
            $this->validateMaxPostFilesSize($this->calculatePostFilesSize($anyPost, explode('/', $request->input('removedPostFiles'))) + $this->calculateUploadedFilesSize($files[$name]));
        }

        $anyPost->update($postAttributes);

        $anyPost->deleteFilesByNames(explode('/', $request->input('removedPostFiles')));

        if (count($files)) {
            $anyPost->saveFiles($files[$name]);
        }

        return redirect()->route('view.post', $anyPost->comment_on_post === null ? $anyPost->id : Post::query()->where('id', $anyPost->comment_on_post)->first()->id)
                ->with('message', 'The ' . ($anyPost->comment_on_post === null ? 'post' : 'comment') . ' has been edited.');
    }

    public function destroy(Post $anyPost)
    {
        foreach ($anyPost->subPosts as $postComment) {
            $postComment->delete();
        }

        $anyPost->delete();

        if (url()->previous() !== route('post.edit', $anyPost->id)) {
            return redirect()->back()->with('message', 'The post has been deleted.');
        } else {
            return redirect()->route('home')->with('message', 'The post has been deleted.');
        }
    }
}
