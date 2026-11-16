<?php

namespace Modules\Complaint\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Modules\Complaint\Http\Requests\SaveComplaintRequest;
use Modules\Complaint\Http\Requests\UpdateComplaintStatusRequest;
use Modules\Complaint\Models\Complaint;
use Modules\Facility\Models\Facility;

class ComplaintController extends Controller
{
    public function page() { return Inertia::render('Complaint::Index', ['facilities'=>Facility::orderBy('name')->get(['id','name','location'])]); }

    public function data(Request $request)
    {
        $user=$request->user(); $search=trim((string)$request->query('search',''));
        return Complaint::with(['user:id,name','facility:id,name,location'])
            ->when(!$user->isAdmin(),fn($q)=>$q->where('user_id',$user->id))
            ->when($search,fn($q)=>$q->where(fn($s)=>$s->where('title','like',"%{$search}%")->orWhere('location','like',"%{$search}%")->orWhere('status','like',"%{$search}%")))
            ->latest()->paginate(8)->withQueryString();
    }

    public function store(SaveComplaintRequest $request)
    {
        $data=$request->validated(); if($request->hasFile('photo'))$data['photo_path']=$request->file('photo')->store('complaints','public');
        unset($data['photo']); $data['user_id']=$request->user()->id; $data['status']='menunggu';
        return response()->json(['message'=>'Pengaduan berhasil dikirim.','complaint'=>Complaint::create($data)->load(['user:id,name','facility:id,name,location'])],201);
    }

    public function update(SaveComplaintRequest $request, Complaint $complaint)
    {
        $data=$request->validated(); if($request->hasFile('photo')){if($complaint->photo_path)Storage::disk('public')->delete($complaint->photo_path);$data['photo_path']=$request->file('photo')->store('complaints','public');}
        unset($data['photo']);$complaint->update($data);
        return response()->json(['message'=>'Pengaduan berhasil diperbarui.','complaint'=>$complaint->fresh(['user:id,name','facility:id,name,location'])]);
    }

    public function updateStatus(UpdateComplaintStatusRequest $request, Complaint $complaint) { $complaint->update($request->validated()); return response()->json(['message'=>'Status laporan diperbarui.','complaint'=>$complaint->fresh()]); }
    public function destroy(Request $request, Complaint $complaint) { abort_unless($request->user()->isAdmin()||($complaint->user_id===$request->user()->id&&$complaint->status==='menunggu'),403); if($complaint->photo_path)Storage::disk('public')->delete($complaint->photo_path);$complaint->delete();return response()->json(['message'=>'Pengaduan berhasil dihapus.']); }
    public function photo(Request $request, Complaint $complaint) { abort_unless($request->user()->isAdmin()||$complaint->user_id===$request->user()->id,403);abort_unless($complaint->photo_path&&Storage::disk('public')->exists($complaint->photo_path),404);return Storage::disk('public')->response($complaint->photo_path); }
}
