<x-metadata title="Explore - TuneInMedia">
    <div id="preview" class="preview-container block"></div>
    <x-flash />

    <div class="left-sidebar-container block">
        <div class="left-sidebar-content scrollbar block">
            <x-sidebar.link-button href="{{ route('home') }}">Home</x-sidebar.link-button>
            <x-sidebar.link-button href="{{ route('bookmarks') }}">Bookmarks</x-sidebar.link-button>
            <x-sidebar.link-button href="{{ route('explore') }}">Explore</x-sidebar.link-button>
            <x-sidebar.link-button href="{{ route('profile', auth()->user()->username) }}">Profile</x-sidebar.link-button>
            <x-sidebar.link-button href="{{ route('profile.settings', auth()->user()->username) }}">Settings</x-sidebar.link-button>
            <x-sidebar.form-button href="{{ route('logout') }}">Log Out</x-sidebar.form-button>
        </div>
    </div>

    <div class="main-container block">
        <div class="heading-text center">Explore</div>

        @if (! $posts->count())
            <div class="no-posts-found-text">There are no posts currently.</div>
        @endif

        @foreach ($posts as $post)
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
                    <x-post.interaction.button id="post-{{ $post->id }}-comment" icon="{{ 'background-image: url(' . asset('/images/comment_white_24dp.svg') . ');' }}">{{ $comments[$post->id]->count() }}</x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $post->id }}-bookmark" icon="{{ in_array($post->id, $userBookmarks) ? 'background-image: url(' . asset('/images/bookmark_white_24dp.svg') . ');' : 'background-image: url(' . asset('/images/bookmark_border_white_24dp.svg') . ');' }}"></x-post-interaction-button>
                </x-post.interaction.tab>
            
                <script>
                    setInteractionButtonsFunctionality({{ $post->id }}, {{ $postLikes[$post->id]->count() }}, "{{ asset('') }}", document.getElementById('preview'));
                </script>
            </x-post.panel>
        @endforeach

        <div class="mb-6">{{ $posts->links() }}</div>
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content scrollbar block">
            <x-sidebar.search-bar href="{{ route('explore') }}">Search...</x-sidebar.search-bar>
        </div>
    </div>

    @if (session('postId'))
        <script>
            viewCommentsWindow("{{ asset('') }}", {{ session('postId') }}, false);
        </script>
    @endif
</x-metadata>