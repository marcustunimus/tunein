@props(['href'])

<a class="post-dropdown-content-button" href="{{ $href }}">
    {{ $slot }}
</a>