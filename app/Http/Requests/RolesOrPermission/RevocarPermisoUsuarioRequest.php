<?php

namespace App\Http\Requests\RolesOrPermission;

use Illuminate\Foundation\Http\FormRequest;

class RevocarPermisoUsuarioRequest extends FormRequest
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
            //
            'permisos' => 'required|array',
            'permisos.*' => 'required|string|exists:permissions,name',
        ];
    }
    public function messages(): array
    {
        return [
            'permisos.required' => 'El campo permisos es requerido',
            'permisos.*.required' => 'El campo permisos es requerido',
            'permisos.*.exists' => 'El permiso no existe',
        ];
    }
}
