<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class LeadRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'nullable|email',  // You need to add validation rules here, otherwise it's ignored
            'contact_no' => 'nullable|regex:/^\+?[0-9]{1,15}$/|max:15|min:10',
            'lead_title' => 'nullable|max:255',
            'title' => 'nullable|string',
            'budget' => 'nullable|numeric',
            'company' => 'nullable|string',
            'audience_type' => 'nullable|string',
            'customer_type' => 'nullable|string',
            'status' => 'nullable|string',
            'last_follow_up' => 'nullable|date',
            'next_follow_up' => 'nullable|date',
            'number_of_follow_up' => 'nullable|integer',
            'web_url' => 'nullable|url',
            'assignedto' => 'nullable',
            'notes' => 'nullable',
            'leadstage' => 'nullable|string',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
            'is_active' => 'nullable|integer',
            'is_deleted' => 'nullable|integer',
            'source' => 'nullable|string',
            'ip' => 'nullable|ip',
            'number_of_attempt' => 'nullable|integer',
        ];
    }

    protected function failedValidation(Validator $validator)
{
    throw new HttpResponseException(
        response()->json([
            'status' => 422,
            'errors' => $validator->errors(),
        ], 422)
    );
}
}
