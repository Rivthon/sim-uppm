<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveGrantSchemeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin_uppm') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:30', Rule::unique('grant_schemes')->ignore($this->route('grant_scheme'))],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['penelitian', 'pkm'])],
            'description' => ['nullable', 'string', 'max:5000'],
            'maximum_funding' => ['required', 'integer', 'min:0'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:60'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
