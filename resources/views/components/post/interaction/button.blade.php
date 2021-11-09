@props(['id', 'icon'])

<div id="{{ $id }}" class="post-interaction-button">
    <div class="post-interaction-icon {{ $icon }}"></div>
    <div class="post-interaction-text">
        {{ $slot }}
    </div>
</div>