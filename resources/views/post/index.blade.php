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
                <x-post.dropdown-link id="post-{{ $post->id }}-link">Copy Link</x-post.dropdown-link>
                @if ($user != null)
                    @if ($post->author->id === $user->id)
                        <x-post.dropdown-link href="{{ route('post.edit', $post) }}">Edit</x-post.dropdown-link>
                        <x-post.dropdown-button href="{{ route('post.destroy', $post) }}" method="DELETE" id="delete{{ $post->id }}" elementPosition="last">Delete</x-post.dropdown-button>
                        <script>
                            deleteFormConfirmationFunctionality(document.getElementById("delete{{ $post->id }}Form"), document.getElementById('second-preview'), "{{ asset('') }}");
                        </script>
                    @endif
                @endif
            </x-post.dropdown>
        
            <div class="post-body-text">{{ $post->body }}</div>
        
            <script>
                loadPostFiles({{ $post->id }}, @json($files[$post->id]), "{{ asset('') }}", document.getElementById('second-preview'));
            </script>

            <x-post.interaction.info id="post-{{ $post->id }}-info" title="View Likes">{{ ($likesCount = $post->likes()->count()) }} {{ $likesCount === 1 ? 'like' : 'likes' }}</x-post.interaction.info>
        
            <x-post.interaction.tab>
                <x-post.interaction.button id="post-{{ $post->id }}-like" title="Like" icon="{{ $post->isLikedByUser($user) ? 'background-image: url(' . asset('/images/favorite_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/favorite_border_white_24dp.svg') . ');' }}"></x-post.interaction.button>
                <x-post.interaction.button id="post-{{ $post->id }}-comment" title="View Comments" icon="{{ 'background-image: url(' . asset('/images/comment_white_24dp.svg') . ');' }}" buttonClassAddition="-disabled">{{ $post->subPosts()->count() }}</x-post.interaction.button>
                <x-post.interaction.button id="post-{{ $post->id }}-bookmark" title="Bookmark" icon="{{ $post->isBookmarkedByUser($user) ? 'background-image: url(' . asset('/images/bookmark_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/bookmark_border_white_24dp.svg') . ');' }}"></x-post.interaction.button>
            </x-post.interaction.tab>
        
            <script>
                setInteractionButtonsFunctionality({{ $post->id }}, {{ $likesCount }}, "{{ asset('') }}", document.getElementById('second-preview'));
            </script>
        </x-post.panel>

        <x-post.comments-panel>
            @if (auth()->check())
                <x-post.create-comment profilePicture="{{ $user->profile_picture }}" username="{{ $user->username }}" route="{{ route('post.comment', $post->id) }}" postId="{{ $post->id }}" previewPrefix="second-" />
            @endif
                
            @if (! $comments->count())
                <div class="no-posts-found-text">There are no comments.</div>
            @endif
                
            @foreach ($comments as $comment)
                <x-post.comment-panel profilePictureURL="{{ $comment->author->profile_picture }}" profileName="{{ $comment->author->username }}" contentId="postContent{{ $comment->id }}" timePassed="{{ $comment->created_at->diffForHumans() }}">
                    <x-post.dropdown containerClass="comment-dropdown-container">
                        <x-post.dropdown-link id="post-{{ $comment->id }}-link">Copy Link</x-post.dropdown-link>
                        @if ($user != null)
                            @if ($comment->author->id === auth()->user()->id)
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
                        <x-post.comment-interaction.button id="post-{{ $comment->id }}-like" title="Like" icon="{{ $comment->isLikedByUser($user) ? 'background-image: url(' . asset('/images/favorite_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/favorite_border_white_24dp.svg') . ');' }}"></x-post.comment-interaction.button>
                        <span id="post-{{ $comment->id }}-like-count" class="comment-interaction-text">{{ ($likesCount = $comment->likes()->count()) }}</span>
                    </x-post.comment-interaction.panel>
                
                    <script>
                        setCommentInteractionButtonsFunctionality({{ $comment->id }}, {{ $likesCount }}, "{{ asset('') }}", document.getElementById('second-preview'));
                    </script>
                </x-post.comment-panel>
            @endforeach
        </x-post.comments-panel>

        @if ($comments->count() > 10)
            <script>
                addLoadMoreCommentsButton({{ $post->id }}, "{{ asset('') }}");
            </script>
        @endif
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content scrollbar block">
        </div>
    </div>

    @if (session('postId'))
        <script>
            viewCommentsWindow("{{ asset('') }}", {{ session('postId') }});
        </script>
    @endif

    @if ($errors->any())
        @foreach ($errors->keys() as $key)
            @if (str_contains($key, "uploadedFiles") && strlen(substr(str_replace("uploadedFiles", "", $key), 0, strlen(str_replace("uploadedFiles", "", $key)) - 2)) > 0)
            <script>
                viewCommentsWindow("{{ asset('') }}", {{ $postId = substr(str_replace("uploadedFiles", "", $key), 0, strlen(str_replace("uploadedFiles", "", $key)) - 2) }}, "uploadedFiles{{ $postId }}Container", "{{ $errors->first($key) }}", "{{ old('body' . $postId) }}");
            </script>
            @elseif (str_contains($key, "body") && strlen(substr(str_replace("body", "", $key), 0, strlen(str_replace("body", "", $key)))) > 0)
            <script>
                viewCommentsWindow("{{ asset('') }}", {{ $postId = substr(str_replace("body", "", $key), 0, strlen(str_replace("body", "", $key))) }}, "body{{ $postId }}Container", "{{ $errors->first($key) }}", "{{ old('body' . $postId) }}");
            </script>
            @endif
        @endforeach
    @endif
</x-metadata>