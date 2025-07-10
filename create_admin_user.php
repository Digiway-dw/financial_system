<?php

// Bootstrap the Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Domain\Entities\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Create admin user
try {
    echo "Starting admin user creation...\n";

    // Check if admin role exists
    $adminRole = Role::where('name', 'admin')->first();
    if (!$adminRole) {
        echo "Creating admin role...\n";
        $adminRole = Role::create(['name' => 'admin']);

        // Add all permissions to admin role
        $permissions = \Spatie\Permission\Models\Permission::all();
        $adminRole->syncPermissions($permissions);
        echo "Admin role created with all permissions.\n";
    } else {
        echo "Admin role already exists.\n";
    }

    // Create or update admin user
    $admin = User::updateOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]
    );
    // Assign admin role
    $admin->assignRole('admin');

    echo "Done!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
