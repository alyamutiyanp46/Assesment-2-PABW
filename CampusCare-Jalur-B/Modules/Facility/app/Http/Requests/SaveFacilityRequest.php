<?php

namespace Modules\Facility\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveFacilityRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user()?->isAdmin(); }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'location' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:2000'],
            'condition' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
        ];
    }
}
