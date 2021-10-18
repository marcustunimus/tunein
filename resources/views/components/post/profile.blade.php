@props(['url' => ''])

<div class="post-profile">
    <img class="post-profile-picture" src="{{ asset('/' . $url) }}">
    
    <span class="post-profile-name">
        {{ $slot }}
    </span>
</div>