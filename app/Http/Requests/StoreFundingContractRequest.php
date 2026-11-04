<?php

namespace App\Http\Requests;

use App\Models\FundingContract;
use App\Models\Proposal;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreFundingContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', FundingContract::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'proposal_id' => ['required', 'integer', 'exists:proposals,id', 'unique:funding_contracts,proposal_id'],
            'contract_number' => ['required', 'string', 'max:100', 'unique:funding_contracts,contract_number'],
            'decree_number' => ['nullable', 'string', 'max:100'],
            'signed_at' => ['required', 'date'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'contract_value' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['draft', 'signed', 'active'])],
            'document' => ['nullable', 'file', 'mimes:pdf', 'extensions:pdf', 'max:10240'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->hasAny(['proposal_id', 'contract_value'])) {
                return;
            }
            $proposal = Proposal::with('decision')->find($this->integer('proposal_id'));
            if (! $proposal?->decision || $proposal->decision->decision !== 'accepted') {
                $validator->errors()->add('proposal_id', 'Kontrak hanya dapat dibuat untuk proposal yang diterima.');

                return;
            }
            if ($this->integer('contract_value') > $proposal->decision->approved_budget) {
                $validator->errors()->add('contract_value', 'Nilai kontrak tidak boleh melebihi dana yang disetujui.');
            }
        }];
    }
}
