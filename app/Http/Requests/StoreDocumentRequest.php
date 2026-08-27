<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'tags' => 'nullable|string',
            'file' => 'required|file|max:20480',
            'project_id' => 'nullable|exists:projects,id',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'is_confidential' => 'nullable|boolean',
            'expiry_date' => 'nullable|date',
            'signers' => 'nullable|array',
            'signers.*' => 'exists:users,id',
        ];
    }
}
