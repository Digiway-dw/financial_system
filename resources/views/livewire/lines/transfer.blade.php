<div class="p-4">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">نقل الخط: {{ $line->mobile_number }}</h3>

    <form wire:submit.prevent="transferLine" class="mt-6 space-y-6">
        <!-- Current Line Details -->
        <div class="mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400"><strong>المستخدم الحالي:</strong> {{ $line->user->name ?? 'N/A' }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400"><strong>الرصيد الحالي:</strong> {{ format_int($line->current_balance) }} EGP</p>
            <p class="text-sm text-gray-600 dark:text-gray-400"><strong>الشبكة:</strong> {{ $line->network ?? 'N/A' }}</p>
        </div>

        <!-- New User Selection -->
        <div>
            <x-input-label for="newUserId" :value="__('Select New User')" />
            <select wire:model="newUserId" id="newUserId" name="newUserId" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                <option value="">اختر مستخدم</option>
                @foreach ($users as $user)
                    <option value="{{ $user['id'] }}">{{ $user['name'] }} ({{ $user['branch']['name'] ?? 'N/A' }})</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('newUserId')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Transfer Line') }}</x-primary-button>

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