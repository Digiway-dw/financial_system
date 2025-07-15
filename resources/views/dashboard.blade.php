<x-app-layout>
    {{-- Removed the <x-slot name="header"> section with the large welcome message --}}

    <div class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden rounded-xl shadow-md border border-gray-100">
                <div class="p-6 text-gray-900">

                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-b from-blue-100 to-transparent h-20 rounded-t-xl -mx-6 -mt-6">
                        </div>
                        <div class="relative">
                            @livewire('dashboard')
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</x-app-layout>
