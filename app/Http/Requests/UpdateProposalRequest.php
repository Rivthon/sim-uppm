<?php

namespace App\Http\Requests;

use App\Models\GrantPeriod;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProposalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('proposal')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'grant_period_id' => ['required', 'integer', Rule::exists('grant_periods', 'id')->where('status', 'open')],
            'research_field_id' => ['required', 'integer', 'exists:research_fields,id'],
            'title' => ['required', 'string', 'max:255'],
            'abstract' => ['required', 'string', 'max:5000'],
            'background' => ['required', 'string', 'max:10000'],
            'objectives' => ['required', 'string', 'max:5000'],
            'methodology' => ['required', 'string', 'max:10000'],
            'roadmap_alignment' => ['required', 'string', 'max:5000'],
            'partner_name' => ['nullable', 'string', 'max:255'],
            'partner_location' => ['nullable', 'string', 'max:255'],
            'requested_budget' => ['required', 'integer', 'min:0'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:36'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'extensions:pdf,doc,docx', 'max:10240'],
            'members' => ['nullable', 'array', 'max:10'],
            'members.*.name' => ['required_with:members', 'string', 'max:255'],
            'members.*.identity_number' => ['nullable', 'string', 'max:50'],
            'members.*.member_type' => ['required_with:members', Rule::in(['dosen', 'mahasiswa'])],
            'members.*.study_program' => ['nullable', 'string', 'max:255'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->hasAny(['grant_period_id', 'requested_budget'])) {
                return;
            }

            $period = GrantPeriod::with('scheme:id,maximum_funding')->find($this->integer('grant_period_id'));
            if ($period && $this->integer('requested_budget') > $period->scheme->maximum_funding) {
                $validator->errors()->add('requested_budget', 'Dana yang diajukan melebihi batas skema hibah.');
            }
        }];
    }
}
