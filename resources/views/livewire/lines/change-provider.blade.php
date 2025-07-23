<div class="p-4">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">تغيير مزود الخط: {{ $line->mobile_number }}</h3>

    <form wire:submit.prevent="updateNetwork" class="mt-6 space-y-6">
        <!-- Current Network Details -->
        <div class="mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400"><strong>الشبكة الحالية:</strong> {{ $line->network ?? 'N/A' }}</p>
        </div>

        <!-- New Network Selection -->
        <div>
            <x-input-label for="newNetwork" :value="__('Select New Network Provider')" />
            <select wire:model="newNetwork" id="newNetwork" name="newNetwork" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                @foreach ($networks as $network)
                    <option value="{{ $network }}">{{ $network }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('newNetwork')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Update Provider') }}</x-primary-button>

            @if (session('message'))
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ session('message') }}</p>
            @endif

            @if (session('error'))
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-red-600 dark:text-red-400"
                >{{ session('error') }}</p>
            @endif
        </div>
    </form>
</div> 