<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['name' => 'Super Admin', 'short_name' => 'superadmin'],
            ['name' => 'Administrator', 'short_name' => 'admin'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
