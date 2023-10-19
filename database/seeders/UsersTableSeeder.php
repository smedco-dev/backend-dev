<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            ['personal_number' => '12345678', 'name' => 'Muhammad Azzam', 'email' => 'azzmnrwebdev@gmail.com', 'password' => Hash::make('smedco123'), 'roles_id' => 1],
            ['personal_number' => '87654321', 'name' => 'Ficri Hanip', 'email' => 'ficrihnp@gmail.com', 'password' => Hash::make('smedco123'), 'roles_id' => 1],
            ['personal_number' => '11111111', 'name' => 'Rafi Maulana', 'email' => 'rafi.maul30@gmail.com', 'password' => Hash::make('smedco123'), 'roles_id' => 2],
            ['personal_number' => '22222222', 'name' => 'Roni Setiawan', 'email' => 'ronisty4@gmail.com', 'password' => Hash::make('smedco123'), 'roles_id' => 2],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
