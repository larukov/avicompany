<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@skyair.ru'],
            [
                'name' => 'Администратор',
                'lastname' => 'SkyAir',
                'tel' => '+79990000001',
                'email' => 'admin@skyair.ru',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );
    }
}