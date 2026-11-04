<?php

namespace App\Http\Requests;

use App\Models\FundingContract;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResearchOutputRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $contract = $this->route('contract');

        return $contract instanceof FundingContract && $this->user()?->can('manageExecution', $contract);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['jurnal', 'prosiding', 'hki', 'buku', 'produk', 'media', 'lainnya'])],
            'title' => ['required', 'string', 'min:10', 'max:500'],
            'achievement_status' => ['required', Rule::in(['draft', 'submitted', 'accepted', 'published', 'granted', 'implemented'])],
            'identifier' => ['nullable', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publication_date' => ['nullable', 'date'],
            'url' => ['nullable', 'url:http,https', 'max:2048'],
            'evidence' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'extensions:pdf,jpg,jpeg,png', 'max:10240'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
