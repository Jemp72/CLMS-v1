<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingStatusRequest extends FormRequest
{
    /**
     * Admin-only action — admin routes are gated by RequireLogin middleware,
     * so authorize() can simply return true here.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_status' => [
                'required',
                'in:approved,rejected,completed',
            ],

            'approved_by' => [
                'nullable',
                'integer',
                'exists:system_users,system_user_id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_status.in' => 'Status must be approved, rejected, or completed.',
        ];
    }
}
