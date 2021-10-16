@props(['href', 'method' => ''])

<form method="POST" action="{{ $href }}" class="sidebar-form block">
    @csrf

    @method($method)

    <x-form.submit class="sidebar-button center link">{{ $slot }}</x-form.submit>
</form>