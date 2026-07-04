<aside
    class="fixed inset-y-0 left-0 z-50 flex w-64 shrink-0 flex-col border-r border-white/10 bg-black text-zinc-100 shadow-[0_20px_80px_rgba(15,23,42,0.25)] transition-transform duration-200 ease-in-out lg:static lg:z-auto lg:translate-x-0 lg:shadow-none"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:!translate-x-0'"
>
    <div class="flex h-16 shrink-0 items-center gap-3 border-b border-white/10 px-6">
        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#FF2D20] text-base font-bold text-white">E</div>
        <span class="text-lg font-semibold tracking-tight text-white">ERP Core</span>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <p class="px-4 pb-2 text-xs font-semibold uppercase tracking-wider text-zinc-500">
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

        @if (auth()->user()->can('customer.view'))
            <x-layout.sidebar-link
                :href="url('/customers')"
                :active="request()->is('customers') || request()->is('customers/*')"
                icon="customers"
            >
                {{ __('Customers') }}
            </x-layout.sidebar-link>
        @endif

        @if (auth()->user()->can('product.view'))
            <x-layout.sidebar-link
                :href="url('/products')"
                :active="request()->is('products') || request()->is('products/*')"
                icon="products"
            >
                {{ __('Products') }}
            </x-layout.sidebar-link>
        @endif

        @if (auth()->user()->isAdmin())
            <p class="px-4 pb-2 pt-4 text-xs font-semibold uppercase tracking-wider text-zinc-500">
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

    <div class="mt-auto shrink-0 border-t border-white/10 p-4">
        <div class="flex items-center gap-3 rounded-[24px] bg-white/5 p-3 ring-1 ring-white/10 backdrop-blur-sm">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-zinc-800 text-sm font-semibold text-white ring-1 ring-white/10">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-white/60">{{ auth()->user()->role->label() }}</p>
            </div>
        </div>
    </div>
</aside>
