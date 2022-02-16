@props(['href' => '', 'id' => ''])

<a id="{{ $id }}" class="post-dropdown-content-button" {{ $href != '' ? 'href=' : '' }}{{ $href }}>
    {{ $slot }}
</a>