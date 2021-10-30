@props(['propName'])

<div class="post-dropdown-container" >
    <button class="post-dropdown-button" style="background-image: url({{ asset('/' . 'images/more_vert_white_24dp.svg') }});"></button>
    <div class="post-dropdown-content">
        {{ $slot }}
    </div>
</div>