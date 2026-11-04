<?php

namespace App\Http\Requests;

use App\Models\FundingContract;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreFinalReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $contract = $this->route('contract');

        return $contract instanceof FundingContract
            && $this->user()?->can('manageExecution', $contract)
            && ! $contract->finalReport()->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'summary' => ['required', 'string', 'min:100', 'max:15000'],
            'conclusions' => ['required', 'string', 'min:50', 'max:10000'],
            'recommendations' => ['nullable', 'string', 'max:5000'],
            'budget_realized' => ['required', 'integer', 'min:0'],
            'document' => ['required', 'file', 'mimes:pdf,doc,docx', 'extensions:pdf,doc,docx', 'max:15360'],
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
