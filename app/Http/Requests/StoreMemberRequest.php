<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'  => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:members,email'],
            'phone' => ['nullable','string','max:50'],

            // referral
            'referral_code_input' => ['nullable','string','exists:members,referral_code'],

            // addresses (at least one)
            'addresses.*.address_type_id' => ['required','exists:address_types,id'],
            'addresses.*.line1' => ['required','string','max:255'],
            'addresses.*.city'  => ['required','string','max:100'],
            'addresses.*.state' => ['nullable','string','max:100'],
            'addresses.*.postal_code' => ['nullable','string','max:20'],
            'addresses.*.country' => ['nullable','string','max:2'],

            // files
            'profile_image'      => ['nullable','file','mimes:jpg,jpeg,png','max:2048'],
            'proof_of_address'   => ['nullable','file','mimes:jpg,jpeg,png','max:4096'],
        ];
    }
}
