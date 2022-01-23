@props(['id'])

<div class="post-interactable-info" id="{{ $id }}-container">
    <span id="{{ $id }}" class="link link-color">
        {{ $slot }}
    </span>
</div>