@props(['href'])

<form method="GET" action="{{ $href }}" class="sidebar-search-bar block">
    <input type="text" placeholder="{{ $slot }}" name="search" class="sidebar-search-bar-text center" value="{{ request('search') }}">
</form>