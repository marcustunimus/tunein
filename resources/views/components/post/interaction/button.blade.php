@props(['id', 'icon'])

<div id="{{ $id }}" class="post-interaction-button">
    <div class="post-interaction-icon" style="{{ $icon }}"></div>
    @if ($slot != "")
        <div class="post-interaction-text">
            {{ $slot }}
        </div>
    @endif
</div>