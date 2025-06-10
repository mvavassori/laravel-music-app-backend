<?php

namespace App\Http\Requests;

use App\Models\Album;
use Illuminate\Foundation\Http\FormRequest;

class AlbumUpdateRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'image_url' => 'nullable|url|max:255',
            'genre' => 'sometimes|required|in:' . implode(',', array_keys(Album::GENRES)),
            'description' => 'nullable|string',
            'contributions' => 'sometimes|array|min:1',
            'contributions.*.artist_id' => 'required|exists:artists,id',
            'contributions.*.role_id' => 'required|exists:roles,id'
        ];
    }
}
