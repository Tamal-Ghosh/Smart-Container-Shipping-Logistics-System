<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use Illuminate\Support\Facades\DB;

Route::get('/test-db', function () {
    try {
        $users = DB::select('SELECT * FROM USERS');
        $ports = DB::select('SELECT * FROM PORT');
        $shipments = DB::select('SELECT * FROM SHIPMENT');

        echo "<h2>✅ Database Connected!</h2>";

        echo "<h3>USERS (" . count($users) . " rows)</h3><pre>";
        print_r($users);
        echo "</pre>";

        echo "<h3>PORTS (" . count($ports) . " rows)</h3><pre>";
        print_r($ports);
        echo "</pre>";

        echo "<h3>SHIPMENTS (" . count($shipments) . " rows)</h3><pre>";
        print_r($shipments);
        echo "</pre>";

    } catch (\Exception $e) {
        echo "<h2>❌ Error: " . $e->getMessage() . "</h2>";
    }
});
