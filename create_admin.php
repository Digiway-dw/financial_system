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

// Create admin user if not exists
$admin = User::where('email', 'admin@example.com')->first();
if (!$admin) {
    echo "Creating admin user...\n";
    $admin = new User();
    $admin->name = 'Admin';
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('password');
    $admin->email_verified_at = now();
    $admin->save();

    // Assign admin role
    $admin->assignRole('admin');
    echo "Admin user created with email: admin@example.com and password: password\n";
} else {
    echo "Admin user already exists. Making sure it has admin role...\n";
    $admin->syncRoles(['admin']);
    echo "Admin role assigned to user.\n";
}

echo "Done! You can now log in with:\n";
echo "Email: admin@example.com\n";
echo "Password: password\n";
