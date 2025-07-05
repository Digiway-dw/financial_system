@props(['type' => 'info', 'dismissible' => false])

@php
$classes = match($type) {
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'error' => 'bg-red-50 border-red-200 text-red-800',
    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
    'info' => 'bg-blue-50 border-blue-200 text-blue-800',
    default => 'bg-gray-50 border-gray-200 text-gray-800',
};
@endphp

<div {{ $attributes->merge(['class' => "border rounded-lg p-4 {$classes}"]) }}>
    <div class="flex items-center">
        <div class="flex-shrink-0">
            @if($type === 'success')
                <x-heroicon-o-check-circle class="w-5 h-5" />
            @elseif($type === 'error')
                <x-heroicon-o-x-circle class="w-5 h-5" />
            @elseif($type === 'warning')
                <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
            @else
                <x-heroicon-o-information-circle class="w-5 h-5" />
            @endif
        </div>
        <div class="ml-3">
            {{ $slot }}
        </div>
        @if($dismissible)
            <div class="ml-auto">
                <button type="button" class="text-gray-400 hover:text-gray-600">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>
        @endif
    </div>
</div>
