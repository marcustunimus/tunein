<x-metadata title="TuneInMedia - Profile">
    <div id="preview" class="preview-container block"></div>

    <div class="left-sidebar-container block">
        <div class="left-sidebar-content block">
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
        <x-profile.background url="{{ $user->background_picture }}" />

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

                <div class="profile-follow-form">
                    <div id="profile-{{ $user->username }}" class="profile-follow-button center link">{{ $userFollowed->count() ? 'Following' : 'Follow' }}</div>
                </div>
            </div>

            <div class="profile-username">{{ $user->username }}</div>

            <div class="profile-followers-count-text">
                <span id="profile-followers-count" class="link">{{ $userFollowers->count() }} {{ $userFollowers->count() === 1 ? 'follower' : 'followers' }}</span>
            </div>

            <script>
                setFollowButtonFunctionality("{{ $user->username }}", {{ $userFollowers->count() }}, "{{ asset('') }}");
                setPreviewFollowersButtonFunctionality("{{ $user->username }}", "{{ asset('') }}");
            </script>
        </div>

        @if (auth()->check())
            @if ($user->id === auth()->user()->id)
                <x-post.create profilePicture="{{ $user->profile_picture }}" username="{{ $user->username }}" route="{{ route('post.create') }}" />
            @endif
        @endif

        @foreach ($posts as $post)
            <x-post.panel profilePictureURL="{{ $post->author->profile_picture }}" profileName="{{ $post->author->username }}" contentId="postContent{{ $post->id }}">
                @if ($post->comment_on_post != null)
                    <div class="post-comment-header">This post is a comment to <a href="{{ route('view.post', $post->comment_on_post) }}" class="link link-color" target="_blank">this</a> post.</div>
                @endif

                <x-post.dropdown>
                    <x-post.dropdown-link id="post-{{ $post->id }}-link" href="{{ route('home') }}">Copy Link</x-post.dropdown-link>
                    @if (auth()->check())
                        @if ($post->author->id === auth()->user()->id)
                            <x-post.dropdown-link href="{{ route('post.edit', $post) }}">Edit</x-post.dropdown-link>
                            <x-post.dropdown-button href="{{ route('post.destroy', $post) }}" method="DELETE">Delete</x-post.dropdown-button>
                        @endif
                    @endif
                </x-post.dropdown>
                
                <div class="post-body-text">{{ $post->body }}</div>

                <script>
                    loadPostFiles({{ $post->id }}, "{{ $files[$post->id] }}", "{{ asset('') }}");
                </script>

                <x-post.interaction.info id="post-{{ $post->id }}-info">{{ $postLikes[$post->id]->count() }} {{ $postLikes[$post->id]->count() === 1 ? 'like' : 'likes' }}</x-post.interaction.info> 

                <x-post.interaction.tab>
                    <x-post.interaction.button id="post-{{ $post->id }}-like" icon="{{ in_array($post->id, $userLikes) ? 'background-image: url(' . asset('/images/favorite_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/favorite_border_white_24dp.svg') . ');' }}"></x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $post->id }}-comment" icon="{{ 'background-image: url(' . asset('/images/comment_white_24dp.svg') . ');' }}">Comments</x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $post->id }}-bookmark" icon="{{ in_array($post->id, $userBookmarks) ? 'background-image: url(' . asset('/images/bookmark_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/bookmark_border_white_24dp.svg') . ');' }}">Bookmark</x-post-interaction-button>
                </x-post.interaction.tab>

                <script>
                    setInteractionButtonsFunctionality({{ $post->id }}, {{ $postLikes[$post->id]->count() }}, "{{ asset('') }}");
                </script>
            </x-post.panel>
        @endforeach
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content block">
            <x-sidebar.search-bar href="/profile">Search...</x-sidebar.search-bar>
        </div>
    </div>
</x-metadata>