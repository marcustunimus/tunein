@props(['url' => ''])

<div class="post-file" style="content: url({{ asset('/' . $url) }});"></div>