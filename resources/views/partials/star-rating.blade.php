@php
    $count = $count ?? null;
    $size = $size ?? 'text-base';
@endphp
<div class="flex items-center gap-1">
    <div class="flex gap-0.5 text-terroir-gold">
        @for($s = 1; $s <= 5; $s++)
            <span class="material-symbols-outlined {{ $size }}{{ $s <= round($rating) ? ' is-filled' : '' }}">star</span>
        @endfor
    </div>
    <span class="text-sm font-semibold text-terroir-dark">{{ number_format($rating, 1) }}</span>
    @if($count !== null)
        <span class="text-xs text-terroir-dark/50">({{ $count }} avis)</span>
    @endif
</div>
