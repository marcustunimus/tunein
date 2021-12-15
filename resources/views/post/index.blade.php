<x-metadata title="TuneInMedia - View Post">
    <div id="preview" class="preview-container block"></div>
    
    <div class="left-sidebar-container block">
        <div class="left-sidebar-content block">
            <x-sidebar.link-button href="{{ route('home') }}">Home</x-sidebar.link-button>
        </div>
    </div>

    <div class="main-container block">
        <x-post.panel profilePictureURL="images/pfp.jpg" profileName="{{ $post->author->username }}" contentId="postContent{{ $post->id }}">
            <x-post.dropdown>
                <x-post.dropdown-link id="post-{{ $post->id }}-link" href="{{ route('home') }}">Copy Link</x-post.dropdown-link>
                @if ($user != null)
                    @if ($post->author->id === auth()->user()->id)
                        <x-post.dropdown-link href="{{ route('post.edit', $post) }}">Edit</x-post.dropdown-link>
                        <x-post.dropdown-button href="{{ route('post.destroy', $post) }}" method="DELETE">Delete</x-post.dropdown-button>
                    @endif
                @endif
            </x-post.dropdown>
            
            <div class="post-body-text">{{ $post->body }}</div>

            <script>
                loadPostFiles({{ $post->id }}, "{{ $files[$post->id] }}", "{{ asset('') }}");
            </script>

            <x-post.interaction.info id="post-{{ $post->id }}-info">{{ $postLikes[$post->id]->count() }} likes</x-post.interaction.info> 

            <x-post.interaction.tab>
                <x-post.interaction.button id="post-{{ $post->id }}-like" icon="{{ in_array($post->id, $userLikes) ? 'background-image: url(' . asset('/images/favorite_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/favorite_border_white_24dp.svg') . ');' }}"></x-post-interaction-button>
                <x-post.interaction.button id="post-{{ $post->id }}-comment" icon="{{ 'background-image: url(' . asset('/images/comment_white_24dp.svg') . ');' }}">Comments</x-post-interaction-button>
                <x-post.interaction.button id="post-{{ $post->id }}-bookmark" icon="{{ in_array($post->id, $userBookmarks) ? 'background-image: url(' . asset('/images/bookmark_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/bookmark_border_white_24dp.svg') . ');' }}">Bookmark</x-post-interaction-button>
            </x-post.interaction.tab>

            <script>
                setInteractionButtonsFunctionality({{ $post->id }}, {{ $postLikes[$post->id]->count() }}, "{{ asset('') }}");
            </script>
        </x-post.panel>

        <x-post.comment profilePicture="images/pfp.jpg" username="{{ $user->username }}" route="{{ route('post.comment', $post->id) }}" postId="{{ $post->id }}"/>

        @foreach ($comments as $comment)
            <x-post.panel profilePictureURL="images/pfp.jpg" profileName="{{ $comment->author->username }}" contentId="postContent{{ $comment->id }}">
                <x-post.dropdown>
                    <x-post.dropdown-link id="post-{{ $comment->id }}-link" href="{{ route('home') }}">Copy Link</x-post.dropdown-link>
                    @if ($user != null)
                        @if ($comment->author->id === auth()->user()->id)
                            <x-post.dropdown-link href="{{ route('post.edit', $comment) }}">Edit</x-post.dropdown-link>
                            <x-post.dropdown-button href="{{ route('post.destroy', $comment) }}" method="DELETE">Delete</x-post.dropdown-button>
                        @endif
                    @endif
                </x-post.dropdown>

                <div class="post-body-text">{{ $comment->body }}</div>

                <script>
                    loadPostFiles({{ $comment->id }}, "{{ $commentsFiles[$comment->id] }}", "{{ asset('') }}");
                </script>

                <x-post.interaction.info id="post-{{ $comment->id }}-info">{{ $commentsLikes[$comment->id]->count() }} likes</x-post.interaction.info> 

                <x-post.interaction.tab>
                    <x-post.interaction.button id="post-{{ $post->id }}-like" icon="{{ in_array($post->id, $userLikes) ? 'background-image: url(' . asset('/images/favorite_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/favorite_border_white_24dp.svg') . ');' }}"></x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $post->id }}-comment" icon="{{ 'background-image: url(' . asset('/images/comment_white_24dp.svg') . ');' }}">Comments</x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $post->id }}-bookmark" icon="{{ in_array($post->id, $userCommentsBookmarks) ? 'background-image: url(' . asset('/images/bookmark_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/bookmark_border_white_24dp.svg') . ');' }}">Bookmark</x-post-interaction-button>
                </x-post.interaction.tab>

                <script>
                    setInteractionButtonsFunctionality({{ $comment->id }}, {{ $commentsLikes[$comment->id]->count() }}, "{{ asset('') }}");
                </script>
            </x-post.panel>
        @endforeach
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content block">
        </div>
    </div>
</x-metadata>