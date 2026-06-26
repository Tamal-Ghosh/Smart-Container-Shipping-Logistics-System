<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // 1. Admin Account
            $adminEmail = 'admin@shipping.com';
            $adminData = [
                'username'      => 'admin',
                'email'         => $adminEmail,
                'password_hash' => bcrypt('password123'),
                'role'          => 'ADMIN',
                'is_active'     => 'Y',
            ];

            if (User::where('email', $adminEmail)->exists()) {
                DB::table('USERS')->where('email', $adminEmail)->update($adminData);
                $this->command->info("Updated existing Admin account with Bcrypt hash.");
            } else {
                DB::table('USERS')->insert($adminData);
                $this->command->info("Seeded Admin account: {$adminEmail}");
            }

            // 2. Operator Account
            $operatorEmail = 'operator@shipping.com';
            $operatorData = [
                'username'      => 'operator',
                'email'         => $operatorEmail,
                'password_hash' => bcrypt('password123'),
                'role'          => 'OPERATOR',
                'is_active'     => 'Y',
            ];

            if (User::where('email', $operatorEmail)->exists()) {
                DB::table('USERS')->where('email', $operatorEmail)->update($operatorData);
                $this->command->info("Updated existing Operator account with Bcrypt hash.");
            } else {
                DB::table('USERS')->insert($operatorData);
                $this->command->info("Seeded Operator account: {$operatorEmail}");
            }

            // 3. Customer Account
            $customerEmail = 'customer@shipping.com';
            $customerData = [
                'username'      => 'customer',
                'email'         => $customerEmail,
                'password_hash' => bcrypt('password123'),
                'role'          => 'CUSTOMER',
                'is_active'     => 'Y',
            ];

            if (User::where('email', $customerEmail)->exists()) {
                DB::table('USERS')->where('email', $customerEmail)->update($customerData);
                $this->command->info("Updated existing Customer account with Bcrypt hash.");
            } else {
                DB::table('USERS')->insert($customerData);
                $this->command->info("Seeded Customer account: {$customerEmail}");
            }

            // Make sure the CUSTOMER profile table row exists for customer@shipping.com
            $user = User::where('email', $customerEmail)->first();
            if ($user) {
                if (!Customer::where('user_id', $user->user_id)->exists()) {
                    Customer::create([
                        'user_id'        => $user->user_id,
                        'company_name'   => 'Global Logistics Corp',
                        'contact_person' => 'John Doe',
                        'phone'          => '+15550199',
                        'email'          => $customerEmail,
                        'address'        => '123 Shipping Lane, Port City',
                        'country'        => 'United States',
                    ]);
                    $this->command->info("Created Customer profile for customer@shipping.com");
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Seeding failed: " . $e->getMessage());
        }
    }
}
