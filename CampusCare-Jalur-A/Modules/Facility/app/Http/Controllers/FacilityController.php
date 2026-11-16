<?php

namespace Modules\Facility\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Facility\Http\Requests\SaveFacilityRequest;
use Modules\Facility\Models\Facility;

class FacilityController extends Controller
{
    // Halaman awal memakai Blade; pencarian dan pagination diproses Eloquent.
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $facilities = Facility::query()
            ->when($search, fn ($query) => $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%")->orWhere('category', 'like', "%{$search}%")))
            ->withCount('complaints')->latest()->paginate(8)->withQueryString();

        return view('facility::index', compact('facilities', 'search'));
    }

    public function store(SaveFacilityRequest $request)
    {
        $facility = Facility::create($request->validated());
        return response()->json(['message' => 'Fasilitas berhasil ditambahkan.', 'facility' => $facility->loadCount('complaints')], 201);
    }

    public function update(SaveFacilityRequest $request, Facility $facility)
    {
        $facility->update($request->validated());
        return response()->json(['message' => 'Fasilitas berhasil diperbarui.', 'facility' => $facility->fresh()->loadCount('complaints')]);
    }

    public function destroy(Facility $facility)
    {
        if ($facility->complaints()->exists()) {
            return response()->json(['message' => 'Fasilitas memiliki riwayat pengaduan dan tidak dapat dihapus.'], 422);
        }
        $facility->delete();
        return response()->json(['message' => 'Fasilitas berhasil dihapus.']);
    }
}
