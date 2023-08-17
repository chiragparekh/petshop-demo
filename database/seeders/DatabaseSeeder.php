<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->setupBuckhillAdminUser();
        $this->setupOrderStatuses();
    }

    private function setupBuckhillAdminUser(): void
    {
        User::firstOrCreate([
            'email' => 'admin@buckhill.co.uk'
        ], [
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'is_admin' => 1,
            'email' => 'admin@buckhill.co.uk',
            'password' => Hash::make('admin'),
            'address' => 'address',
            'phone_number' => '1234567890',
        ]);
    }

    private function setupOrderStatuses()
    {
        collect([
            'Open', 'Pending payment', 'Paid', 'Shipped', 'Cancelled'
        ])->each(function($status) {
           OrderStatus::firstOrCreate([
               'title' => $status,
           ], [
               'title' => $status
           ]);
        });
    }
}
