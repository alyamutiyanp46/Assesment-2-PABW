import { Head } from '@inertiajs/react';
import { Pencil, Plus, Trash2, X } from 'lucide-react';
import { useCallback, useEffect, useState, type FormEvent } from 'react';
import type { ReactNode } from 'react';
import InputError from '@/components/input-error';
import { AlertMessage } from '@/components/alert-message';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { http, HttpError } from '@/lib/http';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

type Loan = { id: number; user_id: number; nama_peminjam: string; judul_buku: string; tanggal_pinjam: string; tanggal_kembali: string | null; status: 'Dipinjam'|'Dikembalikan'; file_path: string|null; file_url: string|null; user?: { name: string } };
type Data = { nama_peminjam: string; judul_buku: string; tanggal_pinjam: string; tanggal_kembali: string; status: string; file: File|null };
type Response = { data: Loan[]; message?: string };
const blank = (): Data => ({ nama_peminjam: '', judul_buku: '', tanggal_pinjam: new Date().toISOString().slice(0,10), tanggal_kembali: '', status: 'Dipinjam', file: null });
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Peminjaman', href: '/peminjaman' }];

export default function Index() {
    const [rows,setRows] = useState<Loan[]>([]); const [formOpen,setFormOpen] = useState(false); const [editing,setEditing] = useState<Loan|null>(null);
    const [data,setData] = useState<Data>(blank()); const [errors,setErrors] = useState<Record<string,string>>({}); const [message,setMessage] = useState(''); const [error,setError] = useState(''); const [busy,setBusy] = useState(false);
    const load = useCallback(async () => { try { const result = await http<Response>('/peminjaman/data'); setRows(result.data); } catch { setError('Data peminjaman gagal dimuat.'); } }, []);
    useEffect(() => { void load(); }, [load]);
    const openCreate = () => { setEditing(null); setData(blank()); setErrors({}); setFormOpen(true); };
    const openEdit = (row: Loan) => { setEditing(row); setData({ nama_peminjam:row.nama_peminjam, judul_buku:row.judul_buku, tanggal_pinjam:row.tanggal_pinjam, tanggal_kembali:row.tanggal_kembali??'', status:row.status, file:null }); setErrors({}); setFormOpen(true); };
    const save = async (event: FormEvent) => { event.preventDefault(); setBusy(true); setErrors({}); setError(''); const body = new FormData();
        Object.entries(data).forEach(([key,value]) => { if (value !== null) body.append(key,value); });
        try { const result = await http<Response>(editing ? `/peminjaman/${editing.id}` : '/peminjaman', { method:editing?'PUT':'POST', body }); setMessage(result.message ?? 'Berhasil disimpan.'); setFormOpen(false); await load(); }
        catch (caught) { if (caught instanceof HttpError && caught.status === 422) setErrors(Object.fromEntries(Object.entries(caught.data?.errors ?? {}).map(([key, value]) => [key, value[0] ?? 'Input tidak valid.']))); else setError(caught instanceof Error ? caught.message : 'Terjadi kesalahan.'); }
        finally { setBusy(false); }
    };
    const remove = async (row: Loan) => { if (!window.confirm(`Hapus peminjaman ${row.judul_buku}?`)) return; try { const result = await http<{message:string}>(`/peminjaman/${row.id}`,{method:'DELETE'}); setMessage(result.message); await load(); } catch (caught) { setError(caught instanceof Error ? caught.message : 'Gagal menghapus data.'); } };

    return <AppLayout breadcrumbs={breadcrumbs}><Head title="Peminjaman"/><div className="flex flex-1 flex-col gap-4 p-4">
        <div className="flex items-center justify-between"><div><h1 className="text-xl font-semibold">Data Peminjaman</h1><p className="text-sm text-muted-foreground">Tambah, ubah, dan hapus data tanpa meninggalkan halaman.</p></div><Button onClick={openCreate}><Plus/> Catat Peminjaman</Button></div>
        <AlertMessage type="success" message={message} onClose={() => setMessage('')}/><AlertMessage type="error" message={error} onClose={() => setError('')}/>
        <div className="overflow-x-auto rounded-xl border"><table className="w-full text-sm"><thead className="bg-muted/50 text-left"><tr>{['No','Peminjam','Judul Buku','Tgl Pinjam','Tgl Kembali','Status','Bukti','Aksi'].map(h=><th key={h} className="px-4 py-3 font-medium">{h}</th>)}</tr></thead><tbody>
            {rows.length===0 && <tr><td colSpan={8} className="px-4 py-8 text-center text-muted-foreground">Belum ada data peminjaman.</td></tr>}
            {rows.map((row,index)=><tr key={row.id} className="border-t"><td className="px-4 py-3">{index+1}</td><td className="px-4 py-3">{row.nama_peminjam}{row.user?.name ? <span className="block text-xs text-muted-foreground">Akun: {row.user.name}</span>:null}</td><td className="px-4 py-3">{row.judul_buku}</td><td className="px-4 py-3">{row.tanggal_pinjam}</td><td className="px-4 py-3">{row.tanggal_kembali??'-'}</td><td className="px-4 py-3">{row.status}</td><td className="px-4 py-3">{row.file_url?<a className="text-primary underline" href={row.file_url} target="_blank" rel="noreferrer">Buka bukti</a>:'-'}</td><td className="px-4 py-3"><div className="flex gap-2"><Button size="sm" variant="outline" onClick={()=>openEdit(row)}><Pencil/> Edit</Button><Button size="sm" variant="destructive" onClick={()=>void remove(row)}><Trash2/> Hapus</Button></div></td></tr>)}
        </tbody></table></div>
    </div>
    {formOpen && <div className="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/60 p-4" role="presentation"><section role="dialog" aria-modal="true" aria-labelledby="loan-title" className="relative my-auto w-full max-w-xl rounded-xl border bg-background p-6 shadow-xl"><button className="absolute right-4 top-4" onClick={()=>setFormOpen(false)} aria-label="Tutup"><X/></button><h2 id="loan-title" className="mb-5 text-lg font-semibold">{editing?'Ubah':'Catat'} Peminjaman</h2>
        <form onSubmit={save} className="space-y-4">
            <Field label="Nama Peminjam" name="nama_peminjam" error={errors.nama_peminjam}><Input id="nama_peminjam" value={data.nama_peminjam} onChange={e=>setData({...data,nama_peminjam:e.target.value})}/></Field>
            <Field label="Judul Buku" name="judul_buku" error={errors.judul_buku}><Input id="judul_buku" value={data.judul_buku} onChange={e=>setData({...data,judul_buku:e.target.value})}/></Field>
            <Field label="Tanggal Pinjam" name="tanggal_pinjam" error={errors.tanggal_pinjam}><Input id="tanggal_pinjam" type="date" value={data.tanggal_pinjam} onChange={e=>setData({...data,tanggal_pinjam:e.target.value})}/></Field>
            <Field label="Tanggal Kembali" name="tanggal_kembali" error={errors.tanggal_kembali}><Input id="tanggal_kembali" type="date" value={data.tanggal_kembali} onChange={e=>setData({...data,tanggal_kembali:e.target.value})}/></Field>
            <Field label="Status" name="status" error={errors.status}><select id="status" className="h-10 rounded-md border border-input bg-background px-3 text-sm" value={data.status} onChange={e=>setData({...data,status:e.target.value})}><option>Dipinjam</option><option>Dikembalikan</option></select></Field>
            <Field label="Bukti (JPG/PNG/PDF, maks. 2 MB)" name="file" error={errors.file}><Input id="file" type="file" accept=".jpg,.jpeg,.png,.pdf" onChange={e=>setData({...data,file:e.target.files?.[0]??null})}/>{editing?.file_url && <a className="text-xs text-primary underline" href={editing.file_url} target="_blank" rel="noreferrer">Buka bukti saat ini</a>}</Field>
            <div className="flex justify-end gap-2 pt-2"><Button type="button" variant="secondary" onClick={()=>setFormOpen(false)}>Batal</Button><Button disabled={busy}>{busy?'Menyimpan...':'Simpan'}</Button></div>
        </form></section></div>}
    </AppLayout>;
}

function Field({label,name,error,children}:{label:string;name:string;error?:string;children:ReactNode}) { return <div className="grid gap-2"><Label htmlFor={name}>{label} *</Label>{children}<InputError message={error}/></div>; }
