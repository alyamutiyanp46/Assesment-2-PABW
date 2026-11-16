<?php

namespace Modules\Peminjaman\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePeminjamanRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }

    public function rules(): array
    {
        return [
            'nama_peminjam' => ['required', 'string', 'max:255'],
            'judul_buku' => ['required', 'string', 'max:255'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali' => ['nullable', 'date', 'after_or_equal:tanggal_pinjam'],
            'status' => ['required', Rule::in(['Dipinjam', 'Dikembalikan'])],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_peminjam.required' => 'Nama peminjam wajib diisi.', 'judul_buku.required' => 'Judul buku wajib diisi.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.', 'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
            'status.in' => 'Status harus Dipinjam atau Dikembalikan.', 'file.mimes' => 'Berkas harus berformat JPG, PNG, atau PDF.', 'file.max' => 'Ukuran berkas maksimal 2 MB.',
        ];
    }
}
