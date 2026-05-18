<aside
    class="fixed inset-y-0 left-0 z-50 flex w-64 shrink-0 flex-col bg-slate-900 text-slate-100 shadow-xl transition-transform duration-200 ease-in-out lg:static lg:z-auto lg:translate-x-0 lg:shadow-none"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:!translate-x-0'"
>
    <div class="flex h-16 shrink-0 items-center gap-2 border-b border-slate-800 px-6">
        <x-application-logo class="h-8 w-auto fill-current text-indigo-400" />
        <span class="text-lg font-semibold tracking-tight text-white">{{ config('app.name') }}</span>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
            {{ __('Main') }}
        </p>

        <x-layout.sidebar-link
            :href="route('dashboard')"
            :active="request()->routeIs('dashboard')"
            icon="home"
        >
            {{ __('Dashboard') }}
        </x-layout.sidebar-link>

        <x-layout.sidebar-link
            :href="route('profile.edit')"
            :active="request()->routeIs('profile.*')"
            icon="user"
        >
            {{ __('Profile') }}
        </x-layout.sidebar-link>

        @if (auth()->user()->isAdmin())
            <p class="px-3 pb-2 pt-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                {{ __('Administration') }}
            </p>

            <x-layout.sidebar-link
                :href="route('admin.users.index')"
                :active="request()->routeIs('admin.*')"
                icon="users"
            >
                {{ __('Users') }}
            </x-layout.sidebar-link>
        @endif
    </nav>

    <div class="shrink-0 border-t border-slate-800 p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-500 text-sm font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ auth()->user()->role->label() }}</p>
            </div>
        </div>
    </div>
</aside>
