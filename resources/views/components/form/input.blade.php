@props(['name', 'class', 'containerClass', 'disableOldValue' => ''])

<div class="{{ $containerClass }} block">
    <input
        name="{{ $name }}"
        id="{{ $name }}"
        placeholder="{{ $slot }}"
        class="{{ $class }}"
        {{ $attributes(['value' => $disableOldValue ? '' : old($name)]) }}
    >

    <x-form.error name="{{ $name }}" />
</div>