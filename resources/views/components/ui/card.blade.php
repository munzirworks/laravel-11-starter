@props(['title' => null])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white shadow-sm']) }}>
    @if ($title)
        <div class="border-b border-slate-200 px-6 py-4">
            <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>
</div>
