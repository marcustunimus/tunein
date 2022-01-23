@props(['name', 'class', 'containerClass', 'value' => '', 'disableOldValue' => ''])

<div class="{{ $containerClass }} block">
    <input
        name="{{ $name }}"
        id="{{ $name }}"
        placeholder="{{ $slot }}"
        class="{{ $class }}"
        value="{{ $disableOldValue ? '' : (old($name) ? old($name) : $value) }}"
        {{ $attributes([]) }}
    >

    <x-form.error name="{{ $name }}" />
</div>