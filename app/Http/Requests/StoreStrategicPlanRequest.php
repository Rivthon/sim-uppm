<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStrategicPlanRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'scope' => ['required', Rule::in(['institution', 'study_program'])],
            'study_program_id' => ['nullable', 'required_if:scope,study_program', 'integer', 'exists:study_programs,id'],
            'type' => ['required', Rule::in(['penelitian', 'pkm', 'integrated'])],
            'start_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'end_year' => ['required', 'integer', 'gte:start_year', 'max:2100'],
            'vision' => ['required', 'string', 'min:30', 'max:10000'],
            'objectives' => ['required', 'string', 'min:30', 'max:15000'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'extensions:pdf,doc,docx', 'max:15360'],
            'status' => ['required', Rule::in(['draft', 'active', 'archived'])],
        ];
    }
}
