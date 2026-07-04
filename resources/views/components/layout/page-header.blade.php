@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    <h1 class="text-2xl font-bold tracking-tight text-black">{{ $title }}</h1>
    @if ($description)
        <p class="mt-1 text-sm text-black/60">{{ $description }}</p>
    @endif
</div>
