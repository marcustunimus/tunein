<x-metadata title="TuneIn - Edit Post">
    <div id="preview" class="preview-container block"></div>

    <div class="left-sidebar-container block">
        <div class="left-sidebar-content block">
            <x-sidebar.link-button href="/home">Back to Home</x-sidebar.link-button>
        </div>
    </div>

    <div class="edit-post-page">
        <div class="edit-post-container">
            <div class="edit-post-heading-text center">Edit Post</div>

            <form method="POST" action="{{ route('post.update', $post) }}" enctype="multipart/form-data">
                @csrf
                
                @method('PATCH')

                <x-form.textarea 
                    name="body" 
                    type="text" 
                    class="post-input-text scrollbar center" 
                    containerClass="post-input-container" 
                    rows="7" 
                    required="true" 
                    placeholder="What's happening?"
                >
                    {{ $post->body }}
                </x-form.textarea>

                <div id="post-files" class="post-files-upload-thumbnails-container"></div>

                <x-form.file 
                    name="file" 
                    class="post-file-upload center-text" 
                    containerClass="post-file-upload-container" 
                    multiple="true" 
                    accept=".png,.jpeg,.jpg,.gif,.mp4,.webm"
                >
                    Attach files...
                </x-form.input>

                <script type="text/javascript" src="{{ asset('/js/create-post.js') }}"></script>
                <script type="text/javascript" src="{{ asset('/js/edit-post.js') }}"></script>

                <script>
                    showPostFilesPreview('{{ $files }}');
                    showUploadedFilesPreview('file');
                </script>

                <div class="post-submit-container block">
                    <x-form.submit class="post-button center link">Save</x-form.submit>
                </div>
            </form>
        </div>
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content block">
            <x-sidebar.form-button href="{{ route('post.destroy', $post) }}" method="DELETE">Delete</x-sidebar.form-button>
        </div>
    </div>
</x-metadata>