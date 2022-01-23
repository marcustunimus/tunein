@props(['name', 'id', 'class', 'containerClass', 'inputClass' => '', 'checked' => ''])

<span class="{{ $containerClass }}">
    <input
        name="{{ $name }}"
        type="checkbox"
        id="{{ $id }}"
        class="{{ $inputClass }}"
        {{ $checked !== '' ? "checked" : "" }}
        {{ $attributes(['value' => old($name)]) }}
    >

    <label for="{{ $id }}" class="{{ $class }}">{{ $slot }}</label>

    <x-form.error name="{{ $name }}" />
</span>