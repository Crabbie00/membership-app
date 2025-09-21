<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
{
    public function rules(): array
    {
        $memberId = $this->route('member');

        return [
            'name'  => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('members','email')->ignore($memberId)],
            'phone' => ['nullable','string','max:50'],

            'addresses.*.id' => ['nullable','integer','exists:addresses,id'],
            'addresses.*.address_type_id' => ['required','exists:address_types,id'],
            'addresses.*.line1' => ['required','string','max:255'],
            'addresses.*.city'  => ['required','string','max:100'],
            'addresses.*.state' => ['nullable','string','max:100'],
            'addresses.*.postal_code' => ['nullable','string','max:20'],
            'addresses.*.country' => ['nullable','string','max:2'],

            'profile_image'    => ['nullable','file','mimes:jpg,jpeg,png','max:2048'],
            'proof_of_address' => ['nullable','file','mimes:jpg,jpeg,png,pdf','max:4096'],
        ];
    }
}
