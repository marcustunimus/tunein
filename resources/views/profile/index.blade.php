<x-metadata title="TuneInMedia - Profile">
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
        <x-profile.background url="storage/profile_backgrounds/{{ $user->background_picture }}" />

        <div class="profile-info">
            <x-profile.image url="storage/profile_pictures/{{ $user->profile_picture }}" />

            <div class="profile-name">{{ $user->name }}</div>

            <div class="profile-username">{{ $user->username }}</div>
        </div>

        <x-post.create profilePicture="storage/profile_pictures/{{ $user->profile_picture }}" username="{{ $user->username }}" route="{{ route('post.create') }}" />

        @foreach ($posts as $post)
            <x-post.panel profilePictureURL="storage/profile_pictures/{{ $post->author->profile_picture }}" profileName="{{ $post->author->username }}" contentId="postContent{{ $post->id }}">
                <x-post.dropdown>
                    <x-post.dropdown-link id="post-{{ $post->id }}-link" href="{{ route('home') }}">Copy Link</x-post.dropdown-link>
                    @if ($user != null)
                        @if ($post->author->id === $user->id)
                            <x-post.dropdown-link href="{{ route('post.edit', $post) }}">Edit</x-post.dropdown-link>
                            <x-post.dropdown-button href="{{ route('post.destroy', $post) }}" method="DELETE">Delete</x-post.dropdown-button>
                        @endif
                    @endif
                </x-post.dropdown>
                
                <div class="post-body-text">{{ $post->body }}</div>

                <script>
                    loadPostFiles({{ $post->id }}, "{{ $files[$post->id] }}", "{{ asset('') }}");
                </script>

                <x-post.interaction.info id="post-{{ $post->id }}-info">{{ $postLikes[$post->id]->count() }} likes</x-post.interaction.info> 

                <x-post.interaction.tab>
                    <x-post.interaction.button id="post-{{ $post->id }}-like" icon="{{ in_array($post->id, $userLikes) ? 'background-image: url(' . asset('/images/favorite_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/favorite_border_white_24dp.svg') . ');' }}"></x-post-interaction-button>
                        <x-post.interaction.button id="post-{{ $post->id }}-comment" icon="{{ 'background-image: url(' . asset('/images/comment_white_24dp.svg') . ');' }}">Comments</x-post-interaction-button>
                        <x-post.interaction.button id="post-{{ $post->id }}-share" icon="{{ 'background-image: url(' . asset('/images/share_white_24dp.svg') . ');' }}">Share</x-post-interaction-button>
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