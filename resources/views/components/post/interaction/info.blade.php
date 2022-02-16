@props(['id', 'title' => ''])

<div class="post-interactable-info" id="{{ $id }}-container">
    <span id="{{ $id }}" class="link link-color" title="{{ $title }}">{{ $slot }}</span>
</div>