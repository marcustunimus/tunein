<x-metadata title="TuneIn - View Post">
    <div class="left-sidebar-container block">
        <div class="left-sidebar-content block">
            <x-sidebar.link-button href="/home">Home</x-sidebar.link-button>
        </div>
    </div>

    <div class="main-container block">
        <x-post.panel profilePictureURL="images/pfp.jpg" profileName="{{ $post->author->username }}">
            <div>{{ $post->body }}</div>

            <x-post.file url="images/pfp.jpg" />
        </x-post.panel>
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content block">
        </div>
    </div>
</x-metadata>