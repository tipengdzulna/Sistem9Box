@extends('layouts.app')
@section('title', 'Tambah User')
@section('topbar-title', 'Tambah Pengguna')
@section('content')
<div class="row justify-content-center animate-in"><div class="col-md-7">
    <div class="card-custom p-3 p-md-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('users.index') }}" class="btn btn-outline-custom btn-sm py-0 px-2"><i class="bi bi-arrow-left"></i></a>
            <div class="card-section-title mb-0"><i class="bi bi-person-plus"></i>Tambah User Baru</div>
        </div>
        <form action="{{ route('users.store') }}" method="POST">@csrf
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nama <span style="color:var(--rose-500)">*</span></label><input type="text" name="name" class="form-control form-control-sm @error('name')is-invalid @enderror" value="{{ old('name') }}" required>@error('name')<div class="invalid-feedback">{{$message}}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label">Username <span style="color:var(--rose-500)">*</span></label><input type="text" name="username" class="form-control form-control-sm @error('username')is-invalid @enderror" value="{{ old('username') }}" required>@error('username')<div class="invalid-feedback">{{$message}}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label">Password <span style="color:var(--rose-500)">*</span></label><input type="password" name="password" class="form-control form-control-sm @error('password')is-invalid @enderror" required>@error('password')<div class="invalid-feedback">{{$message}}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label">Konfirmasi Password <span style="color:var(--rose-500)">*</span></label><input type="password" name="password_confirmation" class="form-control form-control-sm" required></div>
                <div class="col-md-6"><label class="form-label">Role <span style="color:var(--rose-500)">*</span></label>
                    <select name="role" id="roleSelect" class="form-select form-select-sm @error('role')is-invalid @enderror" required>
                        <option value="">-- Pilih --</option>
                        <option value="super_admin" {{ old('role')==='super_admin'?'selected':'' }}>Super Admin</option>
                        <option value="admin" {{ old('role')==='admin'?'selected':'' }}>Admin</option>
                        <option value="operator" {{ old('role')==='operator'?'selected':'' }}>Operator</option>
                    </select>@error('role')<div class="invalid-feedback">{{$message}}</div>@enderror</div>
                <div class="col-md-6" id="ue1F" style="{{ old('role')==='operator'?'':'display:none' }}"><label class="form-label">Unit Eselon 1 <span style="color:var(--rose-500)">*</span></label>
                    <select name="ue1" class="form-select form-select-sm @error('ue1')is-invalid @enderror"><option value="">-- Pilih --</option>
                        @foreach(\App\Models\Pegawai::UE1_LIST as $c=>$n)<option value="{{$c}}" {{ old('ue1')==$c?'selected':'' }}>{{$c}} - {{$n}}</option>@endforeach
                    </select>@error('ue1')<div class="invalid-feedback">{{$message}}</div>@enderror</div>
            </div>
            <div class="mt-3 p-3" style="background:var(--slate-50);border-radius:var(--r-md);font-size:.72rem;color:var(--slate-600)">
                <strong style="color:var(--slate-700)"><i class="bi bi-info-circle me-1"></i>Keterangan:</strong><br>
                <strong>Super Admin</strong> — Akses penuh + kelola user &nbsp;|&nbsp; <strong>Admin</strong> — Input/hapus semua unit &nbsp;|&nbsp; <strong>Operator</strong> — Hanya unit sendiri
            </div>
            <div class="d-flex gap-2 mt-3"><button type="submit" class="btn btn-primary-custom btn-sm"><i class="bi bi-check-lg me-1"></i>Simpan</button><a href="{{ route('users.index') }}" class="btn btn-outline-custom btn-sm">Batal</a></div>
        </form>
    </div>
</div></div>
@endsection
@push('scripts')<script>document.getElementById('roleSelect').addEventListener('change',function(){document.getElementById('ue1F').style.display=this.value==='operator'?'':'none'})</script>@endpush