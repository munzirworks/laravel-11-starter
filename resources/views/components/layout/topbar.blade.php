<header class="sticky top-0 z-10 flex h-16 shrink-0 items-center justify-between border-b border-black/5 bg-white px-4 shadow-sm sm:px-6">
    <button
        type="button"
        @click="sidebarOpen = ! sidebarOpen"
        class="inline-flex items-center justify-center rounded-2xl p-2 text-black/60 transition hover:bg-black/5 hover:text-black lg:hidden"
    >
        <span class="sr-only">{{ __('Open sidebar') }}</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <div class="hidden lg:block">
        <p class="text-sm text-black/50">{{ __('Welcome back') }}, <span class="font-medium text-black">{{ auth()->user()->name }}</span></p>
    </div>

    <div class="flex items-center gap-3">
        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ auth()->user()->isAdmin() ? 'bg-[#FF2D20]/10 text-[#FF2D20]' : 'bg-black/5 text-black/70' }}">
            {{ auth()->user()->role->label() }}
        </span>

        <button
            type="button"
            @click="logout()"
            :disabled="loggingOut"
            class="rounded-2xl px-3 py-1.5 text-sm font-medium text-black/80 ring-1 ring-black/10 transition hover:bg-black/5 disabled:opacity-60"
        >
            <span x-show="! loggingOut">{{ __('Log Out') }}</span>
            <span x-show="loggingOut" x-cloak>{{ __('Logging out...') }}</span>
        </button>
    </div>
</header>
