<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveKpiTargetRequest extends FormRequest
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
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'metric' => ['required', Rule::in(['funded_activities', 'verified_outputs', 'verified_final_reports', 'paid_funding'])],
            'target_value' => ['required', 'integer', 'min:1'],
            'unit' => ['required', Rule::in(['kegiatan', 'luaran', 'laporan', 'rupiah'])],
        ];
    }
}
