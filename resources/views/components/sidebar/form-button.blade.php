@props(['href', 'method' => '', 'id' => '', 'postType' => ''])

<form id="{{ $id }}Form" method="POST" action="{{ $href }}" class="sidebar-form block">
    @csrf

    @method($method)

    @if ($postType !== '')
        <input name="postType" type="hidden" value="{{ $postType }}">        
    @endif

    <x-form.submit class="sidebar-button center link" id="{{ $id }}">{{ $slot }}</x-form.submit>
</form>