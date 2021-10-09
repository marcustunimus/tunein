<x-metadata title="TuneIn - Create Post">
    <div class="left-sidebar-container block">
        <div class="left-sidebar-content block">
            <x-sidebar.link-button href="/home">Back to Home</x-sidebar.link-button>
        </div>
    </div>

    <div class="main-container block">
        <div class="edit-post-heading-text center">Edit Post</div>

        <form method="POST" action="/post/create" enctype="multipart/form-data">
            @csrf

            <x-form.textarea name="body" type="text" class="post-input-text scrollbar center" containerClass="post-input-container" rows="8" required="true" placeholder="What's happening?" />
            <x-form.file name="file" class="post-file-upload center-text" containerClass="post-file-upload-container" multiple="true" accept="video/*,image/*">Choose files...</x-form.input>

            <script type="text/javascript" src="{{ asset('/js/create-post.js') }}"></script>

            <script>
                showUploadedFilesPreview('file');
            </script>

            <div class="post-submit-container block">
                <x-form.submit class="post-button center link">Save</x-form.submit>
            </div>
        </form>
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content block">
        </div>
    </div>
</x-metadata>