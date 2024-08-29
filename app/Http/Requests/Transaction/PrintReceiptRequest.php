<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class PrintReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['sometimes', 'integer', 'exists:data_collections,id'],
            'printer_id' => ['required', 'integer'],
            'epl' => ['required', 'boolean'],
        ];
    }
}
