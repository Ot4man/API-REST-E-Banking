<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:courant,epargne,mineur',
            'guardian_id' => 'required_if:type,mineur|exists:users,id',
            'overdraft_limit' => 'nullable|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0',
        ];
    }
}
