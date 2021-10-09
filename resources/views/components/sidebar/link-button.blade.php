@props(['href'])

<div class="sidebar-form block">
    <a href="{{ $href }}" class="sidebar-button center link">{{ $slot }}</a>
</div>