<?php

namespace Modules\Facility\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Facility\Http\Requests\SaveFacilityRequest;
use Modules\Facility\Models\Facility;

class FacilityController extends Controller
{
    public function page() { return Inertia::render('Facility::Index'); }

    // Fetch meminta data JSON per halaman; search + pagination tetap dilakukan query Eloquent.
    public function data(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        return Facility::query()->when($search, fn ($q) => $q->where(fn ($s) => $s->where('name', 'like', "%{$search}%")
            ->orWhere('location', 'like', "%{$search}%")->orWhere('category', 'like', "%{$search}%")))
            ->withCount('complaints')->latest()->paginate(8)->withQueryString();
    }

    public function store(SaveFacilityRequest $request) { return response()->json(['message'=>'Fasilitas berhasil ditambahkan.','facility'=>Facility::create($request->validated())->loadCount('complaints')],201); }
    public function update(SaveFacilityRequest $request, Facility $facility) { $facility->update($request->validated()); return response()->json(['message'=>'Fasilitas berhasil diperbarui.','facility'=>$facility->fresh()->loadCount('complaints')]); }
    public function destroy(Facility $facility) { if($facility->complaints()->exists()) return response()->json(['message'=>'Fasilitas mempunyai laporan terkait, jadi tidak dapat dihapus.'],422); $facility->delete(); return response()->json(['message'=>'Fasilitas berhasil dihapus.']); }
}
