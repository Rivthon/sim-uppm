<?php

namespace App\Http\Requests;

use App\Models\Proposal;
use App\Models\ReviewAssignment;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreReviewAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', ReviewAssignment::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'proposal_id' => ['required', 'integer', Rule::exists('proposals', 'id')->where('status', 'submitted')],
            'reviewer_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('review_assignments')->where(fn ($query) => $query->where('proposal_id', $this->integer('proposal_id'))),
            ],
            'due_at' => ['required', 'date', 'after:today'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->hasAny(['proposal_id', 'reviewer_id'])) {
                return;
            }

            $proposal = Proposal::find($this->integer('proposal_id'));
            $reviewer = User::find($this->integer('reviewer_id'));

            if ($proposal?->lead_id === $reviewer?->id) {
                $validator->errors()->add('reviewer_id', 'Ketua proposal tidak dapat menjadi reviewer proposalnya sendiri.');
            }

            if ($reviewer && ! $reviewer->hasRole('reviewer')) {
                $validator->errors()->add('reviewer_id', 'Pengguna yang dipilih belum memiliki peran reviewer.');
            }
        }];
    }
}
