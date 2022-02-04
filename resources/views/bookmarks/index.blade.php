<x-metadata title="Bookmarks - TuneInMedia">
    <div id="preview" class="preview-container block"></div>
    <x-flash />

    <div class="left-sidebar-container block">
        <div class="left-sidebar-content scrollbar block">
            <x-sidebar.link-button href="{{ route('home') }}">Home</x-sidebar.link-button>
            <x-sidebar.link-button href="{{ route('bookmarks') }}">Bookmarks</x-sidebar.link-button>
            <x-sidebar.link-button href="{{ route('explore') }}">Explore</x-sidebar.link-button>
            <x-sidebar.link-button href="{{ route('profile', auth()->user()->username) }}">Profile</x-sidebar.link-button>
            <x-sidebar.link-button href="{{ route('profile.settings', auth()->user()->username) }}">Settings</x-sidebar.link-button>
            <x-sidebar.form-button href="{{ route('logout') }}">Log Out</x-sidebar.form-button>
        </div>
    </div>

    <div class="main-container block">
        <div class="heading-text center">Bookmarks</div>

        @if (! $bookmarks->count())
            <div class="no-posts-found-text">You have no bookmarks currently.</div>
        @endif

        @foreach ($bookmarks as $bookmark)
            <x-post.panel profilePictureURL="{{ $bookmark->author->profile_picture }}" profileName="{{ $bookmark->author->username }}" contentId="postContent{{ $bookmark->id }}" timePassed="{{ $bookmark->created_at->diffForHumans() }}">
                @if ($bookmark->comment_on_post != null)
                    <div class="post-comment-header">This post is a comment to <a href="{{ route('view.post', $bookmark->comment_on_post) }}" class="link link-color" target="_blank">this</a> post.</div>
                @endif
                
                <x-post.dropdown containerClass="post-dropdown-container">
                    <x-post.dropdown-link id="post-{{ $bookmark->id }}-link" href="{{ route('home') }}">Copy Link</x-post.dropdown-link>
                    @if ($user != null)
                        @if ($bookmark->author->id === $user->id)
                            <x-post.dropdown-link href="{{ route('post.edit', $bookmark) }}">Edit</x-post.dropdown-link>
                            <x-post.dropdown-button href="{{ route('post.destroy', $bookmark) }}" method="DELETE" id="delete{{ $bookmark->id }}" elementPosition="last">Delete</x-post.dropdown-button>
                            <script>
                                deleteFormConfirmationFunctionality(document.getElementById("delete{{ $bookmark->id }}Form"), document.getElementById('preview'), "{{ asset('') }}");
                            </script>
                        @endif
                    @endif
                </x-post.dropdown>
                
                <div class="post-body-text">{{ $bookmark->body }}</div>

                <script>
                    loadPostFiles({{ $bookmark->id }}, @json($files[$bookmark->id]), "{{ asset('') }}", document.getElementById('preview'));
                </script>

                <x-post.interaction.info id="post-{{ $bookmark->id }}-info">{{ $likesCount = $bookmark->likes()->count() }} {{ \Illuminate\Support\Str::plural('like', $likesCount) }}</x-post.interaction.info> 

                <x-post.interaction.tab>
                    <x-post.interaction.button id="post-{{ $bookmark->id }}-like" icon="{{ $bookmark->isLikedByUser($user) ? 'background-image: url(' . asset('/images/favorite_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/favorite_border_white_24dp.svg') . ');' }}"></x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $bookmark->id }}-comment" icon="{{ 'background-image: url(' . asset('/images/comment_white_24dp.svg') . ');' }}">{{ $bookmark->subPosts()->count() }}</x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $bookmark->id }}-bookmark" icon="{{ $bookmark->isBookmarkedByUser($user) ? 'background-image: url(' . asset('/images/bookmark_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/bookmark_border_white_24dp.svg') . ');' }}"></x-post-interaction-button>
                </x-post.interaction.tab>

                <script>
                    setInteractionButtonsFunctionality({{ $bookmark->id }}, {{ $likesCount }}, "{{ asset('') }}", document.getElementById('preview'));
                </script>
            </x-post.panel>
        @endforeach

        <div class="mb-6">{{ $bookmarks->links() }}</div>
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content scrollbar block">
            <x-sidebar.search-bar href="{{ route('bookmarks') }}">Search...</x-sidebar.search-bar>
        </div>
    </div>

    @if (session('postId'))
        <script>
            viewCommentsWindow("{{ asset('') }}", {{ session('postId') }});
        </script>
    @endif

    @if ($errors->any())
        @foreach ($errors->keys() as $key)
            @if (str_contains($key, "uploadedFiles") && strlen(substr(str_replace("uploadedFiles", "", $key), 0, strlen(str_replace("uploadedFiles", "", $key)) - 2)) > 0)
            <script>
                viewCommentsWindow("{{ asset('') }}", {{ $postId = substr(str_replace("uploadedFiles", "", $key), 0, strlen(str_replace("uploadedFiles", "", $key)) - 2) }}, "uploadedFiles{{ $postId }}Container", "{{ $errors->first($key) }}", "{{ old('body' . $postId) }}");
            </script>
            @elseif (str_contains($key, "body") && strlen(substr(str_replace("body", "", $key), 0, strlen(str_replace("body", "", $key)))) > 0)
            <script>
                viewCommentsWindow("{{ asset('') }}", {{ $postId = substr(str_replace("body", "", $key), 0, strlen(str_replace("body", "", $key))) }}, "body{{ $postId }}Container", "{{ $errors->first($key) }}", "{{ old('body' . $postId) }}");
            </script>
            @endif
        @endforeach
    @endif
</x-metadata>