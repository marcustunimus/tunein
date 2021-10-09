@props(['url' => ''])

<div class="profile-image block" style="content: url({{ asset('/' . $url) }});"></div>