@props(['name', 'class', 'containerClass', 'placeholder', 'required' => false])

<div class="{{ $containerClass }} block">
    <textarea 
        name="{{ $name }}"
        id="{{ $name }}"
        placeholder="{{ $placeholder }}"
        class="{{ $class }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes(['rows' => 1]) }}
    >{{ $slot = old($name) }}</textarea>

    <x-form.error name="{{ $name }}" />
</div>