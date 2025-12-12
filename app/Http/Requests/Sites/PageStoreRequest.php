<?php

namespace App\Http\Requests\Sites;

use Illuminate\Foundation\Http\FormRequest;

class PageStoreRequest extends FormRequest
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
            'display_name' => ['required', 'string', 'max:90'],
            'url' => ['required'],
            'description' => ['nullable', 'string', 'max:1000'],
            'paused' => ['nullable', 'boolean'],
            'check_interval_seconds' => ['nullable', 'integer'],
            'timeout_seconds' => ['nullable', 'integer'],
            'verify_ssl' => ['nullable', 'boolean'],
            'expected_status' => ['array', 'required'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'paused' => $this->has('paused') && $this->paused === 'on',
            'verify_ssl' => $this->has('verify_ssl') && $this->verify_ssl === 'on',
        ]);
    }
}
