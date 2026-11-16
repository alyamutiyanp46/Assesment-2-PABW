import { Head } from '@inertiajs/react';
import { BookOpen, Check, Pencil, Plus, Search, Trash2, X } from 'lucide-react';
import { useState, type FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

type Book = {
    id: number;
    kode_buku: string;
    judul: string;
    penulis: string;
    kategori: string;
    stok: number;
};

type BookForm = Omit<Book, 'id' | 'stok'> & { stok: number | string };
type Props = { books: Book[]; bookCount: number; isAdmin: boolean; canAdd: boolean; filters: { search: string } };

const emptyForm: BookForm = { kode_buku: '', judul: '', penulis: '', kategori: '', stok: 0 };
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Data Buku', href: '/buku' }];

export default function Index({ books: initialBooks, bookCount: initialCount, isAdmin, canAdd }: Props) {
    const [books, setBooks] = useState(initialBooks);
    const [bookCount, setBookCount] = useState(initialCount);
    const [search, setSearch] = useState('');
    const [form, setForm] = useState<BookForm>(emptyForm);
    const [editing, setEditing] = useState<Book | null>(null);
    const [formOpen, setFormOpen] = useState(false);
    const [errors, setErrors] = useState<Record<string, string[]>>({});
    const [notice, setNotice] = useState<{ message: string; type: 'success' | 'error' } | null>(null);
    const [processing, setProcessing] = useState(false);

    const request = async (url: string, method = 'GET', body?: BookForm) => {
        const response = await fetch(url, {
            method,
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '',
            },
            body: body ? JSON.stringify(body) : undefined,
        });
        const data = await response.json();
        return { response, data };
    };

    const loadBooks = async (query = search) => {
        const { response, data } = await request(`/buku/data?search=${encodeURIComponent(query)}`);
        if (!response.ok) throw new Error(data.message ?? 'Data buku gagal dimuat.');
        setBooks(data.books);
        setBookCount(data.total);
    };

    const openCreate = () => {
        setEditing(null);
        setForm(emptyForm);
        setErrors({});
        setNotice(null);
        setFormOpen(true);
    };

    const openEdit = (book: Book) => {
        setEditing(book);
        setForm({ kode_buku: book.kode_buku, judul: book.judul, penulis: book.penulis, kategori: book.kategori, stok: book.stok });
        setErrors({});
        setNotice(null);
        setFormOpen(true);
    };

    const submitSearch = async (event: FormEvent) => {
        event.preventDefault();
        try {
            await loadBooks(search);
        } catch (error) {
            setNotice({ message: error instanceof Error ? error.message : 'Data buku gagal dimuat.', type: 'error' });
        }
    };

    const submitForm = async (event: FormEvent) => {
        event.preventDefault();
        setProcessing(true);
        setErrors({});
        setNotice(null);

        try {
            const url = editing ? `/buku/${editing.id}` : '/buku';
            const { response, data } = await request(url, editing ? 'PUT' : 'POST', form);

            if (response.status === 422) {
                setErrors(data.errors ?? {});
                return;
            }

            if (!response.ok) throw new Error(data.message ?? 'Buku gagal disimpan.');

            await loadBooks();
            setNotice({ message: data.message, type: 'success' });
            setFormOpen(false);
            setEditing(null);
            setForm(emptyForm);
        } catch (error) {
            setNotice({ message: error instanceof Error ? error.message : 'Buku gagal disimpan.', type: 'error' });
        } finally {
            setProcessing(false);
        }
    };

    const deleteBook = async (book: Book) => {
        if (!window.confirm(`Hapus buku ${book.judul}?`)) return;

        try {
            const { response, data } = await request(`/buku/${book.id}`, 'DELETE');
            if (!response.ok) throw new Error(data.message ?? 'Buku gagal dihapus.');
            await loadBooks();
            setNotice({ message: data.message, type: 'success' });
        } catch (error) {
            setNotice({ message: error instanceof Error ? error.message : 'Buku gagal dihapus.', type: 'error' });
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Data Buku" />
            <div className="library-page min-h-full flex-1 p-4 sm:p-7">
                <div className="mx-auto max-w-7xl space-y-6">
                    <div className="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <p className="text-sm font-semibold uppercase tracking-[0.18em] text-emerald-800">Perpustakaan Kampus</p>
                            <h1 className="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Data Buku</h1>
                            <p className="mt-2 text-sm text-slate-600">Kelola koleksi dan pantau ketersediaan buku.</p>
                        </div>
                        <div className="flex items-center gap-3 rounded-xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm">
                            <span className="grid size-10 place-items-center rounded-lg bg-emerald-50 text-emerald-800"><BookOpen className="size-5" /></span>
                            <div><p className="text-2xl font-semibold leading-none text-slate-900">{bookCount}</p><p className="mt-1 text-xs text-slate-500">Buku terdaftar</p></div>
                        </div>
                    </div>

                    {notice && <div role="status" className={`flex items-center gap-2 rounded-lg px-4 py-3 text-sm ${notice.type === 'success' ? 'bg-emerald-50 text-emerald-800' : 'bg-rose-50 text-rose-800'}`}><Check className="size-4" />{notice.message}</div>}

                    <div className="library-card overflow-hidden">
                        <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-4 sm:p-5">
                            <form onSubmit={submitSearch} className="flex w-full max-w-lg gap-2">
                                <div className="relative min-w-0 flex-1"><Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" /><input className="library-field w-full pl-9" value={search} onChange={event => setSearch(event.target.value)} placeholder="Cari kode, judul, penulis, kategori" /></div>
                                <button className="library-action rounded-[0.65rem] bg-slate-800 px-4 text-sm font-medium text-white">Cari</button>
                            </form>
                            {canAdd && <button type="button" onClick={openCreate} className="library-action inline-flex items-center gap-2 rounded-[0.65rem] bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white"><Plus className="size-4" />Tambah Buku</button>}
                        </div>

                        <div className="overflow-x-auto">
                            <table className="library-table w-full min-w-[760px] text-left text-sm">
                                <thead className="bg-slate-50/80"><tr><th className="px-5 py-3">No.</th><th className="px-5 py-3">Kode Buku</th><th className="px-5 py-3">Judul</th><th className="px-5 py-3">Penulis</th><th className="px-5 py-3">Kategori</th><th className="px-5 py-3">Stok</th>{isAdmin && <th className="px-5 py-3">Aksi</th>}</tr></thead>
                                <tbody className="divide-y divide-slate-100">
                                    {books.length === 0 && <tr><td colSpan={isAdmin ? 7 : 6} className="px-5 py-14 text-center text-slate-500">Belum ada buku yang cocok dengan pencarian.</td></tr>}
                                    {books.map((book, index) => <tr key={book.id}>
                                        <td className="px-5 py-4 text-slate-500">{index + 1}</td><td className="px-5 py-4 font-medium text-slate-700">{book.kode_buku}</td><td className="max-w-xs px-5 py-4 font-medium text-slate-900">{book.judul}</td><td className="px-5 py-4 text-slate-600">{book.penulis}</td><td className="px-5 py-4"><span className="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">{book.kategori}</span></td><td className="px-5 py-4 text-slate-700">{book.stok}</td>
                                        {isAdmin && <td className="px-5 py-4"><div className="flex gap-2"><button type="button" onClick={() => openEdit(book)} aria-label={`Edit ${book.judul}`} className="library-action rounded-md bg-amber-100 p-2 text-amber-800"><Pencil className="size-4" /></button><button type="button" onClick={() => deleteBook(book)} aria-label={`Hapus ${book.judul}`} className="library-action rounded-md bg-rose-100 p-2 text-rose-700"><Trash2 className="size-4" /></button></div></td>}
                                    </tr>)}
                                </tbody>
                            </table>
                        </div>
                        <div className="border-t border-slate-100 px-5 py-3 text-xs text-slate-500">Menampilkan {books.length} dari {bookCount} data buku</div>
                    </div>

                    {formOpen && <div className="fixed inset-0 z-50 grid place-items-center overflow-y-auto bg-slate-950/40 p-4" role="presentation" onMouseDown={event => event.target === event.currentTarget && setFormOpen(false)}>
                        <section role="dialog" aria-modal="true" aria-labelledby="book-form-title" className="library-card w-full max-w-xl p-5 sm:p-7">
                            <div className="mb-5 flex items-start justify-between"><div><h2 id="book-form-title" className="text-xl font-semibold text-slate-900">{editing ? 'Ubah Data Buku' : 'Tambah Buku'}</h2><p className="mt-1 text-sm text-slate-500">Lengkapi informasi koleksi perpustakaan.</p></div><button type="button" onClick={() => setFormOpen(false)} className="rounded-md p-2 text-slate-500 hover:bg-slate-100" aria-label="Tutup"><X className="size-5" /></button></div>
                            <form onSubmit={submitForm} className="space-y-4">
                                {([['kode_buku', 'Kode Buku'], ['judul', 'Judul'], ['penulis', 'Penulis'], ['kategori', 'Kategori'], ['stok', 'Stok']] as const).map(([field, label]) => <label key={field} className="grid gap-1.5 text-sm font-medium text-slate-700">{label}<input className="library-field font-normal" type={field === 'stok' ? 'number' : 'text'} min={field === 'stok' ? 0 : undefined} maxLength={field === 'kode_buku' ? 20 : field === 'judul' ? 150 : field === 'penulis' ? 100 : field === 'kategori' ? 50 : undefined} value={form[field]} onChange={event => setForm({ ...form, [field]: event.target.value })} />{errors[field] && <span className="text-xs font-normal text-rose-700">{errors[field][0]}</span>}</label>)}
                                <div className="flex justify-end gap-2 pt-2"><button type="button" onClick={() => setFormOpen(false)} className="library-action rounded-[0.65rem] border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700">Batal</button><button disabled={processing} className="library-action rounded-[0.65rem] bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white disabled:opacity-60">{processing ? 'Menyimpan...' : editing ? 'Simpan Perubahan' : 'Simpan Buku'}</button></div>
                            </form>
                        </section>
                    </div>}
                </div>
            </div>
        </AppLayout>
    );
}
