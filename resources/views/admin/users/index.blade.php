@extends('layouts.app')
@section('title', 'Kelola User')
@section('topbar-title', 'Kelola Pengguna')

@section('content')
<div class="animate-in">
    <div class="card-custom p-3 p-md-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
            <div class="card-section-title mb-0"><i class="bi bi-people"></i>Daftar Pengguna</div>
            <a href="{{ route('users.create') }}" class="btn btn-primary-custom btn-sm"><i class="bi bi-person-plus me-1"></i>Tambah User</a>
        </div>
        <div class="table-responsive" style="border-radius:var(--r-md);border:1px solid var(--slate-200);overflow:hidden">
            <table class="table table-custom table-hover">
                <thead><tr><th>#</th><th>Nama</th><th>Username</th><th>Role</th><th>UE1</th><th style="width:100px"></th></tr></thead>
                <tbody>
                @forelse($users as $i=>$u)
                <tr>
                    <td style="color:var(--slate-400);font-size:.72rem">{{ $users->firstItem()+$i }}</td>
                    <td style="font-weight:600">{{ $u->name }}</td>
                    <td><code class="mono" style="font-size:.75rem">{{ $u->username }}</code></td>
                    <td>
                        @if($u->isSuperAdmin())<span style="font-size:.68rem;padding:3px 9px;border-radius:6px;font-weight:600;background:var(--rose-100);color:var(--rose-600)">Super Admin</span>
                        @elseif($u->isAdmin())<span style="font-size:.68rem;padding:3px 9px;border-radius:6px;font-weight:600;background:var(--navy-100);color:var(--navy-700)">Admin</span>
                        @else<span style="font-size:.68rem;padding:3px 9px;border-radius:6px;font-weight:600;background:var(--amber-100);color:var(--amber-600)">Operator</span>@endif
                    </td>
                    <td>@if($u->ue1)<span class="badge-ue1">{{ $u->ue1 }} - {{ $u->ue1_short }}</span>@else<span style="color:var(--slate-400);font-size:.75rem">Semua</span>@endif</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('users.edit', $u) }}" class="btn btn-sm py-0 px-1" style="color:var(--navy-500)" title="Edit"><i class="bi bi-pencil-square"></i></a>
                            @if($u->id !== auth()->id())
                            <form action="{{ route('users.destroy', $u) }}" method="POST" onsubmit="return confirm('Hapus user {{ $u->name }}?')">@csrf @method('DELETE')
                                <button class="btn btn-sm py-0 px-1" style="color:var(--rose-500)" title="Hapus"><i class="bi bi-trash3"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4" style="color:var(--slate-400)">Belum ada user</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small style="color:var(--slate-400);font-size:.7rem">Hal {{ $users->currentPage() }} / {{ $users->lastPage() }}</small>
            {{ $users->links('pegawai.pagination') }}
        </div>
        @endif
    </div>
</div>
@endsection