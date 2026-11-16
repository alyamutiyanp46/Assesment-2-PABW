<?php

namespace Modules\Complaint\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Complaint\Http\Requests\SaveComplaintRequest;
use Modules\Complaint\Http\Requests\UpdateComplaintStatusRequest;
use Modules\Complaint\Models\Complaint;
use Modules\Facility\Models\Facility;

class ComplaintController extends Controller
{
    // Admin melihat semua laporan; user hanya dapat melihat laporan miliknya.
    public function index(Request $request)
    {
        $user = $request->user();
        $search = trim((string) $request->query('search', ''));
        $complaints = Complaint::with(['user', 'facility'])
            ->when($user->role !== 'admin', fn ($query) => $query->where('user_id', $user->id))
            ->when($search, fn ($query) => $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%")->orWhere('status', 'like', "%{$search}%")))
            ->latest()->paginate(8)->withQueryString();
        $facilities = Facility::orderBy('name')->get();

        return view('complaint::index', compact('complaints', 'facilities', 'search'));
    }

    public function store(SaveComplaintRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('complaints', 'public');
        }
        unset($data['photo']);
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'menunggu'; // user tidak boleh menentukan statusnya sendiri.
        $complaint = Complaint::create($data)->load(['user', 'facility']);

        return response()->json(['message' => 'Pengaduan berhasil dikirim.', 'complaint' => $complaint], 201);
    }

    public function update(SaveComplaintRequest $request, Complaint $complaint)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            if ($complaint->photo_path) Storage::disk('public')->delete($complaint->photo_path);
            $data['photo_path'] = $request->file('photo')->store('complaints', 'public');
        }
        unset($data['photo']);
        $complaint->update($data);

        return response()->json(['message' => 'Pengaduan berhasil diperbarui.', 'complaint' => $complaint->fresh(['user', 'facility'])]);
    }

    public function updateStatus(UpdateComplaintStatusRequest $request, Complaint $complaint)
    {
        $complaint->update($request->validated());
        return response()->json(['message' => 'Status pengaduan berhasil diperbarui.', 'complaint' => $complaint->fresh()]);
    }

    public function destroy(Request $request, Complaint $complaint)
    {
        abort_unless($request->user()->role === 'admin' || ($complaint->user_id === $request->user()->id && $complaint->status === 'menunggu'), 403);
        if ($complaint->photo_path) Storage::disk('public')->delete($complaint->photo_path);
        $complaint->delete();
        return response()->json(['message' => 'Pengaduan berhasil dihapus.']);
    }

    // Mengirim gambar dari Storage langsung; tidak bergantung pada public/storage symlink.
    public function photo(Complaint $complaint, Request $request)
    {
        abort_unless($request->user()->role === 'admin' || $complaint->user_id === $request->user()->id, 403);
        abort_unless($complaint->photo_path && Storage::disk('public')->exists($complaint->photo_path), 404);
        return Storage::disk('public')->response($complaint->photo_path);
    }
}
