@props(['url' => ''])

<div class="post-profile">
    <img class="post-profile-picture" src="{{ $url != "" ? asset('/storage/profile_pictures/' . $url) : asset('/images/pfp.jpg') }}">
    
    <span class="post-profile-name">
        {{ $slot }}
    </span>
</div>