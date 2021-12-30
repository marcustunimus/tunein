@props(['url' => ''])

<img class="profile-background" src="{{ $url != "" ? asset('/storage/profile_backgrounds/' . $url) : asset('/images/background_default_image.jpg') }}">