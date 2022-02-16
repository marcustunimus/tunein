<x-metadata title="Edit Post - TuneInMedia">
    <div id="preview" class="preview-container block"></div>
    <x-flash />

    <div class="left-sidebar-container block">
        <div class="left-sidebar-content scrollbar block">
            <x-sidebar.link-button href="{{ route('home') }}">Home</x-sidebar.link-button>
        </div>
    </div>

    <div class="edit-post-page">
        <div class="edit-post-container">
            <div class="edit-post-heading-text center">Edit {{ $post->comment_on_post === null ? 'Post' : 'Comment' }}</div>
        
            <form id="editPostForm" method="POST" action="{{ route('post.update', $post) }}" enctype="multipart/form-data">
                @csrf
                
                @method('PATCH')
        
                <x-form.textarea name="body" type="text" class="post-input-text scrollbar center" containerClass="post-input-container" rows="7" required="true" placeholder="What's happening?">
                    {{ $post->body }}
                </x-form.textarea>
        
                <x-form.file name="uploadedFiles" class="post-file-upload-image-thumbnail-container" containerClass="post-file-upload-container" multiple="true" accept=".png,.jpeg,.jpg,.gif,.mp4,.webm">
                    <div class="post-file-upload-image-thumbnail post-file-upload-image-thumbnail-border field-theme-color-hover center link">
                        <img src="{{ asset('/images/add_white_24dp.svg') }}" style="width: 2rem;">
                    </div>
                </x-form.file>
        
                <div class="post-submit-container block mt-12">
                    <x-form.submit class="post-button center link" id="submitEditPostForm">Save</x-form.submit>
                </div>
        
                <script>
                    autoResizeTextAreas();
                    showPostFilesPreview(@json($files[$post->id]), "{{ asset('') }}", document.getElementById('preview'), document.getElementById('post-files'));
                    showUploadedFilesPreview(document.getElementById("uploadedFiles"), "{{ asset('') }}", document.getElementById('preview'), document.getElementById('uploads'), document.getElementById('post-files'), "uploadedFiles");
                    addFilesToForm("editPostForm", 'uploads', document.getElementById("uploadedFiles"));
                </script>
            </form>
        </div>
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content scrollbar block">
            <x-sidebar.form-button href="{{ route('post.destroy', $post) }}" method="DELETE" id="delete{{ $post->id }}" postType="{{ $post->comment_on_post === null ? 'post' : 'comment' }}">Delete</x-sidebar.form-button>
            <script>
                deleteFormConfirmationFunctionality(document.getElementById("delete{{ $post->id }}Form"), document.getElementById('preview'), "{{ asset('') }}");
            </script>
        </div>
    </div>

    @if (session('postId'))
        <script>
            viewCommentsWindow("{{ asset('') }}", {{ session('postId') }});
        </script>
    @endif
</x-metadata>