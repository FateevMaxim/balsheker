<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShowUsersRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'string',
            'city' => 'string'
        ];
    }

    public function userStatus(): ?string
    {
       return $this->input('status');
    }
    public function userCity(): ?string
    {
       return $this->input('city');
    }
}
