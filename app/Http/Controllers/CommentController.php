<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TransformsPostFiles;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use TransformsPostFiles;

    public function index(Post $post, Request $request): string|false
    {
        $comments = $post->subPosts()->orderBy('created_at')->paginate(3)->withQueryString();

        $files = $this->getPostFilesForJs([$post]);

        $commentsFiles = $this->getPostFilesForJs($comments->items());

        $commentPageHtml = view('post.view', [
            'post' => $post,
            'comments' => $comments,
            'user' => auth()->user(),
            'files' => $files,
            'commentsFiles' => $commentsFiles,
        ])->render();

        return json_encode([$commentPageHtml, $comments->hasMorePages(), 'body'.$post->id, $request->input('bodyText'), $request->input('errorKey'), $request->input('errorValue')]);
    }

    public function getMore(Post $post): string|false
    {
        $comments = $post->subPosts()->orderBy('created_at')->paginate(3)->withQueryString();

        $commentsFiles = $this->getPostFilesForJs($comments->items());

        $commentsPageHtml = view('post.comments', [
            'user' => auth()->user(),
            'comments' => $comments,
            'commentsFiles' => $commentsFiles,
        ])->render();

        return json_encode([$commentsPageHtml, $comments->hasMorePages()]);
    }
}
