<x-metadata title="Register - TuneInMedia">
    <div class="register-page">
        <div class="register-form-container">
            <div class="register-form-content-container post-container">
                <div class="register-form-content block">
                    <div class="register-heading-text center">Register</div>

                    <form method="POST" action="{{ route('register.store') }}">
                        @csrf

                        <x-form.input name="name" type="text" class="register-input-text center" containerClass="register-input-container">Name</x-form.input>
                        <x-form.input name="username" type="text" class="register-input-text center" containerClass="register-input-container">Username</x-form.input>
                        <x-form.input name="email" type="email" class="register-input-text center" containerClass="register-input-container">Email</x-form.input>
                        <x-form.input name="password" type="password" class="register-input-text center" containerClass="register-input-container">Password</x-form.input>
                        <x-form.input name="password_confirmation" type="password" class="register-input-text center" containerClass="register-input-container">Repeat Password</x-form.input>

                        <div class="register-submit-container block">
                            <x-form.submit class="register-button center link">Register</x-form.submit>
                        </div>
                    </form>

                    <div class="register-login-container register-text center mt-3">
                        Already have an account?<a href="{{ route('login') }}" class="register-text link-color ml-1">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-metadata>
