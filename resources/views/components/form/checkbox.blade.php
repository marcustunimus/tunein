@props(['name', 'id', 'class', 'containerClass', 'checked' => ''])

<span class="{{ $containerClass }}">
    <input
        name="{{ $name }}"
        type="checkbox"
        id="{{ $id }}"
        class="{{ $class }}"
        {{ $checked !== '' ? "checked" : "" }}
        {{ $attributes(['value' => old($name)]) }}
    >

    <label for="{{ $id }}">{{ $slot }}</label>

    <x-form.error name="{{ $name }}" />
</span>