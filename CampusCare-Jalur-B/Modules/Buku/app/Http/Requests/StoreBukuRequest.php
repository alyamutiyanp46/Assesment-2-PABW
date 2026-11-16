<?php

namespace Modules\Buku\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBukuRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'kode_buku' => ['required', 'string', 'max:20', Rule::unique('bukus', 'kode_buku')],
            'judul' => ['required', 'string', 'max:150'],
            'penulis' => ['required', 'string', 'max:100'],
            'kategori' => ['required', 'string', 'max:50'],
            'stok' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kode_buku.required' => 'Kode buku wajib diisi.',
            'kode_buku.unique' => 'Kode buku sudah digunakan.',
            'judul.required' => 'Judul buku wajib diisi.',
            'penulis.required' => 'Penulis wajib diisi.',
            'kategori.required' => 'Kategori wajib diisi.',
            'stok.required' => 'Stok buku wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka.',
            'stok.min' => 'Stok tidak boleh bernilai negatif.',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin', 'petugas') ?? false;
    }
}
