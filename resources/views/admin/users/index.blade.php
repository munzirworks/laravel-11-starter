<x-dashboard-layout>
    <x-layout.page-header
        :title="__('User management')"
        :description="__('Administrator-only area for managing system users.')"
    />

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">{{ __('Name') }}</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">{{ __('Email') }}</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">{{ __('Role') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-4 py-3 text-slate-900">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge :color="$user->isAdmin() ? 'indigo' : 'slate'">
                                    {{ $user->role->label() }}
                                </x-ui.badge>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </x-ui.card>
</x-dashboard-layout>
