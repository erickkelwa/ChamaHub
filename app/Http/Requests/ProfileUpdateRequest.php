<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];

        if ($user->role === 'admin' || $user->role === 'treasurer') {
            $rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)];
            $rules['phone'] = ['nullable', 'string', 'max:15'];
        }

        return $rules;
    }
}
