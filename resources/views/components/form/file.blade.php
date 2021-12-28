@props(['name', 'class', 'containerClass', 'accept' => '', 'multiple' => false,])

<div class="{{ $containerClass }} block">
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

    <div id="uploads" class="post-file-upload-image-thumbnails-container">
        <div id="post-files" class="post-files-upload-thumbnails-container"></div>
    </div>

    <x-form.error name="file.*" />
    <x-form.error name="max_post_size" />
</div>
