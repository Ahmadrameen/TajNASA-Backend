<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user_id;

        return [
            'project_id' => 'required',
            'user_id'    => 'required',
            'type'       => 'required',
            'status'     => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.unique' => 'The project ID has already been taken for this user.',
            'user_id.required'  => 'The user ID field is required.',
            'type.required'     => 'The type field is required.',
            'status.required'   => 'The status field is required.',
        ];
    }
}
