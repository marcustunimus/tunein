@props(['profilePicture', 'username'])

<x-post.panel profilePictureURL="{{ $profilePicture }}" profileName="{{ $username }}">
    <form method="POST" action="/post/create" enctype="multipart/form-data">
        @csrf

        <x-form.textarea name="body" type="text" class="post-input-text scrollbar center" containerClass="post-input-container" rows="5" required="true" placeholder="What's happening?" />
        <x-form.file name="file" class="post-file-upload center-text" containerClass="post-file-upload-container" multiple="true" accept=".png,.jpeg,.jfif,.pjpeg,.pjp,.jpg,.svg,.ico,.cur,.gif,.apng,.ogg,.mp4,.mov,.wmv,.flv,.avi,.h264,.webm,.mkv">Attach files...</x-form.input>

        <script type="text/javascript" src="{{ asset('/js/create-post.js') }}"></script>

        <script>
            showUploadedFilesPreview('file');
        </script>

        <div class="create-post-submit-container block">
            <x-form.submit class="create-post-button center link">Post</x-form.submit>
        </div>
    </form>
</x-post.panel>