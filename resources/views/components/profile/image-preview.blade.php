@props(['url' => '', 'id' => ''])

<img class="profile-image-preview block" id="{{ $id }}" src="{{ asset('/' . $url) }}">