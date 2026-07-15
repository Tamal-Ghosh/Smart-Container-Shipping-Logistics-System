<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && (Auth::user()->role === 'OPERATOR' || Auth::user()->role === 'CUSTOMER' || Auth::user()->role === 'ADMIN');
    }

    public function rules(): array
    {
        $rules = [
            'source_port_id'         => 'required|integer|exists:PORT,port_id',
            'destination_port_id'    => 'required|integer|exists:PORT,port_id|different:source_port_id',
            'vehicle_id'             => 'required|integer|exists:VEHICLE,vehicle_id',
            'containers'             => 'required|array|min:1',
            'containers.*'           => 'required|integer|exists:CONTAINER,container_id',
            'loaded_weights'         => 'required|array',
            'loaded_weights.*'       => 'nullable|numeric|min:0',
            'notes'                  => 'nullable|string|max:500',
        ];

        if (Auth::user()->role === 'OPERATOR' || Auth::user()->role === 'ADMIN') {
            $rules['customer_id'] = 'required|integer|exists:CUSTOMER,customer_id';
        } else {
            $rules['customer_id'] = 'nullable';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'destination_port_id.different' => 'The destination port must be different from the source port.',
            'containers.required'           => 'You must select at least one container to book a shipment.',
        ];
    }
}
