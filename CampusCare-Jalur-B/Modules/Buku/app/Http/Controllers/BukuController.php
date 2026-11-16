<?php

namespace Modules\Buku\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Buku\Http\Requests\StoreBukuRequest;
use Modules\Buku\Http\Requests\UpdateBukuRequest;
use Modules\Buku\Models\Buku;

class BukuController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $books = Buku::query()->orderBy('judul')->get();

        return Inertia::render('Buku::index', [
            'books' => $books,
            'filters' => ['search' => $search],
            'bookCount' => $books->count(),
            'isAdmin' => $request->user()->isAdmin(),
            'canAdd' => $request->user()->hasRole('admin', 'petugas'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $search = $request->string('search')->trim()->toString();
        $books = Buku::query()
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('kode_buku', 'like', "%{$search}%")
                ->orWhere('judul', 'like', "%{$search}%")
                ->orWhere('penulis', 'like', "%{$search}%")
                ->orWhere('kategori', 'like', "%{$search}%")))
            ->orderBy('judul')
            ->get();

        return response()->json(['books' => $books, 'total' => Buku::count()]);
    }

    public function store(StoreBukuRequest $request): JsonResponse
    {
        $book = Buku::create($request->validated());

        return response()->json(['message' => 'Buku berhasil ditambahkan.', 'book' => $book], 201);
    }

    public function update(UpdateBukuRequest $request, Buku $buku): JsonResponse
    {
        $buku->update($request->validated());

        return response()->json(['message' => 'Data buku berhasil diperbarui.', 'book' => $buku->refresh()]);
    }

    public function destroy(Buku $buku): JsonResponse
    {
        $buku->delete();

        return response()->json(['message' => 'Buku berhasil dihapus.']);
    }
}
