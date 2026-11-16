<?php
namespace App\Http\Middleware;
use Inertia\Middleware;
use Illuminate\Http\Request;
// Data identitas dan notifikasi dibagikan ke semua page React melalui Inertia.
class HandleInertiaRequests extends Middleware {
    protected $rootView='app';
    public function share(Request $request): array {
        return array_merge(parent::share($request),[
            'auth'=>['user'=>$request->user()?->only('id','name','email','role')],
            'flash'=>['success'=>fn()=>$request->session()->get('success')],
        ]);
    }
}
