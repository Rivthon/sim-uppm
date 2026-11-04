<?php

namespace App\Http\Requests;

use App\Models\FundingContract;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreActivityLogRequest extends FormRequest
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
            'activity_date' => ['required', 'date'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'result' => ['required', 'string', 'min:10', 'max:5000'],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'expense_amount' => ['required', 'integer', 'min:0'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'extensions:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $contract = $this->route('contract');
            if ($contract instanceof FundingContract && $this->date('activity_date')?->lt($contract->starts_at)) {
                $validator->errors()->add('activity_date', 'Tanggal kegiatan tidak boleh sebelum masa kontrak.');
            }
        }];
    }
}
