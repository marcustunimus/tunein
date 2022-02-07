@props(['profilePicture', 'username', 'route', 'postId', 'previewPrefix' => ''])

<x-post.comment-panel profilePictureURL="{{ $profilePicture }}" profileName="{{ $username }}">
    <form id="commentPostForm{{ $postId }}" method="POST" action="{{ $route }}" enctype="multipart/form-data">
        @csrf

        <x-form.input containerClass="" class="" type="hidden" name="comment_on_post" id="comment_on_post" value="{{ $postId }}" />
        <x-form.textarea name="body{{ $postId }}" type="text" class="post-comment-input-text scrollbar center" containerClass="post-comment-input-container" rows="1" required="true" placeholder="What's your opinion?" />
        <div class="flex justify-between">
            <div class="inline-flex">
                <x-form.file name="uploadedFiles{{ $postId }}" class="post-file-upload-image-thumbnail-container" containerClass="post-file-upload-container" multiple="true" accept=".png,.jpeg,.jpg,.gif,.mp4,.webm" postId="{{ $postId }}">
                    <div class="post-file-upload-image-thumbnail post-file-upload-image-thumbnail-border field-theme-color-hover center link">
                        <img src="{{ asset('/images/add_white_24dp.svg') }}" style="width: 2rem;">
                    </div>
                </x-form.file>
            </div>

            <div class="grid content-end">
                <div class="create-post-submit-container block">
                    <x-form.submit class="create-post-button center link" id="submitCommentPostForm{{ $postId }}">Comment</x-form.submit>
                </div>
            </div>
        </div>

        <script>
            autoResizeTextAreas();
            showUploadedFilesPreview(document.getElementById("uploadedFiles{{ $postId }}"), "{{ asset('') }}", document.getElementById('{{ $previewPrefix }}preview'), document.getElementById('uploads{{ $postId }}'), document.getElementById('post-files{{ $postId }}'), "uploadedFiles{{ $postId }}");
            addFilesToForm("commentPostForm{{ $postId }}", 'uploads{{ $postId }}', document.getElementById("uploadedFiles{{ $postId }}"));
        </script>
    </form>
</x-post.comment-panel>