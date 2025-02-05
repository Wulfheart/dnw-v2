@props(['key', 'label'])

<div>
    <label for="{{ $key }}">{{ $label }}</label>
    {{ $slot }}
    @error($key)
        <div class="error">
            {{ $message }}
        </div>
    @enderror
</div>
