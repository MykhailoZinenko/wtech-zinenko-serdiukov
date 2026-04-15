@props(['field'])

@error($field)
    <span class="form-hint" style="color: var(--clr-red-light);">{{ $message }}</span>
@enderror
