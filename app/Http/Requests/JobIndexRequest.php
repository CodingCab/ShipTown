<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('api')->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
