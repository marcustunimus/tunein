<x-metadata title="TuneIn - View Post">
    <div id="preview" class="preview-container block"></div>
    
    <div class="left-sidebar-container block">
        <div class="left-sidebar-content block">
            <x-sidebar.link-button href="/home">Home</x-sidebar.link-button>
        </div>
    </div>

    <div class="main-container block">
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
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content block">
        </div>
    </div>
</x-metadata>