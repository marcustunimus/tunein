<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\CalculatesSize;
use App\Http\Controllers\Traits\TransformsPostFiles;
use App\Http\Controllers\Traits\TransformsPostLikes;
use App\Models\Like;
use App\Models\Post;
use App\Models\Bookmark;
use App\Models\PostFile;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    use TransformsPostFiles;
    use CalculatesSize;
    use TransformsPostLikes;

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
        $files = [];

        foreach (PostFile::query()->where('post_id', '=', $post->id)->get() as $postFile) {
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

        $postAttributes['comment_on_post'] = (!request('comment_on_post') ? null : $this->validatePostCommentId());

        if ($postAttributes['comment_on_post'] === 'Commenting on a comment is not allowed.' || $postAttributes['comment_on_post'] === 'Commenting on a post that does not exist is not possible.') {
            return redirect()->back()->with('message', $postAttributes['comment_on_post']);
        }

        if ($postAttributes['comment_on_post'] === null) {
            $files = request('uploadedFiles') ? $this->validateFiles(new Request($this->filterUploadedFiles(request('uploadedFiles')))) : [];
        } else {
            $files = request('uploadedFiles' . $postAttributes['comment_on_post']) ? $this->validateFiles(new Request($this->filterUploadedFiles(request('uploadedFiles' . $postAttributes['comment_on_post'])))) : [];
        }

        if (isset($files['file'])) {
            $this->validateMaxPostFilesSize($this->calculateUploadedFilesSize($files['file']));
        }

        $post = Post::create($postAttributes);

        $post = $post->fresh();

        if (count($files)) {
            $post->saveFiles($files['file']);
        }

        if ($postAttributes['comment_on_post'] != null) {
            return redirect()->back()->with('message', 'The comment has been created.')->with('postId', $postAttributes['comment_on_post']);
        } else {
            return redirect()->route('home')->with('message', 'The post has been created.');
        }
    }

    public function update(Post $post)
    {
        $postAttributes = $this->validatePostBody(new Post());

        $files = request('uploadedFiles') ? $this->validateFiles(new Request($this->filterUploadedFiles(request('uploadedFiles')))) : [];

        $this->validateMaxPostFilesSize($this->calculatePostFilesSize($post, explode('/', request('removedPostFiles'))) + (isset($files['file']) ? $this->calculateUploadedFilesSize($files['file']) : 0));

        $post->update($postAttributes);

        $post->deleteFilesByNames(explode('/', request('removedPostFiles')));

        if (count($files)) {
            $post->saveFiles($files['file']);
        }

        return redirect()->back()->with('message', 'The post has been edited.');
    }

    public function destroy(Post $post)
    {
        foreach ($post->subPosts as $postComment) {
            $postComment->remove();
        }

        $post->delete();

        if (url()->previous() !== route('post.edit', $post->id)) {
            return redirect()->back()->with('message', 'The post has been deleted.');
        } else {
            return redirect()->route('home')->with('message', 'The post has been deleted.');
        }
    }

    public function like(Post $post)
    {
        if (auth()->user() == null) {
            return json_encode("Login");
        }

        $likeAttributes = [
            'user_id' => auth()->user()->id,
            'post_id' => $post->id
        ];

        if ($post->isLikedByUser(auth()->user())) {
            $post->likes()->where('user_id', auth()->user()->id)->first()->delete();

            return json_encode("Unliked");
        }

        Like::create($likeAttributes);

        return json_encode("Liked");
    }

    public function bookmark(Post $post)
    {
        if (auth()->user() == null) {
            return json_encode("Login");
        }

        if ($post->comment_on_post !== null) {
            return json_encode("AttemptToBookmarkComment");
        }

        $bookmarkAttributes = [
            'user_id' => auth()->user()->id,
            'post_id' => $post->id
        ];

        if ($post->isBookmarkedByUser(auth()->user())) {
            $post->bookmarks()->where('user_id', auth()->user()->id)->first()->delete();

            return json_encode("Unbookmarked");
        }

        Bookmark::create($bookmarkAttributes);

        return json_encode("Bookmarked");
    }

    public function viewComments(Post $post)
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

        return json_encode([$commentPageHtml, $comments->hasMorePages()]);
    }

    public function viewMoreComments(Post $post)
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


    public function likesInfo(Post $post)
    {
        return json_encode($this->getPostLikesForJs($post));
    }

    

    protected function validatePostBody(?Post $post = null): array
    {
        $post ??= new Post();

        return request()->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);
    }

    protected function validatePostCommentId()
    {
        $commentOnPost = request('comment_on_post');

        $originalPost = Post::query()->where('id', '=', $commentOnPost)->first();

        if ($originalPost !== null) {
            if ($originalPost->comment_on_post !== null) {
                return 'Commenting on a comment is not allowed.';
            } else {
                return $commentOnPost;
            }
        } else {
            return 'Commenting on a post that does not exist is not possible.';
        }
    }

    protected function validateMaxPostFilesSize(int $size): void
    {
        $maxPostSize = 40; // Mbs

        if ($size > $maxPostSize * 1024 * 1024) {
            throw ValidationException::withMessages(['max_post_size' => 'The maximum post files size must not be larger than ' . $maxPostSize . ' Mbs.']);
        }
    }

    protected function filterUploadedFiles(array $files): array
    {
        $files = collect($files)->unique(function (UploadedFile $file) {
            return $file->getClientOriginalName() . $file->getMimeType() . $file->getSize();
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
}
