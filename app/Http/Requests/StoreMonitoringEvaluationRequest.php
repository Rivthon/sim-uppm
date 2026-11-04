<?php

namespace App\Http\Requests;

use App\Models\MonitoringEvaluation;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMonitoringEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', MonitoringEvaluation::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'progress_report_id' => ['nullable', 'integer', 'exists:progress_reports,id'],
            'evaluator_id' => ['required', 'integer', 'exists:users,id'],
            'scheduled_at' => ['required', 'date'],
            'mode' => ['required', Rule::in(['online', 'onsite'])],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $contract = $this->route('contract');

            if ($this->filled('progress_report_id') && ! $contract->progressReports()->whereKey($this->integer('progress_report_id'))->exists()) {
                $validator->errors()->add('progress_report_id', 'Laporan kemajuan tidak terkait dengan kontrak ini.');
            }

            $evaluator = User::query()->find($this->integer('evaluator_id'));
            if ($evaluator && ! ($evaluator->hasRole('reviewer') || $evaluator->hasRole('admin_uppm'))) {
                $validator->errors()->add('evaluator_id', 'Evaluator harus memiliki peran reviewer atau admin UPPM.');
            }
        }];
    }
}
