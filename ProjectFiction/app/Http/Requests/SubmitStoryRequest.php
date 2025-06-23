<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Genre;
use Illuminate\Validation\Rules\Enum;

class SubmitStoryRequest extends FormRequest
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
            'synopsis' => 'required|string|max:500|min:25',
            'genre' => ['required', 'string', new Enum(Genre::class)],
            'story' => 'required|string|max:1024000|min:500', // 1MB in kilobytes (1024 * 1000)
        ];
    }

    /**
     * Custom eror messages
     * @return array{story.max: string}
     */
    public function messages(): array
    {
        return [
            'story.max' => 'The story content must not exceed 1MB.',
            'genre.' . Enum::class => 'The selected genre is not a valid option.',
        ];
    }

}
