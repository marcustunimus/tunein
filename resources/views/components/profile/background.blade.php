@props(['url' => ''])

<img class="profile-background" src="{{ $url != "" ? asset('/storage/profile_backgrounds/' . $url) : asset('/images/pfp.jpg') }}">