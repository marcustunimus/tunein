<x-metadata title="TuneInMedia - Settings">
    <div id="preview" class="preview-container block hidden"></div>
    
    <div class="left-sidebar-container block">
        <div class="left-sidebar-content block">
            <x-sidebar.link-button href="{{ route('home') }}">Back to Home</x-sidebar.link-button>
        </div>
    </div>

    <div class="main-container block">
        @if ($errors->any())
            <div class="error center-text">The form could not be submitted! There were errors with the validation.</div>
        @endif

        <div class="profile-settings-heading-text center">Settings</div>

        <form method="POST" action="{{ route('profile.settings.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="profile-settings-section-text center">Change Profile Details</div>
            <x-form.input name="name" type="text" class="register-input-text center" containerClass="register-input-container" value="{{ $user->name }}" title="Name">Name</x-form.input>
            <x-form.input name="username" type="text" class="register-input-text center" containerClass="register-input-container" value="{{ $user->username }}" title="Username">Username</x-form.input>
            <x-form.input name="email" type="email" class="register-input-text center" containerClass="register-input-container" value="{{ $user->email }}" title="Email">Email</x-form.input>
            <div class="profile-settings-gender-container">
                <span>Gender:</span>
                <x-form.radio name="gender" id="male-radio-button" type="radio" class="" containerClass="" value="Male" checked="{{ $user->gender === 'Male' ? 'checked' : '' }}">Male</x-form.radio>
                <x-form.radio name="gender" id="female-radio-button" type="radio" class="" containerClass="" value="Female" checked="{{ $user->gender === 'Female' ? 'checked' : '' }}">Female</x-form.radio>
                <x-form.radio name="gender" id="unspecified-radio-button" type="radio" class="" containerClass="" value="Unspecified" checked="{{ $user->gender == null ? 'checked' : '' }}">Unspecified</x-form.radio>
            </div>
            <div class="profile-settings-section-text center">Change Profile Picture</div>
            <x-profile.image-preview id="profile-picture" url="storage/profile_pictures/{{ $user->profile_picture }}" />
            <x-form.file name="uploadedProfilePictureFile" class="profile-image-preview-upload center" containerClass="" accept=".png,.jpeg,.jpg"><span class="link link-color" id="uploadedProfilePictureFileButton">Change...</span></x-form.file>
            <script>
                showProfilePicturePreview("uploadedProfilePictureFile", "{{ asset('') }}");
            </script>
            <div class="profile-settings-section-text center">Change Background Picture</div>
            <x-profile.background-preview id="profile-background" url="storage/profile_backgrounds/{{ $user->background_picture }}" />
            <x-form.file name="uploadedBackgroundPictureFile" class="profile-image-preview-upload center" containerClass="" accept=".png,.jpeg,.jpg"><span class="link link-color" id="uploadedBackgroundPictureFileButton">Change...</span></x-form.file>
            <script>
                showBackgroundPicturePreview("uploadedBackgroundPictureFile", "{{ asset('') }}");
            </script>
            <div class="profile-settings-section-text center">Change Password</div>
            <x-form.input name="password_current" type="password" class="register-input-text center" containerClass="register-input-container" disableOldValue="true">Current Password</x-form.input>
            <x-form.input name="password" type="password" class="register-input-text center" containerClass="register-input-container">New Password</x-form.input>
            <x-form.input name="password_confirmation" type="password" class="register-input-text center" containerClass="register-input-container">Repeat New Password</x-form.input>
                        
            <div class="profile-settings-submit-container block">
                <x-form.submit class="register-button center link">Save</x-form.submit>
            </div>
        </form>
    </div>

    <div class="right-sidebar-container block">
        <div class="right-sidebar-content block">
        </div>
    </div>
</x-metadata>