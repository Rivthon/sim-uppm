<?php

namespace App\Http\Requests;

use App\Models\FundingContract;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProgressReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manageExecution', $this->route('contract')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reporting_period' => ['required', 'date', Rule::unique('progress_reports')->where('funding_contract_id', $this->route('contract')?->id)],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'summary' => ['required', 'string', 'min:50', 'max:10000'],
            'achievements' => ['required', 'string', 'min:20', 'max:10000'],
            'obstacles' => ['nullable', 'string', 'max:5000'],
            'budget_realized' => ['required', 'integer', 'min:0'],
            'output_progress' => ['required', 'string', 'min:20', 'max:5000'],
            'document' => ['required', 'file', 'mimes:pdf,doc,docx', 'extensions:pdf,doc,docx', 'max:10240'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $contract = $this->route('contract');
            if ($contract instanceof FundingContract && $this->integer('budget_realized') > $contract->contract_value) {
                $validator->errors()->add('budget_realized', 'Realisasi anggaran tidak boleh melebihi nilai kontrak.');
            }
        }];
    }
}
