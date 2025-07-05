<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Icon Test - Financial System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Icon System Test</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Heroicons Test -->
            <div class="bg-blue-50 p-4 rounded-lg">
                <h2 class="font-semibold text-blue-800 mb-3">Heroicons</h2>
                <div class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <x-heroicon-o-currency-dollar class="w-5 h-5 text-green-600" />
                        <span>Currency Dollar</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <x-heroicon-o-building-library class="w-5 h-5 text-blue-600" />
                        <span>Bank</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <x-heroicon-o-users class="w-5 h-5 text-purple-600" />
                        <span>Users</span>
                    </div>
                </div>
            </div>

            <!-- Custom Alert Component -->
            <div class="bg-green-50 p-4 rounded-lg">
                <h2 class="font-semibold text-green-800 mb-3">Custom Components</h2>
                <x-alert type="success" class="mb-2">
                    Success message!
                </x-alert>
                <x-alert type="warning" class="mb-2">
                    Warning message!
                </x-alert>
                <x-alert type="error">
                    Error message!
                </x-alert>
            </div>

            <!-- Helper Functions -->
            <div class="bg-yellow-50 p-4 rounded-lg">
                <h2 class="font-semibold text-yellow-800 mb-3">Helper Functions</h2>
                <div class="space-y-2 text-sm">
                    <div>Money: @money(1500.75)</div>
                    <div>Date: @dateFormat(now())</div>
                    <div>Icon Helper: {!! icon('money', ['class' => 'w-4 h-4 text-green-600']) !!}</div>
                </div>
            </div>
        </div>

        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
            <h2 class="font-semibold text-gray-800 mb-3">System Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <strong>App Name:</strong> {{ $appName ?? 'Financial System' }}
                </div>
                <div>
                    <strong>Environment:</strong> {{ app()->environment() }}
                </div>
                <div>
                    <strong>Year:</strong> {{ $currentYear ?? date('Y') }}
                </div>
                <div>
                    <strong>Production:</strong> {{ $isProduction ? 'Yes' : 'No' }}
                </div>
            </div>
        </div>
    </div>
</body>

</html>
