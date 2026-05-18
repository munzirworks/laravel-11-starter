@props([
    'color' => 'slate',
])

@php
    $colors = [
        'indigo' => 'bg-indigo-100 text-indigo-800',
        'slate' => 'bg-slate-100 text-slate-800',
        'green' => 'bg-green-100 text-green-800',
    ];
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium '
        . ($colors[$color] ?? $colors['slate']),
]) }}>
    {{ $slot }}
</span>
