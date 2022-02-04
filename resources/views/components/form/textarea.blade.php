@props(['name', 'class', 'containerClass', 'placeholder', 'required' => false])

<div class="{{ $containerClass }} block" id="{{ $name }}Container">
    <textarea 
        name="{{ $name }}"
        id="{{ $name }}"
        placeholder="{{ $placeholder }}"
        class="{{ $class }}"
        maxlength="2000"
        {{ $required ? 'required' : '' }}
        {{ $attributes(['rows' => 1]) }}
    >{{ old($name) ? old($name) : $slot }}</textarea>

    <x-form.error name="{{ $name }}" />
</div>