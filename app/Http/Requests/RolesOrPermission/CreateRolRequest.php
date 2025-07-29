<?php

namespace App\Http\Requests\RolesOrPermission;

use Illuminate\Foundation\Http\FormRequest;

class CreateRolRequest extends FormRequest
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
            "name"=> "required|string|max:255|unique:roles",
            "permissions"=> "required|array"
        ];
    }
    public function messages(): array
    {
        return [
            //
            "name.required"=> "El nombre del rol es requerido",
            "name.string"=> "El nombre del rol debe ser un string",
            "name.max"=> "El nombre del rol debe tener como maximo 255 caracteres",
            "name.unique"=> "El nombre del rol ya existe",
            "permissions.required"=> "Los permisos son requeridos",
            "permissions.array"=> "Los permisos deben ser un array"
        ];
    }
}
