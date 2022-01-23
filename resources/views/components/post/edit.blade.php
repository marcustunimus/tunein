@props(['heading', 'route', 'body', 'files'])

<div class="edit-post-container">
    <div class="edit-post-heading-text center">{{ $heading }}</div>

    <form id="editPostForm" method="POST" action="{{ $route }}" enctype="multipart/form-data">
        @csrf
        
        @method('PATCH')

        <x-form.textarea name="body" type="text" class="post-input-text scrollbar center" containerClass="post-input-container" rows="7" required="true" placeholder="What's happening?">
            {{ $body }}
        </x-form.textarea>

        <x-form.file name="uploadedFiles" class="post-file-upload-image-thumbnail-container" containerClass="post-file-upload-container" multiple="true" accept=".png,.jpeg,.jpg,.gif,.mp4,.webm">
            <div class="post-file-upload-image-thumbnail site-theme-color site-theme-color-hover center link">
                <img src="{{ asset('/images/add_white_24dp.svg') }}" style="width: 2rem;">
            </div>
        </x-form.file>

        <div class="post-submit-container block">
            <x-form.submit class="post-button center link" id="submitEditPostForm">Save</x-form.submit>
        </div>

        <script>
            autoResizeTextAreas();
            showPostFilesPreview('{{ $files }}', "{{ asset('') }}", document.getElementById('preview'), document.getElementById('post-files'));
            showUploadedFilesPreview(document.getElementById("uploadedFiles"), "{{ asset('') }}", document.getElementById('preview'), document.getElementById('uploads'), document.getElementById('post-files'), "uploadedFiles");
            addFilesToForm("editPostForm", 'uploads', document.getElementById("uploadedFiles"));
        </script>
    </form>
</div>