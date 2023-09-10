<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Shailendra',
            'last_name' => 'Gohil',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin@123'),
        ]);
    }
}
