import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import AppLayout from '../Layouts/AppLayout';
export default function Register() {
 const form=useForm({name:'',email:'',password:'',password_confirmation:''});
 return <AppLayout><div className="row justify-content-center py-lg-5"><div className="col-md-7 col-lg-5"><div className="card border-0 shadow-sm rounded-4 p-4 p-md-5"><h1 className="h3 fw-bold">Buat akun mahasiswa</h1><p className="text-secondary">Akun publik otomatis menggunakan role mahasiswa.</p><form onSubmit={e=>{e.preventDefault();form.post('/register')}}>{[['name','Nama lengkap','text'],['email','Email','email'],['password','Kata sandi','password'],['password_confirmation','Ulangi kata sandi','password']].map(([key,label,type])=><div className="mb-3" key={key}><label className="form-label">{label}</label><input className="form-control" type={type} value={form.data[key]} onChange={e=>form.setData(key,e.target.value)} required/><div className="text-danger small">{form.errors[key]}</div></div>)}<button className="btn btn-primary w-100" disabled={form.processing}>Daftar</button></form><p className="mt-3 mb-0">Sudah punya akun? <Link href="/login">Masuk</Link></p></div></div></div></AppLayout>;
}
