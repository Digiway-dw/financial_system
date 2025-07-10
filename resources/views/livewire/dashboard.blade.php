<div>
    @php
    // Use our new helper to determine which dashboard component to render based on user role
    $dashboardComponent = App\Helpers\RoleUiHelper::getDashboardComponent();
    @endphp

    {{-- Dynamically render the appropriate dashboard based on user role --}}
    @if(View::exists('livewire.' . $dashboardComponent))
        @include('livewire.' . $dashboardComponent)
    @else
        <div class="p-6 bg-white rounded-lg shadow-md border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Welcome to the Financial System</h3>
            <p class="mt-2 text-gray-600">Your role-specific dashboard is not available. Please contact an administrator.</p>
        </div>
    @endif
</div>
