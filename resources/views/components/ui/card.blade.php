@props(['title' => null])

<div {{ $attributes->merge(['class' => 'rounded-3xl border border-black/5 bg-white shadow-sm']) }}>
    @if ($title)
        <div class="border-b border-black/5 px-6 py-4">
            <h3 class="text-base font-semibold text-black">{{ $title }}</h3>
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>
</div>
