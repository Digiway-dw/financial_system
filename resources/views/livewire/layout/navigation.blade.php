<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <span class="text-2xl font-bold text-blue-700 tracking-wide font-fidodido">Fido Dido</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @can('view-customers')
                        <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" wire:navigate>
                            {{ __('Customers') }}
                        </x-nav-link>
                    @endcan

                    <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.index') ||
                        request()->routeIs('transactions.create') ||
                        request()->routeIs('transactions.pending')" wire:navigate>
                        {{ __('Transactions') }}
                    </x-nav-link>

                    @canany(['send-transfer', 'send-transfer-pending'])
                        {{-- Removed New Transaction link --}}
                    @endcanany

                    @can('view-lines')
                        <x-nav-link :href="route('lines.index')" :active="request()->routeIs('lines.*')" wire:navigate>
                            {{ __('Lines') }}
                        </x-nav-link>
                    @endcan

                    @can('manage-safes')
                        <x-nav-link :href="route('safes.index')" :active="request()->routeIs('safes.index') ||
                            request()->routeIs('safes.edit') ||
                            request()->routeIs('safes.move')" wire:navigate>
                            {{ __('Safes') }}
                        </x-nav-link>
                    @endcan

                    @role('admin')
                        <x-nav-link :href="route('safes.move')" :active="request()->routeIs('safes.move')" wire:navigate>
                            {{ __('Move Cash') }}
                        </x-nav-link>
                    @endrole

                    @can('view-branches')
                        <x-nav-link :href="route('branches.index')" :active="request()->routeIs('branches.*')" wire:navigate>
                            {{ __('Branches') }}
                        </x-nav-link>
                    @endcan

                    @can('manage-users')
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" wire:navigate>
                            {{ __('Users') }}
                        </x-nav-link>
                    @endcan

                    @can('view-all-reports')
                        <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" wire:navigate>
                            {{ __('Reports') }}
                        </x-nav-link>
                    @endcan

                    @can('view-audit-log')
                        <x-nav-link :href="route('audit-log.index')" :active="request()->routeIs('audit-log.*')" wire:navigate>
                            {{ __('Audit Log') }}
                        </x-nav-link>
                    @endcan

                    @role('admin')
                        <x-dropdown align="right" width="48" class="hidden sm:inline-flex">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-800 focus:outline-none transition ease-in-out duration-150">
                                    <span>{{ __('Permissions') }}</span>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('permissions.index')" wire:navigate>
                                    {{ __('Permissions List') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('permissions.roles')" wire:navigate>
                                    {{ __('Role Permissions') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>

                        <x-nav-link :href="route('permissions.index')" :active="request()->routeIs('permissions.*')" wire:navigate class="sm:hidden">
                            {{ __('Permissions') }}
                        </x-nav-link>
                    @endrole
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <livewire:notification-bell />
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-800 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                                x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-600 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @can('view-customers')
                <x-responsive-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" wire:navigate>
                    {{ __('Customers') }}
                </x-responsive-nav-link>
            @endcan

            <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.index') ||
                request()->routeIs('transactions.create') ||
                request()->routeIs('transactions.pending')" wire:navigate>
                {{ __('Transactions') }}
            </x-responsive-nav-link>

            @canany(['send-transfer', 'send-transfer-pending'])
                {{-- Removed New Transaction link --}}
            @endcanany

            @can('view-lines')
                <x-responsive-nav-link :href="route('lines.index')" :active="request()->routeIs('lines.*')" wire:navigate>
                    {{ __('Lines') }}
                </x-responsive-nav-link>
            @endcan

            @can('manage-safes')
                <x-responsive-nav-link :href="route('safes.index')" :active="request()->routeIs('safes.index') ||
                    request()->routeIs('safes.edit') ||
                    request()->routeIs('safes.move')" wire:navigate>
                    {{ __('Safes') }}
                </x-responsive-nav-link>
            @endcan

            @role('admin')
                <x-responsive-nav-link :href="route('safes.move')" :active="request()->routeIs('safes.move')" wire:navigate>
                    {{ __('Move Cash') }}
                </x-responsive-nav-link>
            @endrole

            @can('view-branches')
                <x-responsive-nav-link :href="route('branches.index')" :active="request()->routeIs('branches.*')" wire:navigate>
                    {{ __('Branches') }}
                </x-responsive-nav-link>
            @endcan

            @can('manage-users')
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" wire:navigate>
                    {{ __('Users') }}
                </x-responsive-nav-link>
            @endcan

            @can('view-all-reports')
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" wire:navigate>
                    {{ __('Reports') }}
                </x-responsive-nav-link>
            @endcan

            @can('view-audit-log')
                <x-responsive-nav-link :href="route('audit-log.index')" :active="request()->routeIs('audit-log.*')" wire:navigate>
                    {{ __('Audit Log') }}
                </x-responsive-nav-link>
            @endcan

            @role('admin')
                <x-responsive-nav-link :href="route('permissions.index')" :active="request()->routeIs('permissions.index')" wire:navigate>
                    {{ __('Permissions List') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('permissions.roles')" :active="request()->routeIs('permissions.roles')" wire:navigate>
                    {{ __('Role Permissions') }}
                </x-responsive-nav-link>
            @endrole
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                    x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
