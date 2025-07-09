<!-- Deposit Cash Transaction Page -->
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50">
    <div class="bg-white/80 rounded-2xl shadow-xl p-10 flex flex-col items-center gap-8 w-full max-w-2xl">
        <h2 class="text-3xl font-bold text-amber-700 mb-6">ايداع نقدي</h2>
        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="w-full mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-xl text-center font-semibold shadow">
                {{ session('message') }}
            </div>
        @endif
        <!-- Deposit Type Selection -->
        <div class="flex gap-4 mb-8">
            <button wire:click="$set('depositType', 'direct')" type="button" class="px-6 py-2 rounded-xl font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all duration-200"
                :class="{'bg-amber-600 text-white': depositType === 'direct', 'bg-amber-100 text-amber-700': depositType !== 'direct'}">
                مباشر
            </button>
            <button wire:click="$set('depositType', 'user')" type="button" class="px-6 py-2 rounded-xl font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all duration-200"
                :class="{'bg-amber-600 text-white': depositType === 'user', 'bg-amber-100 text-amber-700': depositType !== 'user'}">
                مستخدم
            </button>
            <button wire:click="$set('depositType', 'client_wallet')" type="button" class="px-6 py-2 rounded-xl font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all duration-200"
                :class="{'bg-amber-600 text-white': depositType === 'client_wallet', 'bg-amber-100 text-amber-700': depositType !== 'client_wallet'}">
                محفظة عميل
            </button>
            <button wire:click="$set('depositType', 'admin')" type="button" class="px-6 py-2 rounded-xl font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all duration-200"
                :class="{'bg-amber-600 text-white': depositType === 'admin', 'bg-amber-100 text-amber-700': depositType !== 'admin'}">
                اداري
            </button>
        </div>
        <!-- Dynamic Fields -->
        <form wire:submit.prevent="submitDeposit" class="w-full space-y-6">
            <!-- Safe Selection Dropdown -->
            <div>
                <label class="block mb-1 font-medium">الخزنة</label>
                <select wire:model="safeId" class="w-full rounded-xl border px-4 py-2">
                    @foreach ($branchSafes as $safe)
                        <option value="{{ $safe->id }}">{{ $safe->name }}</option>
                    @endforeach
                </select>
            </div>
            @if ($depositType === 'direct')
                <div>
                    <label class="block mb-1 font-medium">اسم العميل</label>
                    <input wire:model="customerName" type="text" class="w-full rounded-xl border px-4 py-2" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">المبلغ</label>
                    <input wire:model="amount" type="number" class="w-full rounded-xl border px-4 py-2" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">ملاحظات</label>
                    <input wire:model="notes" type="text" class="w-full rounded-xl border px-4 py-2" />
                </div>
            @elseif ($depositType === 'user')
                <div>
                    <label class="block mb-1 font-medium">المستخدم</label>
                    <select wire:model="userId" class="w-full rounded-xl border px-4 py-2">
                        <option value="">اختر المستخدم</option>
                        @foreach ($branchUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1 font-medium">المبلغ</label>
                    <input wire:model="amount" type="number" class="w-full rounded-xl border px-4 py-2" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">ملاحظات</label>
                    <input wire:model="notes" type="text" class="w-full rounded-xl border px-4 py-2" />
                </div>
            @elseif ($depositType === 'client_wallet')
                <div>
                    <label class="block mb-1 font-medium">كود العميل</label>
                    <input wire:model="clientCode" type="text" class="w-full rounded-xl border px-4 py-2" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">رقم العميل</label>
                    <input wire:model="clientNumber" type="text" class="w-full rounded-xl border px-4 py-2" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">الرقم القومي</label>
                    <input wire:model="clientNationalNumber" type="text" maxlength="14" class="w-full rounded-xl border px-4 py-2" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">المبلغ</label>
                    <input wire:model="amount" type="number" class="w-full rounded-xl border px-4 py-2" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">ملاحظات</label>
                    <input wire:model="notes" type="text" class="w-full rounded-xl border px-4 py-2" />
                </div>
            @elseif ($depositType === 'admin')
                <div>
                    <label class="block mb-1 font-medium">المبلغ</label>
                    <input wire:model="amount" type="number" class="w-full rounded-xl border px-4 py-2" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">ملاحظات</label>
                    <input wire:model="notes" type="text" class="w-full rounded-xl border px-4 py-2" />
                </div>
            @endif
            <div class="flex justify-end">
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold rounded-xl shadow-lg hover:from-amber-600 hover:to-orange-700 transform hover:scale-105 transition-all duration-200">
                    حفظ الإيداع
                </button>
            </div>
        </form>
    </div>
</div> 