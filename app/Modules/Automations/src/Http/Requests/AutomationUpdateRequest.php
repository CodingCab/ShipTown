<?php

namespace App\Modules\Automations\src\Http\Requests;

use App\Modules\Automations\src\Services\AutomationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AutomationUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->guard('api')->user()->hasRole('admin');
    }

    /**
     * Get the condition rules that apply to the request.
     */
    public function rules(): array
    {
        $available_conditions_classes = AutomationService::availableConditions()->pluck('class');
        $available_action_classes = AutomationService::availableActions()->pluck('class');

        return [
            'name' => 'required|min:3|max:200',
            'description' => 'nullable|string',
            'enabled' => 'required|boolean',
            'priority' => 'required|numeric',
            'conditions' => 'array',
            'conditions.*.condition_class' => ['nullable', Rule::in($available_conditions_classes)],
            'conditions.*.condition_value' => 'nullable|string',
            'actions' => 'array',
            'actions.*.action_class' => ['nullable', Rule::in($available_action_classes)],
            'actions.*.action_value' => 'nullable|string',
        ];
    }
}
