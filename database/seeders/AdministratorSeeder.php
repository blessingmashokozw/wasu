<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdministratorSeeder extends Seeder
{
    public function run(): void
    {
        // Super Administrator
        $superAdmin = User::factory()->superAdministrator()->create([
            'full_name' => 'Super Admin',
            'email' => 'superadmin@ims.test',
            'phone' => '+263711111111',
            'password' => bcrypt('password'),
        ]);

        // Standard Administrator
        $standardAdmin = User::factory()->administrator()->create([
            'full_name' => 'Standard Admin',
            'email' => 'admin@ims.test',
            'phone' => '+263722222222',
            'password' => bcrypt('password'),
        ]);

        $this->command->info('=== Administrator Credentials ===');
        $this->command->info('Super Admin Email: superadmin@ims.test | Password: password');
        $this->command->info('Standard Admin Email: admin@ims.test | Password: password');
    }
}
