@props(['id'])

<div class="post-interactable-info">
    <span id="{{ $id }}" class="cursor-pointer">
        {{ $slot }}
    </span>
</div>