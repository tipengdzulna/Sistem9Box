@extends('layouts.app')
@section('title', 'Edit Profil')
@section('topbar-title', 'Profil Saya')

@section('content')
<div class="row justify-content-center animate-in">
    <div class="col-md-7">

        {{-- Info Card --}}
        <div class="card-custom p-3 p-md-4 mb-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:48px;height:48px;border-radius:50%;background:var(--navy-700);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="bi bi-person-fill" style="font-size:1.3rem;color:#fff"></i>
                </div>
                <div>
                    <div style="font-size:1rem;font-weight:700;color:var(--slate-800)">{{ auth()->user()->name }}</div>
                    <div class="d-flex align-items-center gap-2 flex-wrap" style="font-size:.72rem">
                        @if(auth()->user()->isSuperAdmin())
                            <span style="padding:2px 8px;border-radius:6px;font-weight:600;background:var(--rose-100);color:var(--rose-600)">Super Admin</span>
                        @elseif(auth()->user()->isAdmin())
                            <span style="padding:2px 8px;border-radius:6px;font-weight:600;background:var(--navy-100);color:var(--navy-700)">Admin</span>
                        @else
                            <span style="padding:2px 8px;border-radius:6px;font-weight:600;background:var(--amber-100);color:var(--amber-600)">Operator</span>
                        @endif
                        @if(auth()->user()->isOperator())
                            <span class="badge-ue1">{{ auth()->user()->ue1 }} - {{ auth()->user()->ue1_nama }}</span>
                        @else
                            <span style="color:var(--slate-400)">Akses seluruh unit</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Form --}}
        <div class="card-custom p-3 p-md-4">
            <div class="card-section-title"><i class="bi bi-pencil-square"></i>Edit Profil</div>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap <span style="color:var(--rose-500)">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror"
                               value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username <span style="color:var(--rose-500)">*</span></label>
                        <input type="text" name="username" class="form-control form-control-sm @error('username') is-invalid @enderror"
                               value="{{ old('username', auth()->user()->username) }}" required>
                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Password Section --}}
                <div class="mt-4 pt-3" style="border-top:1px solid var(--slate-200)">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-shield-lock" style="color:var(--navy-500)"></i>
                        <span style="font-size:.78rem;font-weight:700;color:var(--slate-700)">Ubah Password</span>
                        <small style="color:var(--slate-400);font-size:.68rem">— kosongkan jika tidak ingin mengubah</small>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Password Lama</label>
                            <input type="password" name="current_password" class="form-control form-control-sm @error('current_password') is-invalid @enderror"
                                   placeholder="••••••">
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control form-control-sm @error('password') is-invalid @enderror"
                                   placeholder="Min. 6 karakter">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-sm"
                                   placeholder="Ulangi password">
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary-custom btn-sm"><i class="bi bi-check-lg me-1"></i>Simpan Perubahan</button>
                    <a href="{{ route('pegawai.index') }}" class="btn btn-outline-custom btn-sm">Kembali</a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection