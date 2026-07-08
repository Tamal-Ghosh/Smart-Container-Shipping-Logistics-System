<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Customer;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
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
            } else {
                DB::table('USERS')->insert($adminData);
            }

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
            } else {
                DB::table('USERS')->insert($operatorData);
            }

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
            } else {
                DB::table('USERS')->insert($customerData);
            }

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
                }
            }

            if (true) { // Always seed ports, vehicles, containers, and default records
                $customer = Customer::where('email', $customerEmail)->first();
                $admin = User::where('email', $adminEmail)->first();

                $ports = [
                    ['port_id' => 1, 'port_name' => 'Chittagong Port',      'port_code' => 'CGP', 'location' => 'Chittagong', 'country' => 'Bangladesh',   'status' => 'ACTIVE'],
                    ['port_id' => 2, 'port_name' => 'Port of Felixstowe',   'port_code' => 'FXT', 'location' => 'Suffolk',    'country' => 'United Kingdom','status' => 'ACTIVE'],
                    ['port_id' => 3, 'port_name' => 'Port of Singapore',    'port_code' => 'SGP', 'location' => 'Singapore',  'country' => 'Singapore',    'status' => 'ACTIVE'],
                    ['port_id' => 4, 'port_name' => 'Port of Shanghai',     'port_code' => 'SHA', 'location' => 'Shanghai',   'country' => 'China',        'status' => 'ACTIVE'],
                    ['port_id' => 5, 'port_name' => 'Port of Rotterdam',    'port_code' => 'RTM', 'location' => 'Rotterdam',  'country' => 'Netherlands',  'status' => 'ACTIVE'],
                ];

                foreach ($ports as $port) {
                    if (!DB::table('PORT')->where('port_id', $port['port_id'])->exists()) {
                        DB::table('PORT')->insert($port);
                    }
                }

                $vehicles = [
                    ['vehicle_id' => 1, 'vehicle_number' => 'VSL-ALPHA', 'type' => 'VESSEL', 'capacity_kg' => 500000, 'status' => 'AVAILABLE'],
                    ['vehicle_id' => 2, 'vehicle_number' => 'VSL-BETA',  'type' => 'VESSEL', 'capacity_kg' => 750000, 'status' => 'AVAILABLE'],
                    ['vehicle_id' => 3, 'vehicle_number' => 'VSL-GAMMA', 'type' => 'VESSEL', 'capacity_kg' => 350000, 'status' => 'AVAILABLE'],
                    ['vehicle_id' => 4, 'vehicle_number' => 'VSL-DELTA', 'type' => 'VESSEL', 'capacity_kg' => 900000, 'status' => 'AVAILABLE'],
                    ['vehicle_id' => 5, 'vehicle_number' => 'VSL-OMEGA', 'type' => 'VESSEL', 'capacity_kg' => 120000, 'status' => 'AVAILABLE'],
                ];

                foreach ($vehicles as $vehicle) {
                    if (!DB::table('VEHICLE')->where('vehicle_id', $vehicle['vehicle_id'])->exists()) {
                        DB::table('VEHICLE')->insert($vehicle);
                    }
                }

                $containers = [
                    ['container_id' => 1, 'container_number' => 'CON-1001', 'container_type' => 'NORMAL', 'status' => 'AVAILABLE'],
                    ['container_id' => 2, 'container_number' => 'CON-1002', 'container_type' => 'FREEZE', 'status' => 'AVAILABLE'],
                    ['container_id' => 3, 'container_number' => 'CON-1003', 'container_type' => 'NORMAL', 'status' => 'AVAILABLE'],
                    ['container_id' => 4, 'container_number' => 'CON-1004', 'container_type' => 'HAZMAT', 'status' => 'AVAILABLE'],
                    ['container_id' => 5, 'container_number' => 'CON-1005', 'container_type' => 'FREEZE', 'status' => 'AVAILABLE'],
                ];

                foreach ($containers as $container) {
                    if (!DB::table('CONTAINER')->where('container_id', $container['container_id'])->exists()) {
                        DB::table('CONTAINER')->insert($container);
                    }
                }

                // Advance sequences to avoid PK conflicts with seeded IDs
                $maxPortId = DB::table('PORT')->max('port_id') ?? 0;
                for ($i = 0; $i < $maxPortId; $i++) {
                    try { DB::select("SELECT SEQ_PORT.NEXTVAL FROM DUAL"); } catch (\Exception $e) {}
                }

                $maxVehicleId = DB::table('VEHICLE')->max('vehicle_id') ?? 0;
                for ($i = 0; $i < $maxVehicleId; $i++) {
                    try { DB::select("SELECT SEQ_VEHICLE.NEXTVAL FROM DUAL"); } catch (\Exception $e) {}
                }

                $maxContainerId = DB::table('CONTAINER')->max('container_id') ?? 0;
                for ($i = 0; $i < $maxContainerId; $i++) {
                    try { DB::select("SELECT SEQ_CONTAINER.NEXTVAL FROM DUAL"); } catch (\Exception $e) {}
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Seeding failed: " . $e->getMessage());
        }
    }
}
