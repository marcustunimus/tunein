@props(['url' => '', 'timePassed' => ''])

<div class="post-profile">
    <a href="{{ route('profile', $slot) }}"><img class="post-profile-picture" src="{{ $url != "" ? asset('/storage/profile_pictures/' . $url) : asset('/images/pfp.jpg') }}"></a>
    
    <span class="post-profile-name link"><a href="{{ route('profile', $slot) }}">{{ $slot }}</a></span>

    <span class="post-date-text"><time>{{ $timePassed }}</time></span>
</div>