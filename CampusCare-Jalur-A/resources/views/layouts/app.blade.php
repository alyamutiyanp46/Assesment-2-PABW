<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}"><title>@yield('title', 'CampusCare') · CampusCare</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{--ink:#17243a;--muted:#687b91;--paper:#f3f6fb;--brand:#2864dc}body{background:var(--paper);color:var(--ink);font-family:Inter,system-ui,sans-serif}.navbar{background:#14263e}.brand-mark{display:inline-grid;place-items:center;width:36px;height:36px;border-radius:12px;background:#376eff;color:#fff;font-weight:800}.hero{background:linear-gradient(120deg,#172f55,#326ddb);color:white;border-radius:24px}.panel{border:0;border-radius:18px;box-shadow:0 12px 35px #15335b10}.muted{color:var(--muted)}.thumb{width:58px;height:58px;object-fit:cover;border-radius:12px;background:#e7edf6}.btn-primary{background:var(--brand);border-color:var(--brand)}html[data-bs-theme=dark] body{background:#101925;color:#edf3fa}html[data-bs-theme=dark] .panel,html[data-bs-theme=dark] .modal-content{background:#1b2939;color:#edf3fa}html[data-bs-theme=dark] .table{--bs-table-bg:transparent;--bs-table-color:#edf3fa}html[data-bs-theme=dark] .form-control,html[data-bs-theme=dark] .form-select{background:#101a27;color:#edf3fa;border-color:#3b4a5d}
  </style>
  @stack('head')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark"><div class="container"><a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ auth()->check()?route('dashboard'):route('login') }}"><span class="brand-mark">C</span>CampusCare</a>
@auth
<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="mainNav"><div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
<a class="nav-link" href="{{ route('dashboard') }}">Ringkasan</a><a class="nav-link" href="{{ route('facilities.index') }}">Fasilitas</a><a class="nav-link" href="{{ route('complaints.index') }}">Pengaduan</a>
<span class="nav-link text-white-50">{{ auth()->user()->name }} · {{ ucfirst(auth()->user()->role) }}</span>
<button id="theme-toggle" class="btn btn-sm btn-outline-light">◐ Tema</button><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-light">Keluar</button></form>
</div></div>
@endauth</div></nav>
<main class="container py-4 py-lg-5">
@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@yield('content')
</main>
<div id="toast-area" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Tema disimpan per browser; token CSRF dikirim bersama setiap request AJAX.
(()=>{const root=document.documentElement,key='campuscare-theme';root.setAttribute('data-bs-theme',localStorage.getItem(key)||'light');document.getElementById('theme-toggle')?.addEventListener('click',()=>{const mode=root.getAttribute('data-bs-theme')==='dark'?'light':'dark';root.setAttribute('data-bs-theme',mode);localStorage.setItem(key,mode)})})();
function showLoading(on){if(on&&!document.getElementById('loading-line'))document.body.insertAdjacentHTML('beforeend','<div id="loading-line" style="position:fixed;top:0;left:0;width:100%;height:4px;background:#2864dc;z-index:3000"></div>');if(!on)document.getElementById('loading-line')?.remove()}
function notify(message,type='success'){const id='toast-'+Date.now();document.getElementById('toast-area').insertAdjacentHTML('beforeend',`<div id="${id}" class="toast text-bg-${type} border-0"><div class="d-flex"><div class="toast-body"></div><button class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"></button></div></div>`);const el=document.getElementById(id);el.querySelector('.toast-body').textContent=message;new bootstrap.Toast(el).show();el.addEventListener('hidden.bs.toast',()=>el.remove())}
</script>@stack('scripts')
</body></html>
