<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavePublicPageRequest extends FormRequest
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
            'parent_id' => ['nullable', 'integer', Rule::exists('public_pages', 'id')->whereNull('parent_id'), Rule::notIn([$this->route('public_page')?->id])],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash:ascii', Rule::unique('public_pages', 'slug')->ignore($this->route('public_page'))],
            'summary' => ['nullable', 'string', 'max:1000'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:5120'],
            'remove_featured_image' => ['sometimes', 'boolean'],
            'content' => ['nullable', 'string', 'max:100000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:1000'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'featured_image.image' => 'Gambar sampul harus berupa file gambar yang valid.',
            'featured_image.mimes' => 'Gambar sampul harus berformat JPG, PNG, atau WebP.',
            'featured_image.extensions' => 'Ekstensi gambar sampul harus JPG, PNG, atau WebP.',
            'featured_image.max' => 'Ukuran gambar sampul maksimal 5 MB.',
        ];
    }
}
