@props(['profilePicture', 'username', 'route', 'postId'])

<x-post.panel profilePictureURL="{{ $profilePicture }}" profileName="{{ $username }}">
    <form id="commentPostForm" method="POST" action="{{ $route }}" enctype="multipart/form-data">
        @csrf

        <x-form.input containerClass="" class="" type="hidden" name="comment_on_post" id="comment_on_post" value="{{ $postId }}" />
        <x-form.textarea name="body" type="text" class="post-input-text scrollbar center" containerClass="post-input-container" rows="3" required="true" placeholder="What's happening?" />
        <x-form.file name="uploadedFiles" class="post-file-upload center-text" containerClass="post-file-upload-container" multiple="true" accept=".png,.jpeg,.jpg,.gif,.mp4,.webm">Attach files...</x-form.input>

        <div class="create-post-submit-container block">
            <x-form.submit class="create-post-button center link">Comment</x-form.submit>
        </div>

        <script>
            addFilesToForm("commentPostForm");
            showUploadedFilesPreview("uploadedFiles", "{{ asset('') }}");
        </script>
    </form>
</x-post.panel>