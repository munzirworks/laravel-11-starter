<header class="sticky top-0 z-10 flex h-16 shrink-0 items-center justify-between border-b border-slate-200 bg-white px-4 shadow-sm sm:px-6">
    <button
        type="button"
        @click="sidebarOpen = ! sidebarOpen"
        class="inline-flex items-center justify-center rounded-md p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 lg:hidden"
    >
        <span class="sr-only">{{ __('Open sidebar') }}</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <div class="hidden lg:block">
        <p class="text-sm text-slate-500">{{ __('Welcome back') }}, <span class="font-medium text-slate-900">{{ auth()->user()->name }}</span></p>
    </div>

    <div class="flex items-center gap-3">
        <x-ui.badge :color="auth()->user()->isAdmin() ? 'indigo' : 'slate'">
            {{ auth()->user()->role->label() }}
        </x-ui.badge>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
            >
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</header>
