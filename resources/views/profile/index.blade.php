<x-metadata title="TuneIn - Profile">
    <div id="preview" class="preview-container block"></div>

    <div class="left-sidebar-container block">
        <div class="left-sidebar-content block">
            <x-sidebar.link-button href="/home">Home</x-sidebar.link-button>
            <x-sidebar.link-button href="/bookmarks">Bookmarks</x-sidebar.link-button>
            <x-sidebar.link-button href="/explore">Explore</x-sidebar.link-button>
            <x-sidebar.link-button href="/profile">Profile</x-sidebar.link-button>
            <x-sidebar.form-button href="/logout">Log Out</x-sidebar.form-button>
        </div>
    </div>

    <div class="main-container block">
        <x-profile.background url="images/pfp.jpg" />

        <div class="profile-info">
            <x-profile.image url="images/pfp.jpg" />

            <div class="profile-name">MarkTuning</div>

            <div class="profile-username">marktuning</div>
        </div>

        <x-post.create profilePicture="images/pfp.jpg" username="{{ $user->username }}" route="{{ route('post.create') }}" />

        @foreach ($posts as $post)
            <x-post.panel profilePictureURL="images/pfp.jpg" profileName="{{ $post->author->username }}" contentId="postContent{{ $post->id }}">
                <x-post.dropdown>
                    <x-post.dropdown-link href="{{ route('post.edit', $post) }}">Edit</x-post.dropdown-link>
                    <x-post.dropdown-button href="{{ route('post.destroy', $post) }}" method="DELETE">Delete</x-post.dropdown-button>
                </x-post.dropdown>
                
                <div class="post-body-text">{{ $post->body }}</div>

                <script>
                    loadPostFiles({{ $post->id }}, "{{ $files[$post->id] }}");
                </script>

                <x-post.interaction.tab>
                    <x-post.interaction.button id="post-{{ $post->id }}-like" icon="{{ in_array($post->id, $userLikes) ? 'post-liked-icon' : 'post-likable-icon' }}">
                        {{ in_array($post->id, $userLikes) ? 'Dislike' : 'Like' }}
                    </x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $post->id }}-comment" icon="post-comment-icon">Comments</x-post-interaction-button>
                    <x-post.interaction.button id="post-{{ $post->id }}-share" icon="post-share-icon">Share</x-post-interaction-button>
                </x-post.interaction.tab>

                {{-- TODO --}}
                <x-post.interaction.info id="post-{{ $post->id }}-info">{{ $postLikes[$post->id]->count() }} likes</x-post.interaction.info> 
            </x-post.panel>

            <script>
                setInteractionButtonsFunctionality({{ $post->id }}, {{ $postLikes[$post->id]->count() }});
            </script>
        @endforeach
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content block">
            <x-sidebar.search-bar href="/profile">Search...</x-sidebar.search-bar>
        </div>
    </div>
</x-metadata>