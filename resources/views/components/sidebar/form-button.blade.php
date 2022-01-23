@props(['href', 'method' => '', 'id' => ''])

<form id="{{ $id }}Form" method="POST" action="{{ $href }}" class="sidebar-form block">
    @csrf

    @method($method)

    <x-form.submit class="sidebar-button center link" id="{{ $id }}">{{ $slot }}</x-form.submit>
</form>