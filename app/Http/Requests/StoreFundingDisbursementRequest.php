<?php

namespace App\Http\Requests;

use App\Models\FundingContract;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreFundingDisbursementRequest extends FormRequest
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
            'sequence' => ['required', 'integer', 'min:1', 'max:10'],
            'label' => ['required', 'string', 'max:100'],
            'percentage' => ['required', 'integer', 'min:1', 'max:100'],
            'amount' => ['required', 'integer', 'min:1'],
            'scheduled_at' => ['required', 'date'],
            'status' => ['required', Rule::in(['planned', 'paid'])],
            'paid_at' => ['nullable', 'required_if:status,paid', 'date'],
            'proof' => ['nullable', 'required_if:status,paid', 'file', 'mimes:pdf,jpg,jpeg,png', 'extensions:pdf,jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $contract = $this->route('contract');
            if (! $contract instanceof FundingContract || $validator->errors()->hasAny(['amount', 'percentage', 'sequence'])) {
                return;
            }
            if ($contract->disbursements()->where('sequence', $this->integer('sequence'))->exists()) {
                $validator->errors()->add('sequence', 'Nomor termin sudah digunakan.');
            }
            if ($contract->disbursements()->sum('amount') + $this->integer('amount') > $contract->contract_value) {
                $validator->errors()->add('amount', 'Total pencairan tidak boleh melebihi nilai kontrak.');
            }
            if ($contract->disbursements()->sum('percentage') + $this->integer('percentage') > 100) {
                $validator->errors()->add('percentage', 'Total persentase pencairan tidak boleh melebihi 100%.');
            }
        }];
    }
}
