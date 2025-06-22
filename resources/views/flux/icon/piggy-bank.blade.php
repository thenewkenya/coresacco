@php
$attributes = $attributes->class([
    'shrink-0',
])->merge([
    'fill' => 'currentColor',
    'viewBox' => '0 0 24 24',
]);
@endphp

<svg {{ $attributes }}>
    <path d="M18.5 10c-.83 0-1.5-.67-1.5-1.5S17.67 7 18.5 7s1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM6 10c-2.76 0-5 2.24-5 5s2.24 5 5 5h12c2.76 0 5-2.24 5-5s-2.24-5-5-5v-1c0-2.76-2.24-5-5-5S8 6.24 8 9v1H6zm7-1V9c0-1.66 1.34-3 3-3s3 1.34 3 3v1h2c1.66 0 3 1.34 3 3s-1.34 3-3 3H6c-1.66 0-3-1.34-3-3s1.34-3 3-3h7z"/>
    <circle cx="13" cy="14" r="1"/>
    <path d="M9 17v-2h2v2H9zm4 0v-2h2v2h-2z"/>
</svg> 