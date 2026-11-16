<?php

namespace Modules\Pustaka\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Form Request memisahkan validasi input dari logika controller.
class SaveBookRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->role === 'admin'; }
    public function rules(): array
    {
        $book = $this->route('book');
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:180'],
            'author' => ['required', 'string', 'max:120'],
            'isbn' => ['nullable', 'string', 'max:40', Rule::unique('books', 'isbn')->ignore($book)],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:3000'],
            'cover' => ['nullable', 'image', 'max:2048'],
        ];
    }
    public function messages(): array { return [
        'category_id.required' => 'Kategori wajib dipilih.', 'title.required' => 'Judul buku wajib diisi.',
        'author.required' => 'Nama penulis wajib diisi.', 'isbn.unique' => 'ISBN sudah digunakan.',
        'stock.min' => 'Stok tidak boleh negatif.', 'cover.image' => 'Sampul harus berupa gambar.', 'cover.max' => 'Ukuran gambar maksimal 2 MB.',
    ]; }
}
