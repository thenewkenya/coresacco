@props([
    'variant' => 'outline',
])

@php
$classes = Flux::classes()
    ->add([
        'shrink-0',
    ])
    ->add(match($variant) {
        'outline' => 'fill-none stroke-current',
        'solid' => 'fill-current',
        'mini' => 'fill-current',
        'micro' => 'fill-current',
    });
@endphp

<svg {{ $attributes->class($classes) }} data-flux-icon xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
    @if ($variant === 'outline')
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 5v14m7-7H5M19 7V5a2 2 0 00-2-2H7a2 2 0 00-2 2v2m14 0v10a2 2 0 01-2 2H7a2 2 0 01-2-2V7m14 0H5"/>
    @else
        <path d="M19 3H5a2 2 0 00-2 2v2a1 1 0 001 1h16a1 1 0 001-1V5a2 2 0 00-2-2zM5 8a1 1 0 00-1 1v8a2 2 0 002 2h12a2 2 0 002-2V9a1 1 0 00-1-1H5zm7 3a2 2 0 100 4 2 2 0 000-4z"/>
    @endif
</svg> 