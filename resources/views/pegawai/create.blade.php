@extends('layouts.app')

@section('title', 'Input Manual - Talent Mapping')
@section('topbar-title', 'Input Data Pegawai')

@section('content')
<div class="row justify-content-center animate-in">
    <div class="col-md-8">
        <div class="card-custom p-3 p-md-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <a href="{{ route('pegawai.index') }}" class="btn btn-outline-custom btn-sm py-0 px-2"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <div class="card-section-title mb-0"><i class="bi bi-person-plus"></i>Input Data Pegawai</div>
                    <small style="font-size:.7rem;color:var(--slate-500)">Tambah data pegawai secara manual</small>
                </div>
            </div>

            <form action="{{ route('pegawai.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">NIP <span style="color:var(--rose-500)">*</span></label>
                        <input type="text" class="form-control form-control-sm @error('nip') is-invalid @enderror"
                               name="nip" value="{{ old('nip') }}" placeholder="Contoh: 196505041985031001" required>
                        @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control form-control-sm @error('nama') is-invalid @enderror"
                               name="nama" value="{{ old('nama') }}" placeholder="Nama pegawai (opsional)">
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- UE1: show dropdown for admin/super, show locked badge for operator --}}
                @if(auth()->user()->canAccessAllUnits())
                <div class="mt-3">
                    <label class="form-label">Unit Eselon 1 (UE1) <span style="color:var(--rose-500)">*</span></label>
                    <select class="form-select form-select-sm @error('ue1') is-invalid @enderror" name="ue1" required>
                        <option value="">-- Pilih Unit Eselon 1 --</option>
                        @foreach(\App\Models\Pegawai::UE1_LIST as $code => $name)
                            <option value="{{ $code }}" {{ old('ue1') == $code ? 'selected' : '' }}>
                                {{ $code }} - {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('ue1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                @else
                <div class="mt-3">
                    <label class="form-label">Unit Eselon 1</label>
                    <div class="d-flex align-items-center gap-2 py-1">
                        <span style="font-size:.78rem;padding:5px 14px;border-radius:var(--r-md);font-weight:600;background:var(--amber-100);color:var(--amber-600);border:1px solid #f0d89a">
                            <i class="bi bi-lock me-1"></i>{{ auth()->user()->ue1 }} — {{ auth()->user()->ue1_nama }}
                        </span>
                        <small style="color:var(--slate-400);font-size:.68rem">Otomatis sesuai unit Anda</small>
                    </div>
                </div>
                @endif

                <div class="row g-3 mt-0">
                    <div class="col-md-6">
                        <label class="form-label">Kategori Kinerja <span style="color:var(--rose-500)">*</span></label>
                        <select class="form-select form-select-sm @error('kategori_kinerja') is-invalid @enderror"
                                id="kategori_kinerja" name="kategori_kinerja" required>
                            <option value="">-- Pilih Kinerja --</option>
                            <option value="Di atas ekspektasi" {{ old('kategori_kinerja') == 'Di atas ekspektasi' ? 'selected' : '' }}>Di atas ekspektasi (Sangat Baik)</option>
                            <option value="Sesuai ekspektasi" {{ old('kategori_kinerja') == 'Sesuai ekspektasi' ? 'selected' : '' }}>Sesuai ekspektasi (Baik)</option>
                            <option value="Di bawah ekspektasi" {{ old('kategori_kinerja') == 'Di bawah ekspektasi' ? 'selected' : '' }}>Di bawah ekspektasi (Kurang)</option>
                        </select>
                        @error('kategori_kinerja')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kategori Potensial <span style="color:var(--rose-500)">*</span></label>
                        <select class="form-select form-select-sm @error('kategori_potensial') is-invalid @enderror"
                                id="kategori_potensial" name="kategori_potensial" required>
                            <option value="">-- Pilih Potensial --</option>
                            <option value="potensial rendah" {{ old('kategori_potensial') == 'potensial rendah' ? 'selected' : '' }}>Potensial Rendah</option>
                            <option value="potensial menengah" {{ old('kategori_potensial') == 'potensial menengah' ? 'selected' : '' }}>Potensial Menengah</option>
                            <option value="potensial tinggi" {{ old('kategori_potensial') == 'potensial tinggi' ? 'selected' : '' }}>Potensial Tinggi</option>
                        </select>
                        @error('kategori_potensial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Preview Box --}}
                <div id="boxPreview" class="card-custom p-3 mt-3" style="display:none;background:var(--slate-50)">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold" style="font-size:.7rem;color:var(--slate-500);text-transform:uppercase;letter-spacing:.04em">Hasil Klasifikasi:</span>
                        <span id="previewBoxBadge" class="badge-box" style="font-size:1.2rem;width:40px;height:40px"></span>
                        <span id="previewBoxLabel" class="fw-semibold" style="font-size:.82rem"></span>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary-custom btn-sm"><i class="bi bi-check-lg me-1"></i>Simpan</button>
                    <a href="{{ route('pegawai.index') }}" class="btn btn-outline-custom btn-sm">Batal</a>
                </div>
            </form>
        </div>

        {{-- Reference --}}
        <div class="row g-3 mt-1">
            <div class="{{ auth()->user()->canAccessAllUnits() ? 'col-md-6' : 'col-12' }}">
                <div class="card-custom p-3">
                    <div class="card-section-title"><i class="bi bi-grid-3x3"></i>Referensi 9-Box</div>
                    <table class="table table-sm ref-table mb-0">
                        <thead><tr><th>Box</th><th>Potensial</th><th>Kinerja</th></tr></thead>
                        <tbody>
                            @php $refs=[[1,'Rendah','Di bawah'],[2,'Rendah','Sesuai'],[3,'Menengah','Di bawah'],[4,'Rendah','Di atas'],[5,'Menengah','Sesuai'],[6,'Tinggi','Di bawah'],[7,'Menengah','Di atas'],[8,'Tinggi','Sesuai'],[9,'Tinggi','Di atas']]; @endphp
                            @foreach($refs as $r)
                            <tr><td><span class="badge-box badge-box-{{$r[0]}}">{{$r[0]}}</span></td><td>{{$r[1]}}</td><td>{{$r[2]}}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(auth()->user()->canAccessAllUnits())
            <div class="col-md-6">
                <div class="card-custom p-3">
                    <div class="card-section-title"><i class="bi bi-building"></i>Daftar UE1</div>
                    <div style="max-height:280px;overflow-y:auto">
                        <table class="table table-sm ref-table mb-0">
                            <thead class="sticky-top"><tr><th>Kode</th><th>Nama Unit</th></tr></thead>
                            <tbody>
                                @foreach(\App\Models\Pegawai::UE1_LIST as $code=>$name)
                                <tr><td class="fw-bold">{{$code}}</td><td style="font-size:.75rem">{{$name}}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const kS=document.getElementById('kategori_kinerja'),pS=document.getElementById('kategori_potensial'),bP=document.getElementById('boxPreview'),bB=document.getElementById('previewBoxBadge'),bL=document.getElementById('previewBoxLabel');
const bM={'potensial rendah':{'Di bawah ekspektasi':1,'Sesuai ekspektasi':2,'Di atas ekspektasi':4},'potensial menengah':{'Di bawah ekspektasi':3,'Sesuai ekspektasi':5,'Di atas ekspektasi':7},'potensial tinggi':{'Di bawah ekspektasi':6,'Sesuai ekspektasi':8,'Di atas ekspektasi':9}};
const bLabels={1:'Bawah & rendah',2:'Sesuai & rendah',3:'Bawah & menengah',4:'Atas & rendah',5:'Sesuai & menengah',6:'Bawah & tinggi',7:'Atas & menengah',8:'Sesuai & tinggi',9:'Atas & tinggi'};
function upd(){const k=kS.value,p=pS.value;if(k&&p&&bM[p]&&bM[p][k]){const b=bM[p][k];bP.style.display='block';bB.textContent=b;bB.className='badge-box badge-box-'+b;bB.style.cssText='font-size:1.2rem;width:40px;height:40px';bL.textContent='Box '+b+' — '+bLabels[b]}else{bP.style.display='none'}}
kS.addEventListener('change',upd);pS.addEventListener('change',upd);
</script>
@endpush