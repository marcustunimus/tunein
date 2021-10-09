@props(['url' => ''])

<div class="post-profile">
    <div class="post-profile-picture" style="content: url({{ asset('/' . $url) }});"></div>
    
    <span class="post-profile-name">
        {{ $slot }}
    </span>
</div>