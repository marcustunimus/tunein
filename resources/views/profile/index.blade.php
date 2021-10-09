<x-metadata title="TuneIn - Profile">
    <div class="left-sidebar-container block">
        <div class="left-sidebar-content block">
            <x-sidebar.link-button href="/home">Home</x-sidebar.link-button>
            <x-sidebar.link-button href="/bookmarks">Bookmarks</x-sidebar.link-button>
            <x-sidebar.link-button href="/explore">Explore</x-sidebar.link-button>
            <x-sidebar.link-button href="/profile">Profile</x-sidebar.link-button>
            <x-sidebar.link-button href="/post/create">Post</x-sidebar.link-button>
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

        @foreach ($posts as $post)
            <x-post.panel profilePictureURL="images/pfp.jpg" profileName="{{ $post->author->username }}">
                <div>{{ $post->body }}</div>

                <x-post.file url="images/pfp.jpg" />
            </x-post.panel>
        @endforeach

        {{-- @foreach ($posts as $post)
            <x-post.panel profilePictureURL="{{ $post->author->profile_picture }}" profileName="{{ $post->author->username }}">
                <div>{{ $post->body }}</div>

                <x-post.file url="{{ $post->attached_file }}" />
            </x-post.panel>
        @endforeach --}}
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content block">
            <x-sidebar.search-bar href="/profile">Search...</x-sidebar.search-bar>
        </div>
    </div>
</x-metadata>