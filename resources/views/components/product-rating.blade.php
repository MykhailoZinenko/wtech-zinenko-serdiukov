@props([
    'rating' => 0,
    'count' => null,
    'showCount' => true,
])
@php
    $value = (float) $rating;
    $full = (int) floor($value);
    $hasHalf = ($value - $full) >= 0.25 && ($value - $full) < 0.75;
    $hasFull = ($value - $full) >= 0.75;
    if ($hasFull) {
        $full++;
        $hasHalf = false;
    }
    $empty = max(0, 5 - $full - ($hasHalf ? 1 : 0));
@endphp
<div {{ $attributes->merge(['class' => 'product-rating', 'aria-label' => 'Rating: ' . number_format($value, 1) . ' out of 5']) }}>
    @for ($i = 0; $i < $full; $i++)<i class="bi bi-star-fill"></i>@endfor
    @if ($hasHalf)<i class="bi bi-star-half"></i>@endif
    @for ($i = 0; $i < $empty; $i++)<i class="bi bi-star"></i>@endfor
    @if ($showCount && $count !== null)
        <span class="rating-count">({{ $count }})</span>
    @endif
</div>
