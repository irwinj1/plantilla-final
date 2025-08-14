<?php

namespace App\Http\Requests\RolesOrPermission;

use Illuminate\Foundation\Http\FormRequest;

class RevocarRolUsuarioRequest extends FormRequest
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
            'rol' => 'required|string|exists:roles,name',
        ];
    }
    public function messages(): array
    {
        return [
            'rol.required' => 'El campo rol es requerido',
            'rol.exists' => 'El rol no existe',
        ];
    }
}
