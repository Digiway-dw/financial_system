<div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-6 mt-8">
    <h2 class="text-2xl font-bold mb-4">Receive Transfer</h2>
    <form>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="phone_number" :value="__('Phone Number')" />
                <x-text-input id="phone_number" type="text" class="mt-1 block w-full" autocomplete="off" />
            </div>
            <div>
                <x-input-label for="client_code" :value="__('Client Code')" />
                <x-text-input id="client_code" type="text" class="mt-1 block w-full" autocomplete="off" />
            </div>
            <div>
                <x-input-label for="client_name" :value="__('Client Name')" />
                <x-text-input id="client_name" type="text" class="mt-1 block w-full" autocomplete="off" />
            </div>
            <div>
                <x-input-label for="gender" :value="__('Gender')" />
                <div class="flex items-center mt-1">
                    <label class="mr-4"><input type="radio" name="gender" value="male"> Male</label>
                    <label><input type="radio" name="gender" value="female"> Female</label>
                </div>
            </div>
            <div>
                <x-input-label for="to_client" :value="__('To Client (from history)')" />
                <select id="to_client" class="mt-1 block w-full rounded-lg border-gray-300">
                    <option value="">Select recipient</option>
                    <!-- Populate with previous recipients -->
                </select>
            </div>
            <div>
                <x-input-label for="amount" :value="__('Amount')" />
                <x-text-input id="amount" type="number" class="mt-1 block w-full border-blue-500" step="0.01" min="1" />
                <div class="text-xs text-gray-500 mt-1">Commission: 5 EGP for every 500 EGP</div>
            </div>
            <div>
                <x-input-label for="commission" :value="__('Commission (auto-calculated)')" />
                <x-text-input id="commission" type="number" class="mt-1 block w-full bg-gray-100" readonly />
            </div>
            <div>
                <x-input-label for="discount" :value="__('Discount (if any)')" />
                <x-text-input id="discount" type="number" class="mt-1 block w-full" step="0.01" min="0" />
                <div class="text-xs text-red-500 mt-1">If discount is applied, supervisor notification will be sent and transaction will be pending.</div>
            </div>
            <div>
                <x-input-label for="line_type" :value="__('Line Type')" />
                <select id="line_type" class="mt-1 block w-full rounded-lg border-gray-300">
                    <option value="internal">Internal</option>
                    <option value="external">External</option>
                </select>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <x-primary-button type="button">Add Transaction</x-primary-button>
        </div>
        <div class="mt-4 text-red-600 font-semibold">Warning: Transaction must not exceed wallet or line balance.</div>
    </form>
    <div class="mt-8">
        <h3 class="font-semibold">Employee: [Employee Name] | Branch: [Branch Name]</h3>
    </div>
    <div class="mt-8">
        <h4 class="font-semibold mb-2">Search Transactions</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-text-input type="text" placeholder="Phone Number" class="block w-full" />
            <x-text-input type="text" placeholder="Client Code" class="block w-full" />
            <x-text-input type="text" placeholder="Transaction Number" class="block w-full" />
            <x-text-input type="text" placeholder="To Client" class="block w-full" />
            <x-text-input type="date" placeholder="From Date" class="block w-full" />
            <x-text-input type="date" placeholder="To Date" class="block w-full" />
        </div>
    </div>
</div>
