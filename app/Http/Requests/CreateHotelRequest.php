<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class CreateHotelRequest extends FormRequest {
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_platform_admin;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('timezone') || empty($this->input('timezone'))) {
            $this->merge(['timezone' => 'Asia/Kolkata']);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'alpha_dash', 'max:150', 'unique:hotels,slug'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'timezone' => ['nullable', 'timezone'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'admin_name' => ['required', 'string', 'max:120'],
            'admin_email' => ['required', 'email', 'max:255'],
            'plan_name' => ['nullable', 'string', 'max:100'],
            'plan_fee' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
