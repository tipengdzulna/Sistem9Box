@extends('layouts.app')
@section('title', 'Edit User')
@section('topbar-title', 'Edit Pengguna')
@section('content')
<div class="row justify-content-center animate-in"><div class="col-md-7">
    <div class="card-custom p-3 p-md-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('users.index') }}" class="btn btn-outline-custom btn-sm py-0 px-2"><i class="bi bi-arrow-left"></i></a>
            <div class="card-section-title mb-0"><i class="bi bi-pencil-square"></i>Edit: {{ $user->name }}</div>
        </div>
        <form action="{{ route('users.update', $user) }}" method="POST">@csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nama <span style="color:var(--rose-500)">*</span></label><input type="text" name="name" class="form-control form-control-sm @error('name')is-invalid @enderror" value="{{ old('name',$user->name) }}" required>@error('name')<div class="invalid-feedback">{{$message}}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label">Username <span style="color:var(--rose-500)">*</span></label><input type="text" name="username" class="form-control form-control-sm @error('username')is-invalid @enderror" value="{{ old('username',$user->username) }}" required>@error('username')<div class="invalid-feedback">{{$message}}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label">Password Baru <small style="color:var(--slate-400)">(kosongkan jika tidak ubah)</small></label><input type="password" name="password" class="form-control form-control-sm @error('password')is-invalid @enderror">@error('password')<div class="invalid-feedback">{{$message}}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label">Konfirmasi Password</label><input type="password" name="password_confirmation" class="form-control form-control-sm"></div>
                <div class="col-md-6"><label class="form-label">Role <span style="color:var(--rose-500)">*</span></label>
                    <select name="role" id="roleSelect" class="form-select form-select-sm @error('role')is-invalid @enderror" required>
                        <option value="super_admin" {{ old('role',$user->role)==='super_admin'?'selected':'' }}>Super Admin</option>
                        <option value="admin" {{ old('role',$user->role)==='admin'?'selected':'' }}>Admin</option>
                        <option value="operator" {{ old('role',$user->role)==='operator'?'selected':'' }}>Operator</option>
                    </select>@error('role')<div class="invalid-feedback">{{$message}}</div>@enderror</div>
                <div class="col-md-6" id="ue1F" style="{{ old('role',$user->role)==='operator'?'':'display:none' }}"><label class="form-label">Unit Eselon 1 <span style="color:var(--rose-500)">*</span></label>
                    <select name="ue1" class="form-select form-select-sm @error('ue1')is-invalid @enderror"><option value="">-- Pilih --</option>
                        @foreach(\App\Models\Pegawai::UE1_LIST as $c=>$n)<option value="{{$c}}" {{ old('ue1',$user->ue1)==$c?'selected':'' }}>{{$c}} - {{$n}}</option>@endforeach
                    </select>@error('ue1')<div class="invalid-feedback">{{$message}}</div>@enderror</div>
            </div>
            <div class="d-flex gap-2 mt-3"><button type="submit" class="btn btn-primary-custom btn-sm"><i class="bi bi-check-lg me-1"></i>Perbarui</button><a href="{{ route('users.index') }}" class="btn btn-outline-custom btn-sm">Batal</a></div>
        </form>
    </div>
</div></div>
@endsection
@push('scripts')<script>document.getElementById('roleSelect').addEventListener('change',function(){document.getElementById('ue1F').style.display=this.value==='operator'?'':'none'})</script>@endpush