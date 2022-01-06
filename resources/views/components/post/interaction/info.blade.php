@props(['id'])

<div class="post-interactable-info" id="{{ $id }}-container">
    <span id="{{ $id }}" class="cursor-pointer">
        {{ $slot }}
    </span>
</div>