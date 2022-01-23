<x-metadata title="Login - TuneInMedia">
    <div class="login-page">
        <div class="login-form-container">
            <div class="login-form-content-container post-container">
                <div class="login-form-content block">
                    <div class="login-heading-text center">Login</div>

                    <form method="POST" action="{{ route('login.attempt') }}">
                        @csrf

                        <x-form.input name="email" type="email" class="login-input-text center" containerClass="login-input-container">Email</x-form.input>
                        <x-form.input name="password" type="password" class="login-input-text center" containerClass="login-input-container">Password</x-form.input>
                        {{-- <x-form.input name="remember" type="checkbox" class="login-text">Remember me:</x-form.input> --}}

                        <div class="login-submit-container block">
                            <x-form.submit class="login-button center link">Log In</x-form.submit>
                        </div>
                    </form>

                    <div class="login-text login-register-container center mt-3">
                        Don't have an account?<a href="{{ route('register') }}" class="login-text link-color ml-1">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-metadata>
