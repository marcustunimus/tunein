@props(['name', 'class', 'containerClass'])

<div class="{{ $containerClass }} block">
    <input
        name="{{ $name }}"
        id="{{ $name }}"
        placeholder="{{ $slot }}"
        class="{{ $class }}"
        {{ $attributes(['value' => old($name)]) }}
    >

    <x-form.error name="{{ $name }}" />
</div>