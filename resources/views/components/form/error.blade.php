@props(['name'])

@error($name)
    <div class="error">{{ $message }}</div>
@enderror