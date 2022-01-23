@props(['id', 'icon', 'buttonClassAddition' => ''])

<div id="{{ $id }}" class="post-interaction-button{{ $buttonClassAddition }}">
    <div class="post-interaction-icon" style="{{ $icon }}"></div>
    @if ($slot != "")
        <div class="post-interaction-text">
            {{ $slot }}
        </div>
    @endif
</div>