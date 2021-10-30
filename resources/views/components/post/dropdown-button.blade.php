@props(['href', 'method' => ''])

<form method="POST" action="{{ $href }}" class="post-dropdown-content-button" style="padding: 0;">
    @csrf

    @method($method)

    <x-form.submit class="post-dropdown-content-form-button-text">{{ $slot }}</x-form.submit>
</form>