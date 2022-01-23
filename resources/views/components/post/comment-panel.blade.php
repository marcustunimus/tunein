@props(['profilePictureURL', 'profileName', 'contentId' => '', 'timePassed' => ''])

<div class="post-comment">
    <div class="post-comment-padding">
        <x-post.profile url="{{ $profilePictureURL }}" timePassed="{{ $timePassed }}">{{ $profileName }}</x-post.profile>
    </div>
        
    <div class="post-comment-container block">
        <div class="post-comment-content block">
            <div class="post-comment-padding" id="{{ $contentId }}">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>