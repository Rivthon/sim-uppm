<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingRequest extends FormRequest
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
            'site_name' => ['required', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_description' => ['required', 'string', 'max:2000'],
            'about_title' => ['required', 'string', 'max:255'],
            'about_content' => ['required', 'string', 'max:5000'],
            'announcement_title' => ['nullable', 'required_if:is_announcement_active,1', 'string', 'max:255'],
            'announcement_content' => ['nullable', 'required_if:is_announcement_active,1', 'string', 'max:5000'],
            'announcement_link' => ['nullable', 'url:http,https', 'max:2048'],
            'is_announcement_active' => ['sometimes', 'boolean'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
