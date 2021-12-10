<x-metadata title="TuneInMedia - Bookmarks">
    <div id="preview" class="preview-container block"></div>

    <div class="left-sidebar-container block">
        <div class="left-sidebar-content block">
            <x-sidebar.link-button href="{{ route('home') }}">Home</x-sidebar.link-button>
            <x-sidebar.link-button href="{{ route('bookmarks') }}">Bookmarks</x-sidebar.link-button>
            <x-sidebar.link-button href="{{ route('explore') }}">Explore</x-sidebar.link-button>
            <x-sidebar.link-button href="{{ route('profile') }}">Profile</x-sidebar.link-button>
            <x-sidebar.link-button href="{{ route('profile.settings') }}">Settings</x-sidebar.link-button>
            <x-sidebar.form-button href="{{ route('logout') }}">Log Out</x-sidebar.form-button>
        </div>
    </div>

    <div class="main-container block">
        @foreach ($bookmarks as $bookmark)
            <x-post.panel profilePictureURL="storage/profile_pictures/{{ $bookmark->author->profile_picture }}" profileName="{{ $bookmark->author->username }}" contentId="postContent{{ $bookmark->id }}">
                <x-post.dropdown>
                    <x-post.dropdown-link id="post-{{ $bookmark->id }}-link" href="{{ route('home') }}">Copy Link</x-post.dropdown-link>
                    @if ($user != null)
                        @if ($bookmark->author->id === $user->id)
                            <x-post.dropdown-link href="{{ route('post.edit', $bookmark) }}">Edit</x-post.dropdown-link>
                            <x-post.dropdown-button href="{{ route('post.destroy', $bookmark) }}" method="DELETE">Delete</x-post.dropdown-button>
                        @endif
                    @endif
                </x-post.dropdown>
                
                <div class="post-body-text">{{ $bookmark->body }}</div>

                <script>
                    loadPostFiles({{ $bookmark->id }}, "{{ $files[$bookmark->id] }}", "{{ asset('') }}");
                </script>

                <x-post.interaction.info id="post-{{ $bookmark->id }}-info">{{ $postLikes[$bookmark->id]->count() }} likes</x-post.interaction.info> 

                <x-post.interaction.tab>
                    <x-post.interaction.button id="post-{{ $bookmark->id }}-like" icon="{{ in_array($bookmark->id, $userLikes) ? 'background-image: url(' . asset('/images/favorite_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/favorite_border_white_24dp.svg') . ');' }}"></x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $bookmark->id }}-comment" icon="{{ 'background-image: url(' . asset('/images/comment_white_24dp.svg') . ');' }}">Comments</x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $bookmark->id }}-bookmark" icon="{{ in_array($bookmark->id, $userBookmarks) ? 'background-image: url(' . asset('/images/bookmark_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/bookmark_border_white_24dp.svg') . ');' }}">Bookmark</x-post-interaction-button>
                </x-post.interaction.tab>

                <script>
                    setInteractionButtonsFunctionality({{ $bookmark->id }}, {{ $postLikes[$bookmark->id]->count() }}, "{{ asset('') }}");
                </script>
            </x-post.panel>
        @endforeach 
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content block">
            <x-sidebar.search-bar href="{{ route('bookmarks') }}">Search...</x-sidebar.search-bar>
        </div>
    </div>
</x-metadata>