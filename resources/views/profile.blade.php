<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('My Profile') }}
            </h2>
            <span
                class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">{{ auth()->user()->getRoleNames()->first() }}</span>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-6">
                <!-- Profile Sidebar -->
                <div class="md:col-span-1">
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="p-6 text-center">
                            <div
                                class="h-24 w-24 rounded-full bg-blue-100 mx-auto mb-4 flex items-center justify-center">
                                <span class="text-3xl text-blue-600 font-bold">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h3>
                            <p class="text-gray-500 mb-4">{{ $user->email }}</p>
                            <div class="border-t border-gray-100 pt-4 mt-2">
                                <div class="flex flex-col space-y-2">
                                    @if ($user->branch)
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-500">Branch</span>
                                            <span class="text-sm font-medium">{{ $user->branch->name }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Role</span>
                                        <span class="text-sm font-medium">{{ $user->getRoleNames()->first() }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Member Since</span>
                                        <span class="text-sm font-medium">{{ $user->created_at->format('M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Profile Information -->
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="p-6">
                            <h2
                                class="text-lg font-semibold text-gray-800 border-b border-gray-100 pb-3 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ __('Profile Information') }}
                            </h2>

                            <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                                @csrf
                                @method('patch')

                                <div>
                                    <x-input-label for="name" :value="__('Name')" class="text-gray-700" />
                                    <x-text-input id="name" name="name" type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
                                    <x-text-input id="email" name="email" type="email"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        :value="old('email', $user->email)" required autocomplete="username" />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <div class="flex items-center justify-end gap-4 pt-2">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ __('Save Changes') }}
                                    </button>

                                    @if (session('status') === 'profile-updated')
                                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                                            class="text-sm text-green-600">{{ __('Saved successfully!') }}</p>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Update Password -->
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="p-6">
                            <h2
                                class="text-lg font-semibold text-gray-800 border-b border-gray-100 pb-3 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ __('Update Password') }}
                            </h2>

                            <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                                @csrf
                                @method('put')

                                <div>
                                    <x-input-label for="current_password" :value="__('Current Password')" class="text-gray-700" />
                                    <x-text-input id="current_password" name="current_password" type="password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        autocomplete="current-password" />
                                    <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password" :value="__('New Password')" class="text-gray-700" />
                                    <x-text-input id="password" name="password" type="password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm Password')"
                                        class="text-gray-700" />
                                    <x-text-input id="password_confirmation" name="password_confirmation"
                                        type="password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>

                                <div class="flex items-center justify-end gap-4 pt-2">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ __('Update Password') }}
                                    </button>

                                    @if (session('status') === 'password-updated')
                                        <p x-data="{ show: true }" x-show="show" x-transition
                                            x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600">
                                            {{ __('Password updated!') }}</p>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Delete Account -->
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="p-6">
                            <h2
                                class="text-lg font-semibold text-gray-800 border-b border-gray-100 pb-3 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ __('Delete Account') }}
                            </h2>

                            <p class="text-sm text-gray-600 mb-4">
                                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
                            </p>

                            <div class="mt-4">
                                <button x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Delete Account') }}
                                </button>
                            </div>

                            <div x-data="{ show: false, name: 'confirm-user-deletion' }" x-show="show"
                                x-on:open-modal.window="show = ($event.detail == name)"
                                x-on:close.window="show = false" x-on:keydown.escape.window="show = false"
                                class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" style="display: none;">
                                <div class="fixed inset-0 transform transition-all" x-on:click="show = false">
                                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                </div>

                                <div
                                    class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto">
                                    <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                                        @csrf
                                        @method('delete')

                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Are you sure you want to delete your account?') }}
                                        </h2>

                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                                        </p>

                                        <div class="mt-6">
                                            <x-input-label for="password" value="{{ __('Password') }}"
                                                class="sr-only" />

                                            <x-text-input id="password" name="password" type="password"
                                                class="mt-1 block w-3/4 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                placeholder="{{ __('Password') }}" />

                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>

                                        <div class="mt-6 flex justify-end">
                                            <button type="button" x-on:click="show = false"
                                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                {{ __('Cancel') }}
                                            </button>

                                            <button type="submit"
                                                class="ms-3 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                {{ __('Delete Account') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
