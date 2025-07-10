<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Domain\Entities\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

// Check if admin role exists, create if not
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

echo "Done! You can now log in with:\n";
echo "Email: admin@example.com\n";
echo "Password: password\n";
