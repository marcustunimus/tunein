@props(['profilePicture', 'username', 'route'])

<x-post.panel profilePictureURL="{{ $profilePicture }}" profileName="{{ $username }}">
    <form id="createPostForm" method="POST" action="{{ $route }}" enctype="multipart/form-data">
        @csrf

        <x-form.textarea name="body" type="text" class="post-input-text scrollbar center" containerClass="post-input-container" rows="5" required="true" placeholder="What's happening?" />
        <x-form.file name="uploadedFiles" class="post-file-upload center-text" containerClass="post-file-upload-container" multiple="true" accept=".png,.jpeg,.jpg,.gif,.mp4,.webm">Attach files...</x-form.file>

        <div class="create-post-submit-container block">
            <x-form.submit class="create-post-button center link">Post</x-form.submit>
        </div>

        <script>
            addFilesToForm("createPostForm");
            showUploadedFilesPreview("uploadedFiles", "{{ asset('') }}");
        </script>
    </form>
</x-post.panel>