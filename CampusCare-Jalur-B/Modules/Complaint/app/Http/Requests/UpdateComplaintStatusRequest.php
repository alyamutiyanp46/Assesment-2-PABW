<?php

namespace Modules\Complaint\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComplaintStatusRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user()?->isAdmin(); }
    public function rules(): array { return ['status' => ['required', 'in:menunggu,diproses,selesai,ditolak']]; }
}
