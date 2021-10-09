@props(['name', 'class', 'containerClass', 'accept' => '', 'multiple' => false,])

<div class="{{ $containerClass }} block">
    <div id="files-input-container">
        <input
            name="{{ $name }}[]"
            id="{{ $name }}"
            type="file"
            placeholder="{{ $slot }}"
            class="{{ $class }}"
            {{ $multiple ? 'multiple' : '' }}
            {{ $accept ? 'accept=' . $accept : '' }}
        >

        <label 
            for="{{ $name }}"
            class="{{ $class }}"
        >
            {{ $slot }}
        </label>
    </div>

    <div id="uploads" class="post-file-upload-image-thumbnails-container"></div>

    <x-form.error name="{{ $name }}.*" />
</div>
