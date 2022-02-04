@props(['name', 'class', 'containerClass', 'postId' => '', 'accept' => '', 'hideUploadsContainer' => '', 'containerClassPure' => '', 'multiple' => false,])

<div class="{{ $containerClass }} {{ $containerClassPure ? '' : 'block' }}" id="{{ $name }}Container">
    <div id="files-input-container">
        <input
            name="{{ $name }}{{ $multiple ? '[]' : '' }}"
            id="{{ $name }}"
            type="file"
            class="{{ $class }} hidden"
            {{ $multiple ? 'multiple' : '' }}
            {{ $accept ? 'accept=' . $accept : '' }}
        >

        <label 
            for="{{ $name }}"
            class="{{ $class }}"
            id="{{ $name }}Label"
        >
            {{ $slot }}
        </label>
    </div>

    <div id="uploads{{ $postId }}" class="post-file-upload-image-thumbnails-container" {{ $hideUploadsContainer ? 'style=display:none;' : '' }}>
        <div id="post-files{{ $postId }}" class="post-files-upload-thumbnails-container"></div>
    </div>

    <x-form.error name="{{ $name }}.*" />
    <x-form.error name="max_post_size{{ $postId }}" />
</div>
