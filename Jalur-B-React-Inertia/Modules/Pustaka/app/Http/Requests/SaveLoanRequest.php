<?php

namespace Modules\Pustaka\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveLoanRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }
    public function rules(): array { return [
        'book_id' => ['required', 'exists:books,id'],
        'borrowed_at' => ['required', 'date'], 'due_at' => ['required', 'date', 'after_or_equal:borrowed_at'],
        'notes' => ['nullable', 'string', 'max:1000'],
        'status' => ['sometimes', 'in:menunggu,disetujui,ditolak,dikembalikan'],
    ]; }
}
