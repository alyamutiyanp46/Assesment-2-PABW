<?php

namespace Modules\Peminjaman\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Peminjaman\Http\Requests\StorePeminjamanRequest;
use Modules\Peminjaman\Http\Requests\UpdatePeminjamanRequest;
use Modules\Peminjaman\Models\Peminjaman;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Peminjaman::index');
    }

    public function apiIndex(Request $request): JsonResponse
    {
        $query = Peminjaman::query()->with('user:id,name');
        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        return response()->json(['data' => $query->latest()->get()->map(fn (Peminjaman $item) => [
            ...$item->toArray(), 'file_url' => $item->file_path ? Storage::disk('public')->url($item->file_path) : null,
        ])]);
    }

    public function store(StorePeminjamanRequest $request): JsonResponse
    {
        $data = $request->validated();
        unset($data['file']);
        $data['user_id'] = $request->user()->id;
        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('peminjaman', 'public');
        }
        $peminjaman = Peminjaman::create($data);

        return response()->json(['message' => 'Peminjaman berhasil ditambahkan.', 'data' => $peminjaman], 201);
    }

    public function update(UpdatePeminjamanRequest $request, Peminjaman $peminjaman): JsonResponse
    {
        abort_unless($request->user()->isAdmin() || $peminjaman->user_id === $request->user()->id, 403);
        $data = $request->validated();
        unset($data['file']);
        if ($request->hasFile('file')) {
            if ($peminjaman->file_path) Storage::disk('public')->delete($peminjaman->file_path);
            $data['file_path'] = $request->file('file')->store('peminjaman', 'public');
        }
        $peminjaman->update($data);

        return response()->json(['message' => 'Peminjaman berhasil diperbarui.', 'data' => $peminjaman->fresh()]);
    }

    public function destroy(Request $request, Peminjaman $peminjaman): JsonResponse
    {
        abort_unless($request->user()->isAdmin() || $peminjaman->user_id === $request->user()->id, 403);
        if ($peminjaman->file_path) Storage::disk('public')->delete($peminjaman->file_path);
        $peminjaman->delete();

        return response()->json(['message' => 'Peminjaman berhasil dihapus.']);
    }
}
