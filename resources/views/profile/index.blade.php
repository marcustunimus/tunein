<x-metadata title="Profile - TuneInMedia">
    <div id="preview" class="preview-container block"></div>
    <x-flash />

    <div class="left-sidebar-container block">
        <div class="left-sidebar-content scrollbar block">
            @if (auth()->check())
                <x-sidebar.link-button href="{{ route('home') }}">Home</x-sidebar.link-button>
                <x-sidebar.link-button href="{{ route('bookmarks') }}">Bookmarks</x-sidebar.link-button>
                <x-sidebar.link-button href="{{ route('explore') }}">Explore</x-sidebar.link-button>
                <x-sidebar.link-button href="{{ route('profile', auth()->user()->username) }}">Profile</x-sidebar.link-button>
                <x-sidebar.link-button href="{{ route('profile.settings', auth()->user()->username) }}">Settings</x-sidebar.link-button>
                <x-sidebar.form-button href="{{ route('logout') }}">Log Out</x-sidebar.form-button>
            @else
                <x-sidebar.link-button href="{{ route('home') }}">Home</x-sidebar.link-button>
            @endif
        </div>
    </div>

    <div class="main-container block">
        @if ($user->background_picture != null)
            <x-profile.background url="{{ $user->background_picture }}" />
        @endif

        <div class="profile-info">
            <x-profile.image url="{{ $user->profile_picture }}" />

            <div class="flex justify-end">
                <div class="profile-edit-text-container center">
                    @if (auth()->check())
                        @if ($user->id === auth()->user()->id)
                            <a href="{{ route('profile.settings', auth()->user()->username) }}" class="profile-edit-text link link-color">Edit</a>
                        @else
                            <div class="profile-edit-text"></div>
                        @endif
                    @else
                        <div class="profile-edit-text"></div>
                    @endif
                </div>
            </div>

            <div class="flex justify-between">
                <div class="profile-name">{{ $user->name }}</div>

                @if (auth()->check())
                    @if ($user->id !== auth()->user()->id)
                        <div class="profile-follow-form">
                            <div id="profile-{{ $user->username }}" class="profile-follow-button center link">{{ auth()->user()->isFollowing($user) ? 'Following' : 'Follow' }}</div>
                        </div>
                    @endif
                @endif
            </div>

            <div class="profile-username">{{ '@' . $user->username }}</div>

            <div class="profile-followers-count-text">
                <span id="profile-followers-count" class="link link-color">{{ $followersCount = $user->followers()->count() }} {{ \Illuminate\Support\Str::plural('follower', $followersCount) }}</span>
            </div>

            @if (auth()->check())
                @if ($user->id !== auth()->user()->id)
                    <script>
                        setFollowButtonFunctionality("{{ $user->username }}", {{ $followersCount }}, "{{ asset('') }}");
                        setPreviewFollowersButtonFunctionality("{{ $user->username }}", "{{ asset('') }}", document.getElementById('preview'));
                    </script>
                @else
                    <script>
                        setPreviewFollowersButtonFunctionality("{{ $user->username }}", "{{ asset('') }}", document.getElementById('preview'));
                    </script>
                @endif
            @else
                <script>
                    setPreviewFollowersButtonFunctionality("{{ $user->username }}", "{{ asset('') }}", document.getElementById('preview'));
                </script>
            @endif
        </div>

        @if (auth()->check())
            @if ($user->id === auth()->user()->id)
                <x-post.create profilePicture="{{ $user->profile_picture }}" username="{{ $user->username }}" route="{{ route('post.create') }}" />
            @endif
        @endif

        @if (! $posts->count())
            @if (auth()->check())
                @if ($user->id === auth()->user()->id)
                    <div class="no-posts-found-text">You have no posts currently.</div>
                @else
                    <div class="no-posts-found-text">This user has no posts currently.</div>
                @endif
            @else
                <div class="no-posts-found-text">This user has no posts currently.</div>
            @endif
        @endif

        @foreach ($posts as $post)
            <x-post.panel profilePictureURL="{{ $post->author->profile_picture }}" profileName="{{ $post->author->username }}" contentId="postContent{{ $post->id }}" timePassed="{{ $post->created_at->diffForHumans() }}">
                <x-post.dropdown containerClass="post-dropdown-container">
                    <x-post.dropdown-link id="post-{{ $post->id }}-link" href="{{ route('home') }}">Copy Link</x-post.dropdown-link>
                    @if (auth()->check())
                        @if ($post->author->id === auth()->user()->id)
                            <x-post.dropdown-link href="{{ route('post.edit', $post) }}">Edit</x-post.dropdown-link>
                            <x-post.dropdown-button href="{{ route('post.destroy', $post) }}" method="DELETE" id="delete{{ $post->id }}" elementPosition="last">Delete</x-post.dropdown-button>
                            <script>
                                deleteFormConfirmationFunctionality(document.getElementById("delete{{ $post->id }}Form"), document.getElementById('preview'), "{{ asset('') }}");
                            </script>
                        @endif
                    @endif
                </x-post.dropdown>

                <div class="post-body-text">{{ $post->body }}</div>

                <script>
                    loadPostFiles({{ $post->id }}, @json($files[$post->id]), "{{ asset('') }}", document.getElementById('preview'));
                </script>

                <x-post.interaction.info id="post-{{ $post->id }}-info">{{ $likesCount = $post->likes()->count() }} {{ \Illuminate\Support\Str::plural('like', $likesCount) }}</x-post.interaction.info>

                <x-post.interaction.tab>
                    <x-post.interaction.button id="post-{{ $post->id }}-like" icon="{{ $post->isLikedByUser(auth()->user()) ? 'background-image: url(' . asset('/images/favorite_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/favorite_border_white_24dp.svg') . ');' }}"></x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $post->id }}-comment" icon="{{ 'background-image: url(' . asset('/images/comment_white_24dp.svg') . ');' }}">{{ $post->subPosts()->count() }}</x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $post->id }}-bookmark" icon="{{ $post->isBookmarkedByUser(auth()->user()) ? 'background-image: url(' . asset('/images/bookmark_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/bookmark_border_white_24dp.svg') . ');' }}"></x-post-interaction-button>
                </x-post.interaction.tab>

                <script>
                    setInteractionButtonsFunctionality({{ $post->id }}, {{ $likesCount }}, "{{ asset('') }}", document.getElementById('preview'));
                </script>
            </x-post.panel>
        @endforeach

        <div class="mb-6">{{ $posts->links() }}</div>
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content scrollbar block">
            <x-sidebar.search-bar href="/profile">Search...</x-sidebar.search-bar>
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
