@props(['url' => '', 'id' => ''])

<img class="profile-image-preview block" id="{{ $id }}" src="{{ $url != "" ? asset('/storage/profile_pictures/' . $url) : asset('/images/pfp.jpg') }}">