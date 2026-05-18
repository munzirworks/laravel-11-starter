<x-dashboard-layout>
    <x-layout.page-header
        :title="__('Profile')"
        :description="__('Update your account information and password.')"
    />

    <div class="space-y-6">
        <x-ui.card :title="__('Profile information')">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-ui.card>

        <x-ui.card :title="__('Update password')">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-ui.card>

        <x-ui.card :title="__('Delete account')">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-ui.card>
    </div>
</x-dashboard-layout>
