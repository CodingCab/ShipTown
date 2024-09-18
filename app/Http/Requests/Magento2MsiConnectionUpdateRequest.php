<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Magento2MsiConnectionUpdateRequest extends FormRequest
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
