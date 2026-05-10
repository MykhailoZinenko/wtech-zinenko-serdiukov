@props(['field'])

@error($field)
    <span class="form-error">{{ $message }}</span>
@enderror
