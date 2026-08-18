<x-app-layout>
    <x-slot name="header">
        Profile
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="card p-6 sm:p-8" x-reveal>
            <div class="max-w-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card p-6 sm:p-8" x-reveal>
            <div class="max-w-2xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card p-6 sm:p-8 border-rose-100" x-reveal>
            <div class="max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
