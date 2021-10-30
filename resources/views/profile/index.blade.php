<x-metadata title="TuneIn - Profile">
    <div id="preview" class="preview-container block"></div>

    <div class="left-sidebar-container block">
        <div class="left-sidebar-content block">
            <x-sidebar.link-button href="/home">Home</x-sidebar.link-button>
            <x-sidebar.link-button href="/bookmarks">Bookmarks</x-sidebar.link-button>
            <x-sidebar.link-button href="/explore">Explore</x-sidebar.link-button>
            <x-sidebar.link-button href="/profile">Profile</x-sidebar.link-button>
            <x-sidebar.form-button href="/logout">Log Out</x-sidebar.form-button>
        </div>
    </div>

    <div class="main-container block">
        <x-profile.background url="images/pfp.jpg" />

        <div class="profile-info">
            <x-profile.image url="images/pfp.jpg" />

            <div class="profile-name">MarkTuning</div>

            <div class="profile-username">marktuning</div>
        </div>

        <x-post.create profilePicture="images/pfp.jpg" username="{{ $user->username }}" route="{{ route('post.create') }}" />

        @foreach ($posts as $post)
            <x-post.panel profilePictureURL="images/pfp.jpg" profileName="{{ $post->author->username }}" contentId="postContent{{ $post->id }}">
                <x-post.dropdown>
                    <x-post.dropdown-link href="{{ route('post.edit', $post) }}">Edit</x-post.dropdown-link>
                    <x-post.dropdown-button href="{{ route('post.destroy', $post) }}" method="DELETE">Delete</x-post.dropdown-button>
                </x-post.dropdown>
                
                <div class="post-body-text">{{ $post->body }}</div>

                <script>
                    loadPostFiles({{ $post->id }}, "{{ $files[$post->id] }}");
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