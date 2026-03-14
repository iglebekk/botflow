<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBotTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('issueToken', $this->route('bot'));
    }

    public function rules(): array
    {
        return [
            'token_name' => ['required', 'string', 'max:255'],
        ];
    }
}
