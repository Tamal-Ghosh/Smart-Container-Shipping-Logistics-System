<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username'       => ['required', 'string', 'max:50', Rule::unique('USERS', 'username')],
            'email'          => ['required', 'email', 'max:100', Rule::unique('USERS', 'email')],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'role'           => ['required', Rule::in(['ADMIN', 'CUSTOMER'])],
            'phone'          => ['required_if:role,CUSTOMER', 'nullable', 'string', 'max:20'],
            'company_name'   => ['required_if:role,CUSTOMER', 'nullable', 'string', 'max:150'],
            'contact_person' => ['required_if:role,CUSTOMER', 'nullable', 'string', 'max:100'],
            'address'        => ['required_if:role,CUSTOMER', 'nullable', 'string', 'max:300'],
            'country'        => ['required_if:role,CUSTOMER', 'nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required'       => 'Please enter a username.',
            'username.max'            => 'Username must not exceed 50 characters.',
            'username.unique'         => 'This username is already taken.',
            'email.required'          => 'Please enter your email address.',
            'email.email'             => 'Please enter a valid email address.',
            'email.max'              => 'Email must not exceed 100 characters.',
            'email.unique'            => 'This email is already registered.',
            'password.required'       => 'Please enter a password.',
            'password.min'            => 'Password must be at least 8 characters.',
            'password.confirmed'      => 'Password confirmation does not match.',
            'role.required'           => 'Please select a role.',
            'role.in'                 => 'Invalid role selected. Only ADMIN and CUSTOMER are allowed.',
            'phone.required_if'       => 'Phone number is required for customers.',
            'phone.max'              => 'Phone number must not exceed 20 characters.',
            'company_name.required_if' => 'Company name is required for customers.',
            'company_name.max'        => 'Company name must not exceed 150 characters.',
            'contact_person.required_if' => 'Contact person is required for customers.',
            'contact_person.max'      => 'Contact person must not exceed 100 characters.',
            'address.required_if'     => 'Address is required for customers.',
            'address.max'            => 'Address must not exceed 300 characters.',
            'country.required_if'     => 'Country is required for customers.',
            'country.max'            => 'Country must not exceed 100 characters.',
        ];
    }
}
