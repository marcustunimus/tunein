@props(['url' => ''])

<div class="post-file post-files-content-two-per-row" style="content: url({{ asset('/' . $url) }});"></div>