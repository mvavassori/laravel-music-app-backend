<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NextSongGetRequest extends FormRequest
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
            'current_song_id' => 'required|integer',
            'song_ids' => 'required|array', // array of song ids in current order
            'song_ids.*' => 'integer',
            'user_id' => 'required|exists:users,id',
            'should_generate' => 'required|boolean'
        ];
    }
}
