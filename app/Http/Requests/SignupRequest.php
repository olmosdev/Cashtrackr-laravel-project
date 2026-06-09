<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Override;

class SignupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    #[Override]
    public function messages(): array
    {
        return [
            "name.required" => "El Nombre es obligatorio",
            "email.required" => "El E-mail es obligatorio",
            "email.email" => "E-mail no valido",
            "email.unique" => "Este correo ya ha sido registrado",
            "password.required" => "La Contraseña es obligatoria",
            "password.confirmed" => "Las Contraseñas no coinciden",
            "password.min" => "La Contraseña debe tener al menos :min caracteres",
            "password.letters" => "La Contraseña debe tener al menos 1 letra",
            "password.mixed" => "La Contraseña debe tener al menos 1 letra mayuscula y letra minuscula",
            "password.symbols" => "La Contraseña debe tener al menos 1 caracter especial (^@_-.*)",
            "password.numbers" => "La Contraseña debe tener al menos 1 numero",
            "password.uncompromised" => "La Contraseña ha aparecido en filtraciones de datos. Elige una mas segura",
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => ["required", "string"],
            "email" => ["required", "email", "unique:users,email"],
            // "password" => ["required", "confirmed", "min:8"]
            "password" => ["required", "confirmed", 
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->symbols()
                    ->numbers()
                    ->uncompromised()]

        ];
    }
}
