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

            $customer = Customer::where('email', $customerEmail)->first();
            $admin = User::where('email', $adminEmail)->first();

            $ports = [
                ['port_id' => 1, 'port_name' => 'Chittagong Port', 'port_code' => 'CGP', 'location' => 'Chittagong', 'country' => 'Bangladesh', 'status' => 'ACTIVE'],
                ['port_id' => 2, 'port_name' => 'Port of Felixstowe', 'port_code' => 'FXT', 'location' => 'Suffolk', 'country' => 'United Kingdom', 'status' => 'ACTIVE'],
                ['port_id' => 3, 'port_name' => 'Port of Singapore', 'port_code' => 'SGP', 'location' => 'Singapore', 'country' => 'Singapore', 'status' => 'ACTIVE']
            ];

            foreach ($ports as $port) {
                if (!DB::table('PORT')->where('port_id', $port['port_id'])->exists()) {
                    DB::table('PORT')->insert($port);
                }
            }

            $vehicles = [
                ['vehicle_id' => 1, 'vehicle_number' => 'TRK-9900', 'type' => 'TRUCK', 'capacity_kg' => 15000, 'status' => 'AVAILABLE'],
                ['vehicle_id' => 2, 'vehicle_number' => 'VSL-ALPHA', 'type' => 'VESSEL', 'capacity_kg' => 500000, 'status' => 'AVAILABLE'],
                ['vehicle_id' => 3, 'vehicle_number' => 'VSL-BETA', 'type' => 'VESSEL', 'capacity_kg' => 750000, 'status' => 'IN_TRANSIT']
            ];

            foreach ($vehicles as $vehicle) {
                if (!DB::table('VEHICLE')->where('vehicle_id', $vehicle['vehicle_id'])->exists()) {
                    DB::table('VEHICLE')->insert($vehicle);
                }
            }

            $containers = [
                ['container_id' => 1, 'container_number' => 'CON-5501', 'container_type' => 'STANDARD', 'status' => 'AVAILABLE'],
                ['container_id' => 2, 'container_number' => 'CON-7702', 'container_type' => 'REEFER', 'status' => 'AVAILABLE'],
                ['container_id' => 3, 'container_number' => 'CON-8803', 'container_type' => 'STANDARD', 'status' => 'ASSIGNED']
            ];

            foreach ($containers as $container) {
                if (!DB::table('CONTAINER')->where('container_id', $container['container_id'])->exists()) {
                    DB::table('CONTAINER')->insert($container);
                }
            }

            if ($customer && $admin) {
                $shipments = [
                    [
                        'shipment_id' => 1,
                        'customer_id' => $customer->customer_id,
                        'source_port_id' => 1,
                        'destination_port_id' => 2,
                        'vehicle_id' => 1,
                        'created_by' => $admin->user_id,
                        'shipment_ref' => 'SHP-2026-00001',
                        'status' => 'BOOKED',
                        'shipment_date' => now()->subDays(5),
                        'expected_delivery_date' => now()->addDays(15),
                        'actual_delivery_date' => null,
                        'notes' => 'Handle with care'
                    ],
                    [
                        'shipment_id' => 2,
                        'customer_id' => $customer->customer_id,
                        'source_port_id' => 3,
                        'destination_port_id' => 1,
                        'vehicle_id' => 3,
                        'created_by' => $admin->user_id,
                        'shipment_ref' => 'SHP-2026-00002',
                        'status' => 'IN_TRANSIT',
                        'shipment_date' => now()->subDays(2),
                        'expected_delivery_date' => now()->addDays(10),
                        'actual_delivery_date' => null,
                        'notes' => 'Priority shipping'
                    ]
                ];

                foreach ($shipments as $shipment) {
                    if (!DB::table('SHIPMENT')->where('shipment_id', $shipment['shipment_id'])->exists()) {
                        DB::table('SHIPMENT')->insert($shipment);
                    }
                }

                $payments = [
                    [
                        'payment_id' => 1,
                        'shipment_id' => 1,
                        'customer_id' => $customer->customer_id,
                        'amount' => 4500.00,
                        'payment_method' => 'BANK_TRANSFER',
                        'payment_status' => 'COMPLETED',
                        'payment_date' => now()->subDays(4),
                        'transaction_ref' => 'TXN-99881122',
                        'due_date' => now()->addDays(5)
                    ],
                    [
                        'payment_id' => 2,
                        'shipment_id' => 2,
                        'customer_id' => $customer->customer_id,
                        'amount' => 12500.00,
                        'payment_method' => 'CREDIT_CARD',
                        'payment_status' => 'PENDING',
                        'payment_date' => null,
                        'transaction_ref' => null,
                        'due_date' => now()->addDays(8)
                    ]
                ];

                foreach ($payments as $payment) {
                    if (!DB::table('PAYMENT')->where('payment_id', $payment['payment_id'])->exists()) {
                        DB::table('PAYMENT')->insert($payment);
                    }
                }

                $logs = [
                    [
                        'tracking_id' => 1,
                        'shipment_id' => 2,
                        'port_id' => 3,
                        'event_type' => 'DISPATCHED',
                        'location' => 'Port of Singapore',
                        'status' => 'IN_TRANSIT',
                        'remarks' => 'Vessel departed terminal loading area',
                        'updated_at' => now()
                    ]
                ];

                foreach ($logs as $log) {
                    if (!DB::table('TRACKING_LOG')->where('tracking_id', $log['tracking_id'])->exists()) {
                        DB::table('TRACKING_LOG')->insert($log);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Seeding failed: " . $e->getMessage());
        }
    }
}
