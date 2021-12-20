<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\Bookmark;
use App\Models\PostFile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    public function index(Post $post)
    {
        $comments = Post::query()->where('comment_on_post', '=', $post->id)->get();

        $files=[];

        $postFiles = [];

        foreach ($post->files as $postFile) {
            array_push($postFiles, 
                $postFile->file, 
                Storage::mimeType('public/post_files/' . $postFile->file), 
                Storage::size('public/post_files/' . $postFile->file)
            );
        }

        $files[$post->id] = implode('|', $postFiles);

        $postLikes = $this::getLikesOfPosts([$post]);

        $userLikes = $this::getUserLikedPosts($postLikes);

        $postBookmarks = PostController::getBookmarksOfPosts([$post]);

        $userBookmarks = PostController::getUserBookmarkedPosts($postBookmarks);

        $commentsFiles = $this::getPostsFiles($comments);

        $commentsLikes = $this::getLikesOfPosts($comments);

        $userCommentsLikes = $this::getUserLikedPosts($postLikes);

        $commentsBookmarks = PostController::getBookmarksOfPosts($comments);

        $userCommentsBookmarks = PostController::getUserBookmarkedPosts($postBookmarks);
        
        return view('post.index', [
            'post' => $post,
            'user' => auth()->user(),
            'files' => $files,
            'postLikes' => $postLikes,
            'userLikes' => $userLikes,
            'postBookmarks' => $postBookmarks,
            'userBookmarks' => $userBookmarks,
            'comments' => $comments,
            'commentsFiles' => $commentsFiles,
            'commentsLikes' => $commentsLikes,
            'userCommentsLikes' => $userCommentsLikes,
            'commentsBookmarks' => $commentsBookmarks,
            'userCommentsBookmarks' => $userCommentsBookmarks,
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
        
        $postAttributes['comment_on_post'] = (!request('comment_on_post') ? null : $this->validatePostCommentId()['comment_on_post']);

        $files = request('uploadedFiles') ? $this->validateFiles(new Request($this->filterUploadedFiles(request('uploadedFiles')))) : [];

        if (isset($files['file'])) {
            $this->validateMaxPostFilesSize($this->getAllUploadedFilesSize($files['file']));
        }

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

        $this->validateMaxPostFilesSize($this->getAllPostFilesSize($post, explode('/', request('removedPostFiles'))) + (isset($files['file'])) ? $this->getAllUploadedFilesSize($files['file']) : 0);

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



    public function likesInfo(Post $post) {
        $postLikes = $this::getLikesOfPosts([$post]);

        $postLikesInStringFormat = $this::convertLikesOfPostsToStringFormats($postLikes);

        return json_encode($postLikesInStringFormat);
    }



    public static function getLikesOfPosts($posts): array
    {
        $postsLikes = [];

        foreach ($posts as $post) {
            $likes = Like::query()->where('post_id', $post->id)->get();

            $postsLikes[$post->id] = $likes;
        }
        
        return $postsLikes;
    }

    public static function convertLikesOfPostsToStringFormats($postLikes): array
    {
        $postLikesInStringFormat = [];
        $tempCount = 0;

        foreach($postLikes as $key => $postLike) {
            $temp = "";

            foreach($postLike as $like) {
                $tempCount++;
                $temp .= implode('|', [$like->user->profile_picture, $like->user->username]) . ($tempCount !== $postLike->count() ? '|' : "");
            }

            $postLikesInStringFormat[$key] = $temp;
        }

        return $postLikesInStringFormat;
    }

    public static function getUserLikedPosts($postsLikes): array
    {
        $userLikedPosts = [];

        foreach ($postsLikes as $id => $postLikes) {
            foreach ($postLikes as $like) {
                if (auth()->user() != null) {
                    if ($like->user_id === auth()->user()->id) {
                        array_push($userLikedPosts, $id);
                    }
                }
            }
        }

        return $userLikedPosts;
    }

    public static function getBookmarksOfPosts($posts): array
    {
        $postsBookmarks = [];

        foreach ($posts as $post) {
            $bookmarks = Bookmark::query()->where('post_id', $post->id)->get();

            $postsBookmarks[$post->id] = $bookmarks;
        }
        
        return $postsBookmarks;
    }

    public static function convertBookmarksOfPostsToStringFormats($postBookmarks): array
    {
        $postBookmarksInStringFormat = [];
        $tempCount = 0;

        foreach($postBookmarks as $key => $postBookmark) {
            $temp = "";

            foreach($postBookmark as $bookmark) {
                $tempCount++;
                $temp .= implode('|', [$bookmark->user->id, $bookmark->user->username]) . ($tempCount !== $postBookmark->count() ? '|' : "");
            }

            $postBookmarksInStringFormat[$key] = $temp;
        }

        return $postBookmarksInStringFormat;
    }

    public static function getUserBookmarkedPosts($postsBookmarks): array
    {
        $userBookmarkedPosts = [];

        foreach ($postsBookmarks as $id => $postBookmarks) {
            foreach ($postBookmarks as $bookmark) {
                if (auth()->user() != null) {
                    if ($bookmark->user_id === auth()->user()->id) {
                        array_push($userBookmarkedPosts, $id);
                    }
                }
            }
        }

        return $userBookmarkedPosts;
    }

    public static function getPostsFiles($posts): array
    {
        $files = [];

        foreach ($posts as $post) {
            $postFiles = [];

            foreach ($post->files as $postFile) {
                array_push($postFiles, 
                    $postFile->file, 
                    Storage::mimeType('public/post_files/' . $postFile->file), 
                    Storage::size('public/post_files/' . $postFile->file)
                );
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

    protected function validatePostCommentId(?Post $post = null): array
    {
        $post ??= new Post();

        return request()->validate([
            'comment_on_post' => ['state' => 'exists:posts,id']
        ]);
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
            return $file->getClientOriginalName().$file->getMimeType().$file->getSize();
        })->values()->toArray();

        return ['file' => $files];
    }

    protected function getAllUploadedFilesSize(array $files): int
    {
        $size = 0;

        foreach($files as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    protected function getAllPostFilesSize(Post $post, array $removedPostFiles): int
    {
        $size = 0;

        foreach (PostFile::query()->where('post_id', '=', $post->id)->get() as $postFile) {
            if (! in_array($postFile->file, $removedPostFiles, true)) {
                $size += Storage::size('public/post_files/' . $postFile->file);
            }
        }

        return $size;
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