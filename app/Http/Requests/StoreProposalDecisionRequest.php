<?php

namespace App\Http\Requests;

use App\Models\Proposal;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProposalDecisionRequest extends FormRequest
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
            'decision' => ['required', Rule::in(['accepted', 'revision', 'rejected'])],
            'approved_budget' => ['nullable', 'required_if:decision,accepted', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $proposal = $this->route('proposal');
            if ($proposal instanceof Proposal && $this->integer('approved_budget') > $proposal->requested_budget) {
                $validator->errors()->add('approved_budget', 'Dana disetujui tidak boleh melebihi dana yang diajukan.');
            }
        }];
    }
}
