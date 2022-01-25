<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\Bookmark;
use App\Models\PostFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    public function index(Post $post)
    {
        $comments = $post->subPosts()->latest()->paginate(3)->withQueryString();

        $files = [];
        $postFiles = [];

        foreach ($post->files as $postFile) {
            array_push($postFiles,
                $postFile->file,
                Storage::mimeType('public/post_files/' . $postFile->file),
                Storage::size('public/post_files/' . $postFile->file)
            );
        }

        $files[$post->id] = implode('|', $postFiles);

        $commentsFiles = $this::getPostsFiles($comments);

        return view('post.index', [
            'post' => $post,
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
            $this->validateMaxPostFilesSize($this->getAllUploadedFilesSize($files['file']));
        }

        Post::create($postAttributes);

        if (count($files)) {
            $lasestPostId = Post::latest('id')->first()->id;

            $this->createPostFiles($lasestPostId, $files);
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

        $this->validateMaxPostFilesSize($this->getAllPostFilesSize($post, explode('/', request('removedPostFiles'))) + (isset($files['file']) ? $this->getAllUploadedFilesSize($files['file']) : 0));

        $post->update($postAttributes);

        $this->removePostFiles($post->id, explode('/', request('removedPostFiles')));

        if (count($files)) {
            $this->createPostFiles($post->id, $files);
        }

        return redirect()->back()->with('message', 'The post has been edited.');
    }

    public function destroy(Post $post)
    {
        $id = $post->id;

        $postComments = Post::query()->where('comment_on_post', '=', $post->id)->get();

        foreach ($postComments as $postComment) {
            $this->deletePost($postComment);
        }

        $this->deletePost($post);

        if (url()->previous() !== route('post.edit', $id)) {
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

        $likedPost = Like::query()->where([
            ['user_id', '=', $likeAttributes['user_id']],
            ['post_id', '=', $likeAttributes['post_id']]
        ])->get()->first();

        if ($likedPost) {
            $likedPost->delete();

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

        $bookmarkedPost = Bookmark::query()->where([
            ['user_id', '=', $bookmarkAttributes['user_id']],
            ['post_id', '=', $bookmarkAttributes['post_id']]
        ])->get()->first();

        if ($bookmarkedPost) {
            $bookmarkedPost->delete();

            return json_encode("Unbookmarked");
        }

        Bookmark::create($bookmarkAttributes);

        return json_encode("Bookmarked");
    }

    public function viewComments(Post $post)
    {
        $comments = $post->subPosts()->orderBy('created_at')->paginate(3)->withQueryString();

        $files = [];

        $postFiles = [];

        foreach ($post->files as $postFile) {
            array_push($postFiles,
                $postFile->file,
                Storage::mimeType('public/post_files/' . $postFile->file),
                Storage::size('public/post_files/' . $postFile->file)
            );
        }

        $files[$post->id] = implode('|', $postFiles);

        $commentsFiles = $this::getPostsFiles($comments);

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
        $comments = Post::query()->where('comment_on_post', '=', $post->id)->orderBy('created_at')->paginate(3)->withQueryString();

        $allComments = Post::query()->where('comment_on_post', '=', $post->id)->get();

        $commentsFiles = $this::getPostsFiles($comments);

        $commentsLikes = $this::getLikesOfPosts($comments);

        $userCommentsLikes = $this::getUserLikedPosts($commentsLikes);

        $commentsPageHtml = view('post.comments', [
            'user' => auth()->user(),
            'comments' => $comments,
            'commentsFiles' => $commentsFiles,
            'commentsLikes' => $commentsLikes,
            'userCommentsLikes' => $userCommentsLikes,
        ])->render();

        return json_encode([$commentsPageHtml, $allComments->count() === 3 * request('page') && $allComments->count() === $comments->count() * request('page') ? $comments->count() - 1 : $comments->count()]);
    }


    public function likesInfo(Post $post)
    {
        $postLikes = $this::getLikesOfPosts([$post]);

        $postLikesInStringFormat = $this::convertLikesOfPostsToStringFormats($postLikes);

        return json_encode($postLikesInStringFormat);
    }


    public static function convertLikesOfPostsToStringFormats($postLikes): array
    {
        $postLikesInStringFormat = [];
        $tempCount = 0;

        foreach ($postLikes as $key => $postLike) {
            $temp = "";

            foreach ($postLike as $like) {
                $tempCount++;
                $temp .= implode('|', [$like->user->profile_picture, $like->user->username]) . ($tempCount !== $postLike->count() ? '|' : "");
            }

            $postLikesInStringFormat[$key] = $temp;
        }

        return $postLikesInStringFormat;
    }

    public static function convertBookmarksOfPostsToStringFormats($postBookmarks): array
    {
        $postBookmarksInStringFormat = [];
        $tempCount = 0;

        foreach ($postBookmarks as $key => $postBookmark) {
            $temp = "";

            foreach ($postBookmark as $bookmark) {
                $tempCount++;
                $temp .= implode('|', [$bookmark->user->id, $bookmark->user->username]) . ($tempCount !== $postBookmark->count() ? '|' : "");
            }

            $postBookmarksInStringFormat[$key] = $temp;
        }

        return $postBookmarksInStringFormat;
    }

    /**
     * @param Collection|Post[] $posts
     * @return array
     */
    public static function getPostsFiles(Collection|array $posts): array
    {
        $files = [];

        foreach ($posts as $post) {
            $postFiles = [];

            foreach ($post->files as $postFile) {
                $postFiles[] = [
                    'path' => $postFile->file,
                    'mime_type' => $postFile->getMimeType(),
                    'size' => $postFile->getSize(),
                ];
            }

            $files[$post->id] = implode('|', $postFiles);
        }

        return $files;
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
        $files = collect($files);

        $files = $files->unique(function (UploadedFile $file) {
            return $file->getClientOriginalName() . $file->getMimeType() . $file->getSize();
        })->values()->toArray();

        return ['file' => $files];
    }

    protected function getAllUploadedFilesSize(array $files): int
    {
        $size = 0;

        foreach ($files as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    protected function getAllPostFilesSize(Post $post, array $removedPostFiles): int
    {
        $size = 0;

        foreach (PostFile::query()->where('post_id', '=', $post->id)->get() as $postFile) {
            if (!in_array($postFile->file, $removedPostFiles, true)) {
                $size += Storage::size('public/post_files/' . $postFile->file);
            }
        }

        return $size;
    }

    protected function removePostFiles(int $postId, array $removedPostFiles)
    {
        if (!$removedPostFiles) {
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
        foreach ($files as $allFiles) {
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

    protected function deletePost(Post $post)
    {
        $postFiles = PostFile::query()->where('post_id', '=', $post->id)->get();

        $postFilesNames = [];

        foreach ($postFiles as $postFile) {
            $postFilesNames[] = $postFile->file;
        }

        Storage::delete(array_map(function ($value) {
            return 'public/post_files/' . $value;
        }, $postFilesNames));

        // TODO: REFACTOR!

        PostFile::query()->where('post_id', '=', $post->id)->delete();

        Like::query()->where('post_id', '=', $post->id)->delete();

        Bookmark::query()->where('post_id', '=', $post->id)->delete();

        $post->delete();
    }
}
