@foreach ($comments as $comment)
    <x-post.comment-panel profilePictureURL="{{ $comment->author->profile_picture }}" profileName="{{ $comment->author->username }}" contentId="postContent{{ $comment->id }}" timePassed="{{ $comment->created_at->diffForHumans() }}">
        <x-post.dropdown containerClass="comment-dropdown-container">
            <x-post.dropdown-link id="post-{{ $comment->id }}-link" href="{{ route('home') }}">Copy Link</x-post.dropdown-link>
            @if ($user != null)
                @if ($comment->author->id === $user->id)
                    <x-post.dropdown-link href="{{ route('post.edit', $comment) }}">Edit</x-post.dropdown-link>
                    <x-post.dropdown-button href="{{ route('post.destroy', $comment) }}" method="DELETE" id="delete{{ $comment->id }}" elementPosition="last">Delete</x-post.dropdown-button>
                    <script>
                        deleteFormConfirmationFunctionality(document.getElementById("delete{{ $comment->id }}Form"), document.getElementById('second-preview'), "{{ asset('') }}");
                    </script>
                @endif
            @endif
        </x-post.dropdown>

        <div class="comment-body-text">{{ $comment->body }}</div>

        <div id="post-{{ $comment->id }}-info-container"></div>

        <script>
            loadPostFiles({{ $comment->id }}, @json($commentsFiles[$comment->id]), "{{ asset('') }}", document.getElementById('second-preview'));
        </script>

        <x-post.comment-interaction.panel>
            <x-post.comment-interaction.button id="post-{{ $comment->id }}-like" icon="{{ $comment->isLikedByUser($user) ? 'background-image: url(' . asset('/images/favorite_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/favorite_border_white_24dp.svg') . ');' }}"></x-post.comment-interaction.button>
            <span id="post-{{ $comment->id }}-like-count" class="comment-interaction-text">{{ $comment->subPosts()->count() }}</span>
        </x-post.comment-interaction.panel>

        <script>
            setCommentInteractionButtonsFunctionality({{ $comment->id }}, {{ $comment->likes()->count() }}, "{{ asset('') }}", document.getElementById('second-preview'));
        </script>
    </x-post.comment-panel>
@endforeach