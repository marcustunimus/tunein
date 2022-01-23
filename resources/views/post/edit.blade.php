<x-metadata title="Edit Post - TuneInMedia">
    <div id="preview" class="preview-container block"></div>
    <x-flash />

    <div class="left-sidebar-container block">
        <div class="left-sidebar-content scrollbar block">
            <x-sidebar.link-button href="{{ route('home') }}">Back to Home</x-sidebar.link-button>
        </div>
    </div>

    <div class="edit-post-page">
        <x-post.edit heading="Edit Post" route="{{ route('post.update', $post) }}" body="{{ $post->body }}" files="{{ $files }}" />
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content scrollbar block">
            <x-sidebar.form-button href="{{ route('post.destroy', $post) }}" method="DELETE" id="delete{{ $post->id }}">Delete</x-sidebar.form-button>
            <script>
                deleteFormConfirmationFunctionality(document.getElementById("delete{{ $post->id }}Form"), document.getElementById('preview'), "{{ asset('') }}");
            </script>
        </div>
    </div>

    @if (session('postId'))
        <script>
            viewCommentsWindow("{{ asset('') }}", {{ session('postId') }}, false);
        </script>
    @endif
</x-metadata>