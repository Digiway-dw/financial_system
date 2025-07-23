<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('lines.index') }}"
                    class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Lines
                </a>
                <div class="border-l border-gray-300 h-6"></div>
                <div>
                    <h1
                        class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                        Create New Line</h1>
                    <p class="mt-1 text-sm text-slate-600">Add a new financial line to the system</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
        <!-- Form Header -->
        <div class="px-8 py-6 border-b border-white/20 bg-white/60 backdrop-blur-sm">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Line Information</h2>
                    <p class="text-sm text-gray-600 mt-1">Please fill in the details for the new line</p>
                </div>
            </div>
        </div>

        <!-- Form Content -->
        <form wire:submit.prevent="createLine" class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mobile Number -->
                <div class="md:col-span-2">
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <x-input-label for="mobileNumber" :value="__('Mobile Number')" class="text-sm font-medium text-gray-700" />
                    </div>
                    <x-text-input wire:model="mobileNumber" id="mobileNumber" name="mobileNumber" type="text"
                        class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                        placeholder="Enter mobile number (11 digits)" maxlength="11" minlength="11" pattern="\d{11}"
                        required autofocus
                        x-on:input="event.target.value = event.target.value.replace(/[^\d]/g, '').slice(0,11)" />
                    <div class="text-xs text-gray-500 mt-1">Mobile number must be exactly 11 digits.</div>
                    @if (strlen($mobileNumber ?? '') > 0 && strlen($mobileNumber ?? '') != 11)
                        <div class="text-sm text-red-600 mt-1">Please enter all 11 digits of the mobile number.
                        </div>
                    @endif
                    <x-input-error class="mt-2" :messages="$errors->get('mobileNumber')" />
                </div>

                <!-- Current Balance -->
                <div>
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                        <x-input-label for="currentBalance" :value="__('Current Balance')"
                            class="text-sm font-medium text-gray-700" />
                    </div>
                    <div class="relative">
                        <x-text-input wire:model="currentBalance" id="currentBalance" name="currentBalance"
                            type="number" step="1" min="0"
                            class="block w-full pl-14 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                            required x-on:input="event.target.value = event.target.value.replace(/[^\d]/g, '')" />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">EGP</span>
                        </div>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('currentBalance')" />
                </div>

                <!-- Daily Limit -->
                <div>
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <x-input-label for="dailyLimit" :value="__('Daily Limit')" class="text-sm font-medium text-gray-700" />
                    </div>
                    <div class="relative">
                        <x-text-input wire:model="dailyLimit" id="dailyLimit" name="dailyLimit" type="number"
                            step="1" min="0"
                            class="block w-full pl-14 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                            required x-on:input="event.target.value = event.target.value.replace(/[^\d]/g, '')" />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">EGP</span>
                        </div>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('dailyLimit')" />
                </div>

                <!-- Monthly Limit -->
                <div>
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <x-input-label for="monthlyLimit" :value="__('Monthly Limit')"
                            class="text-sm font-medium text-gray-700" />
                    </div>
                    <div class="relative">
                        <x-text-input wire:model="monthlyLimit" id="monthlyLimit" name="monthlyLimit" type="number"
                            step="1" min="0"
                            class="block w-full pl-14 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                            required x-on:input="event.target.value = event.target.value.replace(/[^\d]/g, '')" />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">EGP</span>
                        </div>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('monthlyLimit')" />
                </div>

                <!-- Network -->
                <div>
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                        </svg>
                        <x-input-label for="network" :value="__('Network Provider')" class="text-sm font-medium text-gray-700" />
                    </div>
                    <select wire:model="network" id="network" name="network"
                        class="block w-full rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 text-sm"
                        required>
                        <option value="">Select Network Provider</option>
                        <option value="Vodafone">Vodafone</option>
                        <option value="Orange">Orange</option>
                        <option value="Etisalat">Etisalat</option>
                        <option value="We">We</option>
                        <option value="Fawry">Fawry</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('network')" />
                </div>

                <!-- Assigned Branch -->
                <div>
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <x-input-label for="branchId" :value="__('Assigned Branch')" class="text-sm font-medium text-gray-700" />
                    </div>
                    <select wire:model="branchId" id="branchId" name="branchId"
                        class="block w-full rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 text-sm"
                        required>
                        <option value="">Select Branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('branchId')" />
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-8 mt-8 border-t border-white/20">
                <div class="flex space-x-4">
                    @if (session('message'))
                        <div x-data="{ show: true }" x-show="show"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-90" x-init="setTimeout(() => show = false, 4000)"
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-green-50 border border-green-200">
                            <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium text-green-700">{{ session('message') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div x-data="{ show: true }" x-show="show"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-90" x-init="setTimeout(() => show = false, 4000)"
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-red-50 border border-red-200">
                            <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="text-sm font-medium text-red-700">{{ session('error') }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('lines.index') }}"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                        Cancel
                    </a>
                    <x-primary-button
                        class="px-8 py-3 bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create Line
                    </x-primary-button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
