<div class="bg-white shadow-sm rounded-lg border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-xl font-semibold text-gray-900">Create New Customer</h3>
        <p class="mt-1 text-sm text-gray-600">Add a new customer to the system with their details and preferences.</p>
    </div>

    <form wire:submit.prevent="createCustomer" class="p-6 space-y-6">
        <!-- Name -->
        <div class="space-y-2">
            <x-input-label for="name" :value="__('Customer Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full"
                required autofocus />
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <!-- Mobile Numbers -->
        <div class="space-y-2">
            <x-input-label :value="__('Mobile Numbers')" />
            <div class="space-y-3">
                @foreach ($mobileNumbers as $i => $number)
                    <div class="flex gap-3 items-center">
                        <x-text-input wire:model="mobileNumbers.{{ $i }}" type="text" class="flex-1"
                            placeholder="Enter mobile number" required />
                        @if (count($mobileNumbers) > 1)
                            <button type="button" wire:click="removeMobileNumber({{ $i }})"
                                class="inline-flex items-center px-3 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                    <x-input-error class="mt-1" :messages="$errors->get('mobileNumbers.' . $i)" />
                @endforeach
                <button type="button" wire:click="addMobileNumber"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('Add Number') }}
                </button>
            </div>
        </div>

        <!-- Customer Code -->
        <!-- Removed: Customer code is now auto-generated -->

        <!-- Gender -->
        <div class="space-y-2">
            <x-input-label for="gender" :value="__('Gender')" class="text-sm font-medium text-gray-700" />
            <select wire:model="gender" id="gender" name="gender"
                class="mt-1 block w-full border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 focus:ring-1 rounded-lg shadow-sm"
                required>
                <option value="" class="text-gray-500">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
            <x-input-error class="mt-1" :messages="$errors->get('gender')" />
        </div>

        <!-- Balance -->
        <div class="space-y-2">
            <x-input-label for="balance" :value="__('Balance')" />
            <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">EGP</span>
                <x-text-input wire:model="balance" id="balance" name="balance" type="number" step="0.01"
                    class="pl-12 block w-full" placeholder="0.00" required />
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('balance')" />
        </div>

        <!-- Is Client -->
        <div class="space-y-2">
            <label for="is_client"
                class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="checkbox" wire:model="is_client" id="is_client" name="is_client"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 focus:ring-1" />
                <div class="ml-3">
                    <span class="text-sm font-medium text-gray-700">{{ __('Is Client') }}</span>
                    <p class="text-xs text-gray-500">Mark this customer as a client for special handling</p>
                </div>
            </label>
            <x-input-error class="mt-1" :messages="$errors->get('is_client')" />
        </div>

        <!-- Agent ID -->
        <div class="space-y-2">
            <x-input-label for="agent_id" :value="__('Agent')" class="text-sm font-medium text-gray-700" />
            <select wire:model="agent_id" id="agent_id" name="agent_id"
                class="mt-1 block w-full border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 focus:ring-1 rounded-lg shadow-sm">
                <option value="" class="text-gray-500">Select Agent (Optional)</option>
                @foreach ($agents as $agent)
                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-1" :messages="$errors->get('agent_id')" />
        </div>

        <!-- Branch ID -->
        <div class="space-y-2">
            <x-input-label for="branch_id" :value="__('Branch')" class="text-sm font-medium text-gray-700" />
            <select wire:model="branch_id" id="branch_id" name="branch_id"
                class="mt-1 block w-full border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 focus:ring-1 rounded-lg shadow-sm"
                required>
                <option value="" class="text-gray-500">Select Branch</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-1" :messages="$errors->get('branch_id')" />
        </div>

        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <x-primary-button
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white font-medium rounded-lg shadow-sm">
                {{ __('Create Customer') }}
            </x-primary-button>

            <div class="flex space-x-4">
                @if (session('message'))
                    <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90" x-init="setTimeout(() => show = false, 4000)"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-green-50 border border-green-200">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="text-sm font-medium text-green-700">{{ session('message') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90" x-init="setTimeout(() => show = false, 4000)"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-red-50 border border-red-200">
                        <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="text-sm font-medium text-red-700">{{ session('error') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>
