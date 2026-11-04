<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('assignment')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'relevance_score' => ['nullable', 'integer', 'min:0', 'max:20'],
            'methodology_score' => ['nullable', 'integer', 'min:0', 'max:20'],
            'feasibility_score' => ['nullable', 'integer', 'min:0', 'max:20'],
            'budget_score' => ['nullable', 'integer', 'min:0', 'max:20'],
            'output_score' => ['nullable', 'integer', 'min:0', 'max:20'],
            'strengths' => ['nullable', 'string', 'max:5000'],
            'weaknesses' => ['nullable', 'string', 'max:5000'],
            'comments' => ['nullable', 'string', 'max:5000'],
            'recommendation' => ['nullable', Rule::in(['funded', 'revision', 'rejected'])],
            'has_conflict' => ['nullable', 'boolean'],
            'conflict_notes' => ['nullable', 'required_if:has_conflict,1', 'string', 'max:2000'],
        ];
    }
}
