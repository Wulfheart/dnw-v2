@props(['name', 'label'])

<div>
    <label for="{{$name}}">{{$label}}</label>
    {{ $slot }}
    @error('userId')
    <div class="error">
        {{ $message }}
    </div>
    @enderror
</div>
