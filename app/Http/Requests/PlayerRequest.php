<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PlayerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'team_id' => ['nullable', 'integer', 'exists:teams,id'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:-18 years'],
            'gender' => ['required', 'string', 'in:male,female'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_of_birth.before_or_equal' => 'The player must be at least 18 years old.',
        ];
    }
}
