<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SubscribeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //dd($this->input());

        $subscribedToId = $this->input('id');
        $subscriberId = Auth::id();

        //You cannot subscribe to yourself
        if ($subscriberId == $subscribedToId) {
            return false; // Authorization fails
        }

        //Ensure the user being subscribed to actually exists
        if (!User::where('id', $subscribedToId)->exists()) {
            return false; // Authorization fails
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //dd($this->input());
        return [
            'id' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id.required' => 'The user to subscribe to is required.',
            'id.integer' => 'Invalid user ID for subscription.',
        ];
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        $subscribedToId = $this->input('id');
        $subscriberId = Auth::id();

        if ($subscriberId == $subscribedToId) {
            throw new \Illuminate\Auth\Access\AuthorizationException('You cannot subscribe to yourself.');
        }

        // If subscribedToUser doesn't exist. This assumes `subscribed_to_id` is passed.
        if ($subscribedToId && !User::where('id', $subscribedToId)->exists()) {
            throw new \Illuminate\Auth\Access\AuthorizationException('User to subscribe to not found.');
        }

        // Default message if none of the specific conditions met (e.g., subscriber not found - less likely)
        throw new \Illuminate\Auth\Access\AuthorizationException('This action is unauthorized.');
    }

}
