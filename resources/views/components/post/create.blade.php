@props(['profilePicture', 'username', 'route'])

<x-post.panel profilePictureURL="{{ $profilePicture }}" profileName="{{ $username }}">
    <form id="createPostForm" method="POST" action="{{ $route }}" enctype="multipart/form-data">
        @csrf

        <x-form.textarea name="body" type="text" class="post-input-text scrollbar center" containerClass="post-input-container" rows="3" required="true" placeholder="What's happening?" />
        <div class="flex justify-between">
            <div class="inline-flex">
                <x-form.file name="uploadedFiles" class="post-file-upload-image-thumbnail-container" containerClass="post-file-upload-container" multiple="true" accept=".png,.jpeg,.jpg,.gif,.mp4,.webm">
                    <div class="post-file-upload-image-thumbnail post-file-upload-image-thumbnail-border field-theme-color-hover center link">
                        <img src="{{ asset('/images/add_white_24dp.svg') }}" style="width: 2rem;">
                    </div>
                </x-form.file>
            </div>

            <div class="grid content-start pt-2">
                <div class="create-post-submit-container block pl-2">
                    <x-form.submit class="create-post-button center link" id="submitCreatePostForm">Post</x-form.submit>
                </div>
            </div>
        </div>

        <script>
            autoResizeTextAreas();
            showUploadedFilesPreview(document.getElementById("uploadedFiles"), "{{ asset('') }}", document.getElementById('preview'), document.getElementById('uploads'), document.getElementById('post-files'), "uploadedFiles");
            addFilesToForm("createPostForm", 'uploads', document.getElementById("uploadedFiles"));
        </script>
    </form>
</x-post.panel>