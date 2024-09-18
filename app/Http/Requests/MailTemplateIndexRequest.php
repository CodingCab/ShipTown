<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MailTemplateIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('api')->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
