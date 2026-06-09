<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class SignInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * If the default value is "true", you can delete it since it will implicitly be "true" as well.
     */
    // public function authorize(): bool
    // {
    //     return true;
    // }

    // This method is used to customize the attribute names in the validation error messages. By default, Laravel uses the field names as they are defined in the form, but you can provide more user-friendly names for better readability in error messages.
    #[Override]
    public function attributes()
    {
        return [
            "password" => "contraseña"
        ];
    }

    // To overwrite error messages
    public function messages()
    {
        return [
            "email.exists" => "No encontramos una cuenta con ese correo electrónico"
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
            "email" => ["required", "email", "exists:users,email"],
            "password" => ["required"]
        ];
    }
}
