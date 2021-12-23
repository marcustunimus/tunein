<div id="flash-message-container" class="flash-message-container" style="{{ session('message') ? '' : 'display: none;' }}">
    <div class="flash-message-content">
        <div id="flash-message-text" class="flash-message-text">{{ session('message') ? session('message') : '' }}</div>
        <div id="flash-message-close-button" class="flash-message-close-button-container">
            <div class="flash-message-close-button" style="background-image: url({{ asset('images/close_white_24dp.svg') }});"></div>
        </div>
    </div>

    <script>
        setFlashMessageCloseButtonFunctionality();
    </script>
</div>

