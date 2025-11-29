<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'min:8', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', 'string', Password::min(8)],
            'role' => ['nullable', 'string', 'in:admin,user,guest,super_admin'],
            'member_id' => ['required', 'integer', 'exists:members,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama harus diisi',
            'email.email' => 'Format email tidak valid',
            'username.required' => 'Username harus diisi',
            'username.min' => 'Username minimal 8 karakter',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'role.in' => 'Role harus salah satu dari: admin, user, guest, super_admin',
            'member_id.exists' => 'Member tidak ditemukan',
        ];
    }
}
