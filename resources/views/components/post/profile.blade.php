@props(['url' => ''])

<div class="post-profile">
    <img class="post-profile-picture" src="{{ $url != "" ? asset('/storage/profile_pictures/' . $url) : asset('/images/pfp.jpg') }}">
    
    <span class="post-profile-name link"><a href="{{ route('profile', $slot) }}">{{ $slot }}</a></span>
</div>