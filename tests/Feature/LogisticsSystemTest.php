<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Container;
use App\Models\Port;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LogisticsSystemTest extends TestCase
{
    private function getAdminUser()
    {
        return User::where('email', 'admin@shipping.com')->first();
    }

    private function getOperatorUser()
    {
        return User::where('email', 'operator@shipping.com')->first();
    }

    public function test_admin_can_manage_vehicles()
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        // 1. Create a vehicle
        $vehicleNumber = 'TEST-VEH-' . rand(1000, 9999);
        $response = $this->post('/vehicles', [
            'vehicle_number' => $vehicleNumber,
            'type' => 'VESSEL',
            'capacity_kg' => 900000,
        ]);

        $response->assertRedirect('/vehicles');
        
        $vehicle = Vehicle::where('vehicle_number', $vehicleNumber)->first();
        $this->assertNotNull($vehicle);
        $this->assertEquals('AVAILABLE', $vehicle->status);

        // 2. Update status (invalid transition)
        // Transition from AVAILABLE to IN_USE is valid, but let's test invalid transition
        // AVAILABLE -> RETIRED is valid.
        // Let's test a valid status transition
        $response = $this->post("/vehicles/{$vehicle->vehicle_id}/status", [
            'status' => 'MAINTENANCE',
        ]);
        $response->assertRedirect();
        
        $vehicle->refresh();
        $this->assertEquals('MAINTENANCE', $vehicle->status);

        // 3. Delete vehicle
        $response = $this->delete("/vehicles/{$vehicle->vehicle_id}");
        $response->assertRedirect('/vehicles');
        $this->assertNull(Vehicle::find($vehicle->vehicle_id));
    }

    public function test_admin_can_manage_containers()
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        // 1. Create a container
        $containerNumber = 'TEST-CON-' . rand(1000, 9999);
        $response = $this->post('/containers', [
            'container_number' => $containerNumber,
            'container_type' => 'NORMAL',
        ]);

        $response->assertRedirect('/containers');

        $container = Container::where('container_number', $containerNumber)->first();
        $this->assertNotNull($container);
        $this->assertEquals('AVAILABLE', $container->status);

        // 2. Update status
        $response = $this->post("/containers/{$container->container_id}/status", [
            'status' => 'MAINTENANCE',
        ]);
        $response->assertRedirect();

        $container->refresh();
        $this->assertEquals('MAINTENANCE', $container->status);

        // 3. Delete container
        $response = $this->delete("/containers/{$container->container_id}");
        $response->assertRedirect('/containers');
        $this->assertNull(Container::find($container->container_id));
    }

    public function test_operator_can_book_shipment_and_log_tracking_event()
    {
        $operator = $this->getOperatorUser();
        $this->actingAs($operator);

        // Create available vehicle and container first (using DB or direct Eloquent as Admin)
        $vehicleNumber = 'OP-VEH-' . rand(1000, 9999);
        Vehicle::create([
            'vehicle_number' => $vehicleNumber,
            'type' => 'VESSEL',
            'capacity_kg' => 20000,
            'status' => 'AVAILABLE',
        ]);
        $vehicle = Vehicle::where('vehicle_number', $vehicleNumber)->first();

        $containerNumber = 'OP-CON-' . rand(1000, 9999);
        Container::create([
            'container_number' => $containerNumber,
            'container_type' => 'FREEZE',
            'status' => 'AVAILABLE',
        ]);
        $container = Container::where('container_number', $containerNumber)->first();

        $customer = Customer::first();
        if (!$customer) {
            $user = User::create([
                'username' => 'test_cust_' . rand(1000, 9999),
                'email' => 'test_cust_' . rand(1000, 9999) . '@example.com',
                'password_hash' => bcrypt('password123'),
                'role' => 'CUSTOMER',
                'is_active' => 'Y',
            ]);
            $customer = Customer::create([
                'user_id' => $user->user_id,
                'company_name' => 'Test Company',
                'contact_person' => 'Test Contact',
                'phone' => '12345678',
                'email' => $user->email,
                'address' => 'Test Address',
                'country' => 'Test Country',
            ]);
        }

        $ports = Port::where('status', 'ACTIVE')->take(2)->get();
        if ($ports->count() < 2) {
            Port::create([
                'port_name' => 'Test Port A',
                'port_code' => 'TPA',
                'location' => 'Location A',
                'country' => 'Country A',
                'status' => 'ACTIVE',
            ]);
            Port::create([
                'port_name' => 'Test Port B',
                'port_code' => 'TPB',
                'location' => 'Location B',
                'country' => 'Country B',
                'status' => 'ACTIVE',
            ]);
            $ports = Port::where('status', 'ACTIVE')->take(2)->get();
        }

        // 1. Book shipment
        $response = $this->post('/operator/shipments', [
            'customer_id' => $customer->customer_id,
            'source_port_id' => $ports[0]->port_id,
            'destination_port_id' => $ports[1]->port_id,
            'vehicle_id' => $vehicle->vehicle_id,
            'expected_delivery_date' => date('Y-m-d', strtotime('+5 days')),
            'containers' => [$container->container_id],
            'seal_numbers' => [
                $container->container_id => 'SEAL-TEST-99'
            ],
            'loaded_weights' => [
                $container->container_id => 14000
            ],
            'notes' => 'Integration test booking',
        ]);

        $response->assertRedirect('/operator/dashboard');

        // Check if shipment was created and statuses updated
        $shipment = Shipment::where('vehicle_id', $vehicle->vehicle_id)->first();
        $this->assertNotNull($shipment);
        $this->assertEquals('BOOKED', $shipment->status);
        $this->assertStringStartsWith('SHP-', $shipment->shipment_ref);

        $vehicle->refresh();
        $container->refresh();
        $this->assertEquals('IN_USE', $vehicle->status);
        $this->assertEquals('IN_USE', $container->status);

        // 2. Log a tracking event (which calls PROCEDURE add_tracking_event)
        $response = $this->post('/tracking/log', [
            'shipment_id' => $shipment->shipment_id,
            'event_type' => 'IN_TRANSIT',
            'location' => 'Open sea near transit bay',
            'port_id' => $ports[0]->port_id,
            'status' => 'ON_TIME',
            'remarks' => 'Smooth sailing',
        ]);

        $response->assertRedirect();
        
        $shipment->refresh();
        $this->assertEquals('IN_TRANSIT', $shipment->status);

        // 3. Log DELIVERED event -> should auto-update shipment status to DELIVERED and release vehicle/containers
        $response = $this->post('/tracking/log', [
            'shipment_id' => $shipment->shipment_id,
            'event_type' => 'DELIVERED',
            'location' => 'Arrival Dock B',
            'port_id' => $ports[1]->port_id,
            'status' => 'EARLY',
            'remarks' => 'Completed shipment delivery',
        ]);

        $response->assertRedirect();

        $shipment->refresh();
        $vehicle->refresh();
        $container->refresh();

        $this->assertEquals('DELIVERED', $shipment->status);
        $this->assertEquals('AVAILABLE', $vehicle->status);
        $this->assertEquals('AVAILABLE', $container->status);
    }

    public function test_admin_can_manage_users_and_deactivate_them()
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        // 1. Create operator
        $username = 'test_op_' . rand(100, 999);
        $email = $username . '@shipping.com';
        $response = $this->post('/admin/users', [
            'username' => $username,
            'email'    => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertRedirect('/admin/users');

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertEquals('OPERATOR', $user->role);
        $this->assertEquals('Y', $user->is_active);

        // 2. Deactivate operator (calls PL/SQL procedure deactivate_user)
        $response = $this->post("/admin/users/{$user->user_id}/toggle");
        $response->assertRedirect();

        $user->refresh();
        $this->assertEquals('N', $user->is_active);

        // 3. Delete operator user
        $response = $this->delete("/admin/users/{$user->user_id}");
        $response->assertRedirect('/admin/users');
        $this->assertNull(User::find($user->user_id));
    }

    public function test_admin_can_manage_payments_and_calculate_revenue()
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $shipment = $this->getOrCreateShipment();
        $customer = Customer::find($shipment->customer_id);

        $amount = (float) rand(100000, 999999) / 100.0;

        // 1. Create a payment (calls PL/SQL procedure create_payment)
        $response = $this->post('/payments', [
            'shipment_id'    => $shipment->shipment_id,
            'amount'         => $amount,
            'payment_method' => 'BANK_TRANSFER',
            'due_date'       => date('Y-m-d', strtotime('+10 days')),
        ]);
        $response->assertRedirect('/payments');

        $payment = Payment::where('shipment_id', $shipment->shipment_id)
            ->where('amount', $amount)
            ->first();
        $this->assertNotNull($payment);
        $this->assertEquals('PENDING', $payment->payment_status);

        // 2. Mark as Paid (updates to COMPLETED)
        $response = $this->post("/payments/{$payment->payment_id}/status", [
            'action' => 'pay',
        ]);
        $response->assertRedirect();

        $payment->refresh();
        $this->assertEquals('COMPLETED', $payment->payment_status);
        $this->assertNotNull($payment->transaction_ref);

        // 3. Test revenue calculator (calls PL/SQL function get_revenue)
        $response = $this->get('/payments?' . http_build_query([
            'rev_start' => date('Y-m-d', strtotime('-1 day')),
            'rev_end'   => date('Y-m-d', strtotime('+1 day')),
        ]));
        $response->assertStatus(200);
        $response->assertSee('৳');
    }

    public function test_global_search_redirects_or_returns_results()
    {
        $operator = $this->getOperatorUser();
        $this->actingAs($operator);

        $shipment = $this->getOrCreateShipment();

        // Search for exact shipment ref should redirect directly
        $response = $this->get("/search?q={$shipment->shipment_ref}");
        $response->assertRedirect("/operator/shipments/{$shipment->shipment_id}");

        // Create a second shipment to prevent exact match redirect on generic search
        $admin = $this->getAdminUser();
        Shipment::create([
            'customer_id' => $shipment->customer_id,
            'source_port_id' => $shipment->source_port_id,
            'destination_port_id' => $shipment->destination_port_id,
            'vehicle_id' => $shipment->vehicle_id,
            'created_by' => $admin->user_id,
            'shipment_ref' => 'SHP-DUP-' . rand(10000, 99999),
            'status' => 'BOOKED',
            'shipment_date' => now(),
            'expected_delivery_date' => now()->addDays(5),
        ]);

        // Search for generic text should list results
        $response = $this->get("/search?q=SHP");
        $response->assertStatus(200);
        $response->assertSee('Matching Shipments');
    }

    public function test_operator_can_access_tracking_log_creation_page()
    {
        $operator = $this->getOperatorUser();
        $this->actingAs($operator);

        $response = $this->get('/operator/tracking/log');
        $response->assertStatus(200);
        $response->assertSee('Log Tracking Event');
        $response->assertSee('Select Cargo Shipment');
    }

    private function getOrCreateShipment()
    {
        $shipment = Shipment::first();
        if ($shipment) {
            return $shipment;
        }

        // Create ports
        $portA = Port::create([
            'port_name' => 'Test Port A',
            'port_code' => 'TPA_' . rand(100, 999),
            'location' => 'Location A',
            'country' => 'Country A',
            'status' => 'ACTIVE',
        ]);
        $portB = Port::create([
            'port_name' => 'Test Port B',
            'port_code' => 'TPB_' . rand(100, 999),
            'location' => 'Location B',
            'country' => 'Country B',
            'status' => 'ACTIVE',
        ]);

        // Create vehicle
        $vehicle = Vehicle::create([
            'vehicle_number' => 'VSL-TEST-' . rand(1000, 9999),
            'type' => 'VESSEL',
            'capacity_kg' => 500000,
            'status' => 'AVAILABLE',
        ]);

        // Create user & customer
        $user = User::create([
            'username' => 'test_cust_' . rand(1000, 9999),
            'email' => 'test_cust_' . rand(1000, 9999) . '@example.com',
            'password_hash' => bcrypt('password123'),
            'role' => 'CUSTOMER',
            'is_active' => 'Y',
        ]);
        $customer = Customer::create([
            'user_id' => $user->user_id,
            'company_name' => 'Test Company',
            'contact_person' => 'Test Contact',
            'phone' => '12345678',
            'email' => $user->email,
            'address' => 'Test Address',
            'country' => 'Test Country',
        ]);

        // Create admin user (needed as created_by)
        $admin = $this->getAdminUser();

        $shipment = Shipment::create([
            'customer_id' => $customer->customer_id,
            'source_port_id' => $portA->port_id,
            'destination_port_id' => $portB->port_id,
            'vehicle_id' => $vehicle->vehicle_id,
            'created_by' => $admin->user_id,
            'shipment_ref' => 'SHP-' . rand(10000, 99999),
            'status' => 'BOOKED',
            'shipment_date' => now(),
            'expected_delivery_date' => now()->addDays(5),
        ]);

        return $shipment;
    }
}
