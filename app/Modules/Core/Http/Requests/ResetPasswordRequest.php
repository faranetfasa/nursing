<?php

namespace App\Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min((int) config('core.auth.password_min_length', 8))],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'ایمیل',
            'password' => 'رمز عبور',
        ];
    }
}
