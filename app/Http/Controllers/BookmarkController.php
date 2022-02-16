<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TransformsPostFiles;
use App\Models\Post;
use App\Models\Bookmark;
use Illuminate\Contracts\Auth\Factory;

class BookmarkController extends Controller
{
    use TransformsPostFiles;

    public function index(Factory $auth)
    {
        $search = request()->query('search');

        $bookmarksQuery = Bookmark::query()->select('post_id')->where('user_id', '=', $auth->guard()->user()->id);

        if ($search) {
            $bookmarksQuery = $bookmarksQuery->whereHas('post', function ($query) use ($search) {
                return $query->where('body', 'like', '%' . $search . '%');
            });
        }

        $posts = Post::query()->whereIn('id', $bookmarksQuery)->latest()->paginate(10)->withQueryString();

        $files = $this->getPostFilesForJs($posts->items());

        return view('bookmarks.index', [
            'bookmarks' => $posts,
            'user' => $auth->guard()->user(),
            'files' => $files,
        ]);
    }

    public function store(Post $post): string|false
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

        if (! $post->isBookmarkedByUser(auth()->user())) {
            Bookmark::create($bookmarkAttributes);
        }

        return json_encode("Bookmarked");
    }

    public function destroy(Post $post): string|false
    {
        if (auth()->user() == null) {
            return json_encode("Login");
        }

        if ($post->isBookmarkedByUser(auth()->user())) {
            $post->bookmarks()->where('user_id', auth()->user()->id)->first()->delete();
        }

        return json_encode("Unbookmarked");
    }
}
