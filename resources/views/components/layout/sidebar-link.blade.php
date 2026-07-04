@props([
    'href',
    'active' => false,
    'icon' => 'home',
])

@php
    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />',
        'customers' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L4.5 4.5h15L21 9.75m-18 0A2.25 2.25 0 005.25 12h.75A2.25 2.25 0 008.25 9.75m-5.25 0V19.5A1.5 1.5 0 004.5 21h15a1.5 1.5 0 001.5-1.5V9.75m-12.75 0A2.25 2.25 0 0010.5 12h3A2.25 2.25 0 0015.75 9.75m-7.5 0A2.25 2.25 0 0010.5 12m5.25-2.25A2.25 2.25 0 0018 12h.75A2.25 2.25 0 0021 9.75" />',
        'products' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5L12 3 3 7.5m18 0L12 12m9-4.5v9L12 21m9-4.5L12 12m0 9L3 16.5m9 4.5v-9m0 0L3 7.5" />',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />',
    ];
@endphp

<a
    href="{{ $href }}"
    @click="if (window.innerWidth < 1024) sidebarOpen = false"
    {{ $attributes->merge([
        'class' => 'flex items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-medium transition '
            . ($active
                ? 'bg-[#FF2D20] text-white shadow-sm'
                : 'text-zinc-300 hover:bg-white/10 hover:text-white'),
    ]) }}
>
    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        {!! $icons[$icon] ?? $icons['home'] !!}
    </svg>
    <span class="font-medium">{{ $slot }}</span>
</a>
