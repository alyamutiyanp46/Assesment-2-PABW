import { Link, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

type Props = { children: React.ReactNode };

// Layout latihan: satu navbar antarmodul, logout Inertia, tema lokal, dan feedback.
export default function CampusLayout({ children }: Props) {
    const { auth } = usePage<any>().props;
    const [dark, setDark] = useState(false);
    useEffect(() => { const saved = localStorage.getItem('campuscare-theme') === 'dark'; setDark(saved); document.documentElement.classList.toggle('dark', saved); }, []);
    function toggleTheme() { const next = !dark; setDark(next); document.documentElement.classList.toggle('dark', next); localStorage.setItem('campuscare-theme', next ? 'dark' : 'light'); }
    return <div className="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
        <nav className="border-b bg-white dark:border-slate-800 dark:bg-slate-900"><div className="mx-auto flex max-w-7xl flex-wrap items-center gap-4 px-4 py-3">
            <Link href="/dashboard" className="mr-auto flex items-center gap-2 text-lg font-bold"><span className="grid size-9 place-items-center rounded-xl bg-blue-600 text-white">C</span>CampusCare</Link>
            <Link className="text-sm hover:text-blue-600" href="/dashboard">Ringkasan</Link><Link className="text-sm hover:text-blue-600" href="/facilities">Fasilitas</Link><Link className="text-sm hover:text-blue-600" href="/complaints">Pengaduan</Link>
            <span className="hidden text-sm text-slate-500 sm:inline">{auth?.user?.name} · {auth?.user?.role?.label ?? auth?.user?.role}</span>
            <button onClick={toggleTheme} className="rounded-lg border px-3 py-2 text-sm">{dark ? '☀ Terang' : '◐ Gelap'}</button>
            <Link href="/logout" method="post" as="button" className="rounded-lg bg-slate-900 px-3 py-2 text-sm text-white dark:bg-white dark:text-slate-900">Keluar</Link>
        </div></nav><main className="mx-auto max-w-7xl px-4 py-6 md:py-9">{children}</main>
    </div>;
}
