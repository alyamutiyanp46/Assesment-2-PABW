<?php

namespace Modules\Complaint\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        $complaint = $this->route('complaint');
        return $this->user() && (!$complaint || $this->user()->role === 'admin' || ($complaint->user_id === $this->user()->id && $complaint->status === 'menunggu'));
    }

    public function rules(): array
    {
        return [
            'facility_id' => ['required', 'exists:facilities,id'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:3000'],
            'location' => ['required', 'string', 'max:180'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
