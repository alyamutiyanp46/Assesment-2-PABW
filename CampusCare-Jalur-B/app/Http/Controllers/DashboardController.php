<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Complaint\Models\Complaint;
use Modules\Facility\Models\Facility;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with record counts per module.
     */
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $complaints = Complaint::query()->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id));
        return Inertia::render('dashboard', [
            'facilityCount' => Facility::count(),
            'complaintCount' => (clone $complaints)->count(),
            'waitingCount' => (clone $complaints)->where('status', 'menunggu')->count(),
            'recentComplaints' => (clone $complaints)->with(['facility:id,name', 'user:id,name'])->latest()->take(5)->get(),
        ]);
    }
}
