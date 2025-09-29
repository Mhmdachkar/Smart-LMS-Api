<?php
// File: app/Http/Requests/Api/V1/RegisterRequest.php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'first_name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'regex:/^[a-zA-Z\s]+$/', // Only letters and spaces
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'regex:/^[a-zA-Z\s]+$/', // Only letters and spaces
            ],
            'email' => [
                'required',
                'string',
                app()->environment('production') ? 'email:rfc,dns' : 'email:rfc',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'confirmed', // Requires password_confirmation field
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', // Strong password
            ],
            'password_confirmation' => [
                'required',
                'string',
                'min:8',
                'max:255',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[+]?[0-9\s\-\(\)]+$/', // Phone number format
            ],
            'role' => [
                'nullable',
                'string',
                Rule::in(['student', 'instructor']), // Admin role can only be assigned by existing admin
            ],
            'bio' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'timezone' => [
                'nullable',
                'string',
                'max:50',
                'in:UTC,America/New_York,America/Los_Angeles,Europe/London,Europe/Paris,Asia/Tokyo,Asia/Dubai,Australia/Sydney',
            ],
            'language' => [
                'nullable',
                'string',
                'max:5',
                'in:en,es,fr,de,ar,zh,ja,ko',
            ],
            'social_links' => [
                'nullable',
                'array',
            ],
            'social_links.linkedin' => [
                'nullable',
                'url',
                'max:255',
            ],
            'social_links.twitter' => [
                'nullable',
                'url',
                'max:255',
            ],
            'social_links.github' => [
                'nullable',
                'url',
                'max:255',
            ],
            'social_links.website' => [
                'nullable',
                'url',
                'max:255',
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.regex' => 'First name can only contain letters and spaces.',
            'first_name.min' => 'First name must be at least 2 characters.',
            
            'last_name.required' => 'Last name is required.',
            'last_name.regex' => 'Last name can only contain letters and spaces.',
            'last_name.min' => 'Last name must be at least 2 characters.',
            
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            
            'password_confirmation.required' => 'Password confirmation is required.',
            
            'phone.regex' => 'Please provide a valid phone number.',
            
            'role.in' => 'Role must be either student or instructor.',
            
            'bio.max' => 'Bio cannot exceed 1000 characters.',
            
            'timezone.in' => 'Please select a valid timezone.',
            
            'language.in' => 'Please select a valid language.',
            
            'social_links.*.url' => 'Social media links must be valid URLs.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'password_confirmation' => 'password confirmation',
            'social_links.linkedin' => 'LinkedIn URL',
            'social_links.twitter' => 'Twitter URL',
            'social_links.github' => 'GitHub URL',
            'social_links.website' => 'Website URL',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors,
            ], 422)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Clean and normalize data
        $this->merge([
            'first_name' => $this->cleanName($this->first_name),
            'last_name' => $this->cleanName($this->last_name),
            'email' => strtolower(trim($this->email)),
            'phone' => $this->cleanPhone($this->phone),
            'role' => $this->role ?? 'student',
            'timezone' => $this->timezone ?? 'UTC',
            'language' => $this->language ?? 'en',
        ]);
    }

    /**
     * Clean name fields (remove extra spaces, capitalize properly)
     */
    private function cleanName(?string $name): ?string
    {
        if (!$name) {
            return $name;
        }

        // Remove extra spaces and capitalize each word
        return ucwords(strtolower(trim(preg_replace('/\s+/', ' ', $name))));
    }

    /**
     * Clean phone number (remove non-numeric characters except + at start)
     */
    private function cleanPhone(?string $phone): ?string
    {
        if (!$phone) {
            return $phone;
        }

        // Keep only numbers, spaces, +, -, (, )
        return trim(preg_replace('/[^0-9+\s\-\(\)]/', '', $phone));
    }
}