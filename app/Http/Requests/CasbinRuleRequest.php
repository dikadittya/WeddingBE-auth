<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CasbinRuleRequest extends FormRequest
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
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        
        if ($isUpdate) {
            return [
                'ptype' => ['sometimes', 'string', 'in:p,g'],
                'role' => ['sometimes', 'string'],
                'route' => ['nullable', 'string'],
                'action' => ['nullable', 'string'],
                'v3' => ['nullable', 'string'],
                'v4' => ['nullable', 'string'],
                'v5' => ['nullable', 'string'],
            ];
        }
        
        // Store rules
        return [
            'ptype' => ['required', 'string', 'in:p,g'], // p for policy, g for grouping (role inheritance)
            'role' => ['required', 'string'], // subject (user/role)
            'route' => ['required_if:ptype,p', 'nullable', 'string'], // object/resource (required for policy)
            'action' => ['required_if:ptype,p', 'nullable', 'string'], // action (required for policy)
            'v3' => ['nullable', 'string'],
            'v4' => ['nullable', 'string'],
            'v5' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ptype.required' => 'Policy type is required',
            'ptype.in' => 'Policy type must be either p (policy) or g (grouping)',
            'role.required' => 'Role is required',
            'route.required_if' => 'Route is required for policy rules',
            'action.required_if' => 'Action is required for policy rules',
        ];
    }
}