<?php

namespace App\Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'login' => 'نام کاربری یا موبایل',
            'password' => 'رمز عبور',
        ];
    }

    public function remember(): bool
    {
        return (bool) config('core.auth.remember_me', true) && $this->boolean('remember');
    }
}
