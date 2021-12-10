@props(['href' => '', 'id' => ''])

<a id="{{ $id }}" class="post-dropdown-content-button" href="{{ $href }}">
    {{ $slot }}
</a>