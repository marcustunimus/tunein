<x-metadata title="TuneInMedia - Edit Post">
    <div id="preview" class="preview-container block"></div>
    <x-flash />

    <div class="left-sidebar-container block">
        <div class="left-sidebar-content block">
            <x-sidebar.link-button href="{{ route('home') }}">Back to Home</x-sidebar.link-button>
        </div>
    </div>

    <div class="edit-post-page">
        <x-post.edit heading="Edit Post" route="{{ route('post.update', $post) }}" body="{{ $post->body }}" files="{{ $files }}" />
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content block">
            <x-sidebar.form-button href="{{ route('post.destroy', $post) }}" method="DELETE">Delete</x-sidebar.form-button>
        </div>
    </div>
</x-metadata>