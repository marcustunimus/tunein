@props(['profilePictureURL', 'profileName'])

<div class="post">
    <x-post.profile url="{{ $profilePictureURL }}">{{ $profileName }}</x-post.profile>
    
    <div class="post-container block">
        <div class="post-content block">
            {{ $slot }}
        </div>
    </div>
</div>