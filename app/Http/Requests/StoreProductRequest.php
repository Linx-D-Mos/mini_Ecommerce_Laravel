<?php

namespace App\Http\Requests;

use App\Enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'price' => 'required|integer',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpg,png,svg,jpeg',
            'content' => 'required|file|mimes:pdf,zip,txt,odt',
            'status' => ['required',Rule::enum(ProductStatus::class)]
        ];
    }
}
