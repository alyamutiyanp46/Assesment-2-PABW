<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Complaint\Models\Complaint;
use Modules\Facility\Models\Facility;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $query = Complaint::with(['facility', 'user'])->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id));
        return view('dashboard', [
            'facilityCount' => Facility::count(),
            'totalComplaints' => (clone $query)->count(),
            'waitingComplaints' => (clone $query)->where('status', 'menunggu')->count(),
            'recentComplaints' => (clone $query)->latest()->take(5)->get(),
        ]);
    }
}
