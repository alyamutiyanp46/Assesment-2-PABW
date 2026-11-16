import React, { useEffect, useState } from 'react';
import { Link, usePage } from '@inertiajs/react';

export default function AppLayout({ children }) {
    const { auth, flash } = usePage().props;
    const [dark, setDark] = useState(() => localStorage.getItem('sipustaka-theme') === 'dark');
    useEffect(() => { document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light'); localStorage.setItem('sipustaka-theme', dark ? 'dark' : 'light'); }, [dark]);
    return <><nav className="navbar navbar-expand-lg navbar-dark" style={{ background: '#12243a' }}><div className="container"><Link className="navbar-brand fw-bold" href="/dashboard">📚 SIPustaka</Link><div className="navbar-nav ms-auto flex-row align-items-center gap-3">{auth.user ? <><Link className="nav-link" href="/dashboard">Ringkasan</Link><Link className="nav-link" href="/books">Katalog</Link><Link className="nav-link" href="/loans">Peminjaman</Link><span className="text-white-50 small">{auth.user.name} · {auth.user.role}</span><button className="btn btn-sm btn-outline-light" onClick={() => setDark(!dark)}>◐ Tema</button><form method="post" action="/logout"><input type="hidden" name="_token" value={document.querySelector('meta[name=csrf-token]')?.content} /><button className="btn btn-sm btn-light">Keluar</button></form></> : <><Link className="nav-link" href="/login">Masuk</Link><Link className="nav-link" href="/register">Daftar</Link></>}</div></div></nav><main className="container py-4 py-lg-5">{flash?.success && <div className="alert alert-success">{flash.success}</div>}{children}</main></>;
}
