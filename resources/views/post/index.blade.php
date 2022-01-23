<x-metadata title="View Post - TuneInMedia">
    <div id="preview" class="preview-container block"></div>
    <x-flash />
    
    <div class="left-sidebar-container block">
        <div class="left-sidebar-content scrollbar block">
            <x-sidebar.link-button href="{{ route('home') }}">Home</x-sidebar.link-button>
        </div>
    </div>

    <div class="main-container block">
        <div class="heading-text center">Post</div>

        <x-post.panel profilePictureURL="{{ $post->author->profile_picture }}" profileName="{{ $post->author->username }}" contentId="postContent{{ $post->id }}" timePassed="{{ $post->created_at->diffForHumans() }}">
            <x-post.dropdown containerClass="post-dropdown-container">
                <x-post.dropdown-link id="post-{{ $post->id }}-link" href="{{ route('home') }}">Copy Link</x-post.dropdown-link>
                @if ($user != null)
                    @if ($post->author->id === auth()->user()->id)
                        <x-post.dropdown-link href="{{ route('post.edit', $post) }}">Edit</x-post.dropdown-link>
                        <x-post.dropdown-button href="{{ route('post.destroy', $post) }}" method="DELETE" id="delete{{ $post->id }}" elementPosition="last">Delete</x-post.dropdown-button>
                        <script>
                            deleteFormConfirmationFunctionality(document.getElementById("delete{{ $post->id }}Form"), document.getElementById('preview'), "{{ asset('') }}");
                        </script>
                    @endif
                @endif
            </x-post.dropdown>
            
            <div class="post-body-text">{{ $post->body }}</div>

            <script>
                loadPostFiles({{ $post->id }}, "{{ $files[$post->id] }}", "{{ asset('') }}", document.getElementById('preview'));
            </script>

            <x-post.interaction.info id="post-{{ $post->id }}-info">{{ $postLikes[$post->id]->count() }} {{ $postLikes[$post->id]->count() === 1 ? 'like' : 'likes' }}</x-post.interaction.info> 

            <x-post.interaction.tab>
                <x-post.interaction.button id="post-{{ $post->id }}-like" icon="{{ in_array($post->id, $userLikes) ? 'background-image: url(' . asset('/images/favorite_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/favorite_border_white_24dp.svg') . ');' }}"></x-post-interaction-button>
                <x-post.interaction.button id="post-{{ $post->id }}-comment" icon="{{ 'background-image: url(' . asset('/images/comment_white_24dp.svg') . ');' }}">{{ $comments->count() }}</x-post-interaction-button>
                <x-post.interaction.button id="post-{{ $post->id }}-bookmark" icon="{{ in_array($post->id, $userBookmarks) ? 'background-image: url(' . asset('/images/bookmark_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/bookmark_border_white_24dp.svg') . ');' }}"></x-post-interaction-button>
            </x-post.interaction.tab>

            <script>
                setInteractionButtonsFunctionality({{ $post->id }}, {{ $postLikes[$post->id]->count() }}, "{{ asset('') }}", document.getElementById('preview'));
            </script>
        </x-post.panel>

        <div class="post-comments-heading">Comments</div>

        @if (auth()->check())
            <x-post.create-comment profilePicture="{{ $user->profile_picture }}" username="{{ $user->username }}" route="{{ route('post.comment', $post->id) }}" postId="{{ $post->id }}"/>
        @endif

        @foreach ($comments as $comment)
            <x-post.panel profilePictureURL="{{ $comment->author->profile_picture }}" profileName="{{ $comment->author->username }}" contentId="postContent{{ $comment->id }}" timePassed="{{ $comment->created_at->diffForHumans() }}">
                <x-post.dropdown containerClass="post-dropdown-container">
                    <x-post.dropdown-link id="post-{{ $comment->id }}-link" href="{{ route('home') }}">Copy Link</x-post.dropdown-link>
                    @if ($user != null)
                        @if ($comment->author->id === auth()->user()->id)
                            <x-post.dropdown-link href="{{ route('post.edit', $comment) }}">Edit</x-post.dropdown-link>
                            <x-post.dropdown-button href="{{ route('post.destroy', $comment) }}" method="DELETE" id="delete{{ $comment->id }}" elementPosition="last">Delete</x-post.dropdown-button>
                            <script>
                                deleteFormConfirmationFunctionality(document.getElementById("delete{{ $comment->id }}Form"), document.getElementById('preview'), "{{ asset('') }}");
                            </script>
                        @endif
                    @endif
                </x-post.dropdown>

                <div class="post-body-text">{{ $comment->body }}</div>

                <script>
                    loadPostFiles({{ $comment->id }}, "{{ $commentsFiles[$comment->id] }}", "{{ asset('') }}", document.getElementById('preview'));
                </script>

                <x-post.interaction.info id="post-{{ $comment->id }}-info">{{ $commentsLikes[$comment->id]->count() }} {{ $commentsLikes[$comment->id]->count() === 1 ? 'like' : 'likes' }}</x-post.interaction.info> 

                <x-post.interaction.tab>
                    <x-post.interaction.button id="post-{{ $comment->id }}-like" icon="{{ in_array($comment->id, $userCommentsLikes) ? 'background-image: url(' . asset('/images/favorite_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/favorite_border_white_24dp.svg') . ');' }}"></x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $comment->id }}-comment" icon="{{ 'background-image: url(' . asset('/images/comment_white_24dp.svg') . ');' }}">{{ $commentsOfComments[$comment->id]->count() }}</x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $comment->id }}-bookmark" icon="{{ in_array($comment->id, $userCommentsBookmarks) ? 'background-image: url(' . asset('/images/bookmark_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/bookmark_border_white_24dp.svg') . ');' }}"></x-post-interaction-button>
                </x-post.interaction.tab>

                <script>
                    setInteractionButtonsFunctionality({{ $comment->id }}, {{ $commentsLikes[$comment->id]->count() }}, "{{ asset('') }}", document.getElementById('preview'));
                </script>
            </x-post.panel>
        @endforeach

        <div class="mb-6">{{ $comments->links() }}</div>
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content scrollbar block">
        </div>
    </div>

    @if (session('postId'))
        <script>
            viewCommentsWindow("{{ asset('') }}", {{ session('postId') }}, false);
        </script>
    @endif
</x-metadata>