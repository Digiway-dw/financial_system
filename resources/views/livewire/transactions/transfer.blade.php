<div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-6 mt-8">
    <h2 class="text-2xl font-bold mb-4">التحويل الداخلي/الخارجي</h2>
    <form wire:submit.prevent="addTransaction">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="phone_number" :value="__('Phone Number')" />
                <x-text-input id="phone_number" type="text" wire:model.lazy="phone_number" class="mt-1 block w-full" />
            </div>
            <div>
                <x-input-label for="client_code" :value="__('Client Code')" />
                <x-text-input id="client_code" type="text" wire:model.lazy="client_code" class="mt-1 block w-full" />
            </div>
            <div>
                <x-input-label for="client_name" :value="__('Client Name')" />
                <x-text-input id="client_name" type="text" wire:model.defer="client_name" class="mt-1 block w-full" :readonly="$client_found" />
            </div>
            <div>
                <x-input-label for="gender" :value="__('Gender')" />
                <div class="flex items-center mt-1">
                    <label class="mr-4"><input type="radio" wire:model="gender" value="male"> ذكر</label>
                    <label><input type="radio" wire:model="gender" value="female"> أنثى</label>
                </div>
            </div>
            <div>
                <x-input-label for="to_client" :value="__('To Client')" />
                <select id="to_client" wire:model="to_client" class="mt-1 block w-full rounded-lg border-gray-300">
                    <option value="">اختر المستلم</option>
                    @foreach($previous_recipients as $recipient)
                        <option value="{{ $recipient }}">{{ $recipient }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="amount" :value="__('Amount')" />
                <x-text-input id="amount" type="number" wire:model="amount" class="mt-1 block w-full" step="0.01" min="1" />
            </div>
            <div>
                <x-input-label for="commission" :value="__('Commission')" />
                <x-text-input id="commission" type="number" wire:model="commission" class="mt-1 block w-full bg-gray-100" readonly />
            </div>
            <div>
                <x-input-label for="discount" :value="__('Discount (optional)')" />
                <x-text-input id="discount" type="number" wire:model="discount" class="mt-1 block w-full" step="0.01" min="0" />
            </div>
            <div>
                <x-input-label for="line_type" :value="__('Line Type')" />
                <select id="line_type" wire:model="line_type" class="mt-1 block w-full rounded-lg border-gray-300">
                    <option value="internal">داخلي</option>
                    <option value="external">خارجي</option>
                </select>
            </div>
            <div>
                <x-input-label for="collect_from_wallet" :value="__('Collect from Client Wallet')" />
                <input id="collect_from_wallet" type="checkbox" wire:model="collect_from_wallet" class="mt-2" />
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <x-primary-button type="submit">إضافة المعاملة</x-primary-button>
        </div>
        @if($warning)
            <div class="mt-4 text-red-600 font-semibold">{{ $warning }}</div>
        @endif
        @if(session()->has('message'))
            <div class="mt-4 text-green-600 font-semibold">{{ session('message') }}</div>
        @endif
    </form>
    <div class="mt-8">
        <h3 class="font-semibold">الموظف: {{ $employee_name }} | الفرع: {{ $branch_name }}</h3>
    </div>
</div> 