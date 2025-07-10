<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:setup-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup all system roles according to Constants\Roles and ensure at least one admin user exists';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up system roles...');

        // Run the roles migration first if it hasn't been run
        $this->call('migrate', ['--path' => 'database/migrations/2025_07_10_000001_add_system_roles.php']);

        // Run the necessary seeders
        $this->call('db:seed', ['--class' => 'RoleSeeder']);
        $this->call('db:seed', ['--class' => 'EnhancedPermissionsSeeder']);
        $this->call('db:seed', ['--class' => 'FinancialSystemSeeder']);

        $this->info('Role setup complete!');
        $this->info('Available demo users:');
        $this->info('Admin: admin@financial.system / password');
        $this->info('Supervisor: supervisor@financial.system / password');
        $this->info('Auditor: auditor@financial.system / password');
        $this->info('Branch Manager: manager@financial.system / password');
        $this->info('Agent: agent@financial.system / password');
        $this->info('Trainee: trainee@financial.system / password');

        return Command::SUCCESS;
    }
}
