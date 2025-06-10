<?php

namespace App\Http\Requests;

use App\Models\Album;
use Illuminate\Foundation\Http\FormRequest;

class AlbumStoreRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'image_url' => 'nullable|url|max:255',
            'genre' => 'required|in:' . implode(',', array_keys(Album::GENRES)),
            'description' => 'nullable|string',
            'contributions' => 'required|array|min:1', // contribution field required, it must be an array and there must be at least one element in the array
            'contributions.*.artist_id' => 'required|exists:artists,id', // contributions.* = For EACH item in the contributions array. Look at the artist_id field. It must be present. The value must exist in the artists table's id column
            'contributions.*.role_id' => 'required|exists:roles,id', // contributions.* = For EACH item in the contributions array. Look at the role_id field. It must be present. The value must exist in the roles table's id column
        ];
    }
}
