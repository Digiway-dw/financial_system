<div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-6 mt-8">
    <h2 class="text-2xl font-bold mb-4">Cash Handling</h2>
    <form>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="client_code" :value="__('Client Code')" />
                <x-text-input id="client_code" type="text" class="mt-1 block w-full" autocomplete="off" />
                        </div>
            <div>
                <x-input-label for="client_phone" :value="__('Client Phone Number')" />
                <x-text-input id="client_phone" type="text" class="mt-1 block w-full" autocomplete="off" />
                    </div>
                    <div>
                <x-input-label for="client_name" :value="__('Client Name')" />
                <x-text-input id="client_name" type="text" class="mt-1 block w-full bg-gray-100" readonly />
                    </div>
            <div>
                <x-input-label for="employee_name" :value="__('Employee Name')" />
                <x-text-input id="employee_name" type="text" class="mt-1 block w-full bg-gray-100" value="[Employee Name]" readonly />
                </div>
            <div>
                <x-input-label for="branch_name" :value="__('Branch Name')" />
                <x-text-input id="branch_name" type="text" class="mt-1 block w-full bg-gray-100" value="[Branch Name]" readonly />
            </div>
            <div>
                <x-input-label for="date" :value="__('Date')" />
                <x-text-input id="date" type="date" class="mt-1 block w-full bg-gray-100" value="[Date]" readonly />
        </div>
            <div>
                <x-input-label for="time" :value="__('Time')" />
                <x-text-input id="time" type="time" class="mt-1 block w-full bg-gray-100" value="[Time]" readonly />
    </div>
            <div>
                <x-input-label for="amount" :value="__('Amount')" />
                <x-text-input id="amount" type="number" class="mt-1 block w-full border-yellow-500" step="0.01" min="1" />
                    </div>
            <div>
                <x-input-label for="transaction_type" :value="__('Transaction Type')" />
                <select id="transaction_type" class="mt-1 block w-full rounded-lg border-gray-300">
                    <option value="deposit">Deposit</option>
                    <option value="withdraw">Withdraw</option>
                </select>
                </div>
                <div>
                <x-input-label for="target" :value="__('Target')" />
                <select id="target" class="mt-1 block w-full rounded-lg border-gray-300">
                    <option value="line">Line</option>
                    <option value="vault">Vault</option>
                    <option value="inter-branch">Inter-Branch</option>
                    <option value="client-vault">Client Vault</option>
                </select>
                </div>
            <div>
                <x-input-label for="admin" :value="__('Admin (Full Permissions)')" />
                <input id="admin" type="checkbox" class="mt-2" />
                <span class="text-xs text-gray-500 ml-2">Check if you are an admin to enable unrestricted actions.</span>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <x-primary-button type="button">Submit Cash Operation</x-primary-button>
                            </div>
        <div class="mt-4 text-red-600 font-semibold">
            <ul class="list-disc ml-6">
                <li>Withdrawals from lines require admin approval.</li>
                <li>Withdrawals from vaults are pending until admin approval.</li>
                <li>Inter-branch transfers require confirmation by the receiving employee and admin review.</li>
                <li>Client vault deposits allowed only for registered clients with approved vaults at this branch.</li>
                <li>Withdrawals from client vaults must be done via the Send Transfer screen.</li>
                <li>Admin can deposit/withdraw without restrictions; these are not pending.</li>
            </ul>
                    </div>
        <div class="mt-4 text-green-600 font-semibold">
            Receipt will display: Employee Name, Branch Name, Date, and Time for every transaction.
        </div>
    </form>
</div>
