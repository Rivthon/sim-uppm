<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGrantPeriodRequest extends FormRequest
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
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'grant_scheme_id' => ['required', 'integer', 'exists:grant_schemes,id'],
            'name' => ['required', 'string', 'max:255'],
            'submission_starts_at' => ['required', 'date'],
            'submission_ends_at' => ['required', 'date', 'after:submission_starts_at'],
            'quota' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'budget' => ['required', 'integer', 'min:0'],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(['draft', 'open', 'closed'])],
        ];
    }

    public function messages(): array
    {
        return [
            'submission_ends_at.after' => 'Batas akhir harus setelah waktu pembukaan pengajuan.',
            'academic_year_id.exists' => 'Tahun akademik yang dipilih tidak tersedia.',
            'grant_scheme_id.exists' => 'Skema hibah yang dipilih tidak tersedia.',
        ];
    }
}
