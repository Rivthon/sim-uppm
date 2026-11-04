<?php

namespace App\Http\Requests;

use App\Models\StrategicPlan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SaveRoadmapMilestoneRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $plan = $this->route('strategic_plan') ?? $this->route('milestone')?->strategicPlan;

        return $plan instanceof StrategicPlan && $this->user()?->can('update', $plan);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'end_year' => ['required', 'integer', 'gte:start_year', 'max:2100'],
            'theme' => ['required', 'string', 'min:5', 'max:255'],
            'focus' => ['required', 'string', 'min:20', 'max:10000'],
            'indicators' => ['required', 'string', 'min:10', 'max:10000'],
            'target_outputs' => ['nullable', 'string', 'max:5000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:1000'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $plan = $this->route('strategic_plan') ?? $this->route('milestone')?->strategicPlan;
            if ($plan instanceof StrategicPlan && ($this->integer('start_year') < $plan->start_year || $this->integer('end_year') > $plan->end_year)) {
                $validator->errors()->add('start_year', 'Periode milestone harus berada di dalam periode Renstra.');
            }
        }];
    }
}
