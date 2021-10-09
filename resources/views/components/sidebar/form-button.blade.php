@props(['href'])

<form method="POST" action="{{ $href }}" class="sidebar-form block">
    @csrf

    <x-form.submit class="sidebar-button center link">{{ $slot }}</x-form.submit>
</form>