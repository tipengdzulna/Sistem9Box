@extends('layouts.app')
@section('title', 'Dashboard - Pemetaan Talenta')
@section('topbar-title', $gridUe1 ? (\App\Models\Pegawai::UE1_SHORT[$gridUe1] ?? '') . ' — Pemetaan Talenta' : 'Pemetaan Talenta')

@section('content')
@php
    $user = auth()->user();
    $canAll = $user->canAccessAllUnits();
    $isOp = $user->isOperator();
    $topCount = ($boxCounts[9]??0)+($boxCounts[8]??0)+($boxCounts[7]??0);
    $midCount = ($boxCounts[5]??0)+($boxCounts[4]??0)+($boxCounts[2]??0);
    $lowCount = ($boxCounts[1]??0)+($boxCounts[3]??0)+($boxCounts[6]??0);
@endphp

<div class="animate-in" id="dashboardContent">

    {{-- HEADER ROW: Title + Actions --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
        <div>
            <h5 style="font-size:.95rem;font-weight:800;color:var(--slate-800);margin:0">
                @if($isOp)
                    {{ \App\Models\Pegawai::UE1_LIST[$user->ue1] ?? '' }}
                @elseif($gridUe1)
                    {{ \App\Models\Pegawai::UE1_LIST[$gridUe1] ?? '' }}
                @else
                    Seluruh Unit Kementerian Keuangan
                @endif
            </h5>
            <small style="font-size:.7rem;color:var(--slate-500)">Pemetaan Talenta &middot; {{ number_format($gridTotal) }} Pegawai</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if($canAll)
            <form method="GET" action="{{ route('pegawai.index') }}" class="d-flex align-items-center gap-1">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                @if(request('box'))<input type="hidden" name="box" value="{{ request('box') }}">@endif
                @if(request('ue1'))<input type="hidden" name="ue1" value="{{ request('ue1') }}">@endif
                <select name="grid_ue1" class="form-select form-select-sm" style="width:185px;font-size:.72rem" onchange="this.form.submit()">
                    <option value="">Semua Unit</option>
                    @foreach(\App\Models\Pegawai::UE1_LIST as $code => $name)
                        <option value="{{ $code }}" {{ $gridUe1 == $code ? 'selected' : '' }}>{{ $code }} — {{ \App\Models\Pegawai::UE1_SHORT[$code] }}</option>
                    @endforeach
                </select>
                @if($gridUe1)
                    <a href="{{ route('pegawai.index', array_filter(request()->except('grid_ue1'))) }}" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:.72rem" title="Reset"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
            @endif
            <button onclick="downloadPDF()" class="btn btn-primary-custom btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i>Download PDF</button>
        </div>
    </div>

    {{-- PDF WRAPPER --}}
    <div id="pdfArea">

    {{-- SUMMARY CARDS --}}
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="summary-card card-custom sc-blue">
                <i class="bi bi-people-fill s-icon"></i>
                <div class="s-number">{{ number_format($gridTotal) }}</div>
                <div class="s-label">Total Pegawai</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card card-custom sc-green"><i class="bi bi-arrow-up-circle-fill s-icon"></i>
                <div class="s-number">{{ number_format($topCount) }}</div>
                <div class="s-label">Di Atas / Tinggi</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card card-custom sc-amber"><i class="bi bi-dash-circle-fill s-icon"></i>
                <div class="s-number">{{ number_format($midCount) }}</div>
                <div class="s-label">Sesuai / Menengah</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card card-custom sc-rose"><i class="bi bi-arrow-down-circle-fill s-icon"></i>
                <div class="s-number">{{ number_format($lowCount) }}</div>
                <div class="s-label">Perlu Perbaikan</div>
            </div>
        </div>
    </div>

    {{-- 9-BOX GRID + CHARTS --}}
    <div class="row g-2 g-md-3 mb-3">
        {{-- 9-Box Grid --}}
        <div class="col-12 col-lg-7">
            <div class="card-custom p-3 h-100">
                <div class="card-section-title"><i class="bi bi-grid-3x3-gap-fill"></i>Pemetaan 9-Box Grid</div>
                <div class="nine-box-grid">
                    <div class="grid-corner"></div>
                    <div class="grid-header-col">Rendah</div><div class="grid-header-col">Menengah</div><div class="grid-header-col">Tinggi</div>

                    <div class="grid-header-row">Di Atas Ekspektasi</div>
                    <div class="grid-cell cell-green"><div class="box-number">{{ number_format($boxCounts[4]??0) }}</div><div class="box-count">4</div><div class="box-label">Atas & rendah</div></div>
                    <div class="grid-cell cell-blue"><div class="box-number">{{ number_format($boxCounts[7]??0) }}</div><div class="box-count">7</div><div class="box-label">Atas & menengah</div></div>
                    <div class="grid-cell cell-star"><div class="box-number">{{ number_format($boxCounts[9]??0) }}</div><div class="box-count">9</div><div class="box-label">Atas & tinggi</div></div>

                    <div class="grid-header-row">Sesuai Ekspektasi</div>
                    <div class="grid-cell cell-amber"><div class="box-number">{{ number_format($boxCounts[2]??0) }}</div><div class="box-count">2</div><div class="box-label">Sesuai & rendah</div></div>
                    <div class="grid-cell cell-green"><div class="box-number">{{ number_format($boxCounts[5]??0) }}</div><div class="box-count">5</div><div class="box-label">Sesuai & menengah</div></div>
                    <div class="grid-cell cell-blue"><div class="box-number">{{ number_format($boxCounts[8]??0) }}</div><div class="box-count">8</div><div class="box-label">Sesuai & tinggi</div></div>

                    <div class="grid-header-row">Di Bawah Ekspektasi</div>
                    <div class="grid-cell cell-danger"><div class="box-number">{{ number_format($boxCounts[1]??0) }}</div><div class="box-count">1</div><div class="box-label">Bawah & rendah</div></div>
                    <div class="grid-cell cell-amber"><div class="box-number">{{ number_format($boxCounts[3]??0) }}</div><div class="box-count">3</div><div class="box-label">Bawah & menengah</div></div>
                    <div class="grid-cell cell-amber"><div class="box-number">{{ number_format($boxCounts[6]??0) }}</div><div class="box-count">6</div><div class="box-label">Bawah & tinggi</div></div>

                    <div class="grid-corner"></div>
                    <div class="grid-axis-label" style="grid-column:2/5">POTENSIAL →</div>
                </div>
                <div class="text-center mt-1"><small style="font-weight:600;font-size:.55rem;color:var(--slate-400)">↕ KINERJA &nbsp;&middot;&nbsp; ↔ POTENSIAL</small></div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="col-12 col-lg-5">
            <div class="row g-2 g-md-3 h-100">
                {{-- Donut: Distribusi Kinerja --}}
                <div class="col-6 col-lg-12">
                    <div class="card-custom p-3 h-100">
                        <div class="card-section-title"><i class="bi bi-pie-chart"></i>Distribusi Kinerja</div>
                        <div style="max-width:220px;margin:0 auto;position:relative">
                            <canvas id="chartKinerja"></canvas>
                        </div>
                    </div>
                </div>
                {{-- Bar: Per Box --}}
                <div class="col-6 col-lg-12">
                    <div class="card-custom p-3 h-100">
                        <div class="card-section-title"><i class="bi bi-bar-chart"></i>Distribusi Per Box</div>
                        <div style="max-height:180px"><canvas id="chartBox"></canvas></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- UE1 BAR CHART (admin/super only) --}}
    @if($canAll)
    <div class="card-custom p-3 mb-3">
        <div class="card-section-title"><i class="bi bi-building"></i>Distribusi Per Unit Eselon 1</div>
        <div style="max-height:280px"><canvas id="chartUe1"></canvas></div>
    </div>
    @endif

    </div>{{-- end #pdfArea --}}

    {{-- REFERENCE CARDS (collapsible on mobile) --}}
    <div class="mb-3">
        <button class="btn btn-sm w-100 d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#refCards" style="background:var(--slate-100);color:var(--slate-600);font-size:.72rem;font-weight:600;border-radius:var(--r-md);padding:8px">
            <i class="bi bi-chevron-down me-1"></i>Lihat Tabel Referensi
        </button>
        <div class="collapse d-md-block mt-2 mt-md-0" id="refCards">
            <div class="row g-2 g-md-3">
                <div class="col-12 col-md-4">
                    <div class="card-custom p-3 h-100">
                        <div class="card-section-title"><i class="bi bi-bookmark-check"></i>Kategori Kinerja</div>
                        <table class="table table-sm ref-table mb-0"><thead><tr><th>Kategori</th><th>Dasar</th></tr></thead><tbody>
                            <tr><td><span class="badge-kinerja badge-kinerja-atas">Di atas ekspektasi</span></td><td>Sangat Baik</td></tr>
                            <tr><td><span class="badge-kinerja badge-kinerja-sesuai">Sesuai ekspektasi</span></td><td>Baik</td></tr>
                            <tr><td><span class="badge-kinerja badge-kinerja-bawah">Di bawah ekspektasi</span></td><td>Butuh Perbaikan, Kurang, Sangat Kurang</td></tr>
                        </tbody></table>
                    </div>
                </div>
                <div class="col-6 col-md-{{ $canAll ? '3' : '8' }}">
                    <div class="card-custom p-3 h-100">
                        <div class="card-section-title"><i class="bi bi-grid-3x3"></i>Per Box</div>
                        <table class="table table-sm ref-table mb-0"><thead><tr><th>Box</th><th>Jml</th></tr></thead><tbody>
                            @for($i=1;$i<=9;$i++)<tr><td><span class="badge-box badge-box-{{$i}}">{{$i}}</span></td><td>{{ number_format($boxCounts[$i]??0) }}</td></tr>@endfor
                            <tr style="border-top:2px solid var(--slate-200)"><td class="fw-bold">Total</td><td class="fw-bold">{{ number_format($gridTotal) }}</td></tr>
                        </tbody></table>
                    </div>
                </div>
                @if($canAll)
                <div class="col-6 col-md-5">
                    <div class="card-custom p-3 h-100">
                        <div class="card-section-title"><i class="bi bi-building"></i>Per UE1</div>
                        <div style="max-height:310px;overflow-y:auto">
                            <table class="table table-sm ref-table mb-0"><thead class="sticky-top"><tr><th>#</th><th>Unit</th><th>Jml</th></tr></thead><tbody>
                                @foreach(\App\Models\Pegawai::UE1_LIST as $code=>$name)
                                <tr><td class="fw-bold" style="font-size:.7rem">{{$code}}</td><td><span class="badge-ue1" title="{{$name}}">{{ \App\Models\Pegawai::UE1_SHORT[$code] }}</span></td><td>{{ number_format($ue1Counts[$code]??0) }}</td></tr>
                                @endforeach
                            </tbody></table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- DATA TABLE --}}
    <div class="card-custom p-3">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
            <div class="card-section-title mb-0"><i class="bi bi-table"></i>Data Pegawai</div>
            @if($totalPegawai > 0 && $canAll)
            <form action="{{ route('pegawai.destroyAll') }}" method="POST" onsubmit="return confirm('PERHATIAN!\n\nAnda akan menghapus SELURUH data pegawai di semua unit.\n\nYakin ingin melanjutkan?')">@csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm" style="font-size:.72rem"><i class="bi bi-trash3 me-1"></i>Hapus Semua</button>
            </form>
            @elseif($totalPegawai > 0 && $isOp)
            <form action="{{ route('pegawai.destroyAllUnit') }}" method="POST" onsubmit="return confirm('PERHATIAN!\n\nAnda akan menghapus seluruh data pegawai unit {{ $user->ue1_short }}.\nTotal: {{ number_format($totalPegawai) }} data.\n\nYakin ingin melanjutkan?')">@csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm" style="font-size:.72rem"><i class="bi bi-trash3 me-1"></i>Hapus Semua {{ $user->ue1_short }}</button>
            </form>
            @endif
        </div>

        <form method="GET" action="{{ route('pegawai.index') }}" class="filter-bar mb-3">
            @if($gridUe1 && $canAll)<input type="hidden" name="grid_ue1" value="{{ $gridUe1 }}">@endif
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm">
                    <label class="form-label">Cari NIP / Nama</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Ketik..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-sm-auto" style="min-width:100px">
                    <label class="form-label">Box</label>
                    <select name="box" class="form-select form-select-sm"><option value="">Semua</option>
                        @for($i=1;$i<=9;$i++)<option value="{{$i}}" {{ request('box')==$i?'selected':'' }}>Box {{$i}}</option>@endfor
                    </select>
                </div>
                @if($canAll)
                <div class="col-6 col-sm-auto" style="min-width:140px">
                    <label class="form-label">Unit Eselon 1</label>
                    <select name="ue1" class="form-select form-select-sm"><option value="">Semua</option>
                        @foreach(\App\Models\Pegawai::UE1_SHORT as $code=>$short)<option value="{{$code}}" {{ request('ue1')==$code?'selected':'' }}>{{$code}} - {{$short}}</option>@endforeach
                    </select>
                </div>
                @endif
                <div class="col-12 col-sm-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
                    <a href="{{ route('pegawai.index', ($gridUe1 && $canAll) ? ['grid_ue1'=>$gridUe1] : []) }}" class="btn btn-outline-custom btn-sm"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
                </div>
            </div>
        </form>

        @if($pegawai->count() > 0)
        <div class="d-flex justify-content-between align-items-center mb-2">
            <small style="color:var(--slate-500);font-size:.72rem;font-weight:500">{{ $pegawai->firstItem() }}–{{ $pegawai->lastItem() }} dari {{ number_format($pegawai->total()) }}</small>
        </div>

        {{-- Desktop table --}}
        <div class="d-none d-md-block">
            <div class="table-responsive" style="border-radius:var(--r-md);border:1px solid var(--slate-200);overflow:hidden">
                <table class="table table-custom table-hover">
                    <thead><tr><th style="width:42px">#</th><th>NIP</th><th>Nama</th><th>UE1</th><th>Kinerja</th><th>Potensial</th><th style="width:48px">Box</th><th style="width:48px"></th></tr></thead>
                    <tbody>
                    @foreach($pegawai as $i=>$p)
                    <tr>
                        <td style="color:var(--slate-400);font-size:.72rem">{{ $pegawai->firstItem()+$i }}</td>
                        <td><code class="mono" style="font-size:.72rem;color:var(--slate-700)">{{ $p->nip }}</code></td>
                        <td style="font-weight:500">{{ $p->nama ?? '-' }}</td>
                        <td><span class="badge-ue1" title="{{ $p->ue1_nama }}">{{ $p->ue1_short }}</span></td>
                        <td>
                            @if($p->kategori_kinerja==='Di atas ekspektasi')<span class="badge-kinerja badge-kinerja-atas">Di atas</span>
                            @elseif($p->kategori_kinerja==='Sesuai ekspektasi')<span class="badge-kinerja badge-kinerja-sesuai">Sesuai</span>
                            @else<span class="badge-kinerja badge-kinerja-bawah">Di bawah</span>@endif
                        </td>
                        <td>
                            @if($p->kategori_potensial==='potensial tinggi')<span class="badge-potensial badge-potensial-tinggi">Tinggi</span>
                            @elseif($p->kategori_potensial==='potensial menengah')<span class="badge-potensial badge-potensial-menengah">Menengah</span>
                            @else<span class="badge-potensial badge-potensial-rendah">Rendah</span>@endif
                        </td>
                        <td><span class="badge-box badge-box-{{ $p->box }}">{{ $p->box }}</span></td>
                        <td>
                            <form action="{{ route('pegawai.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
                                <button class="btn btn-sm py-0 px-1" style="color:var(--slate-400);font-size:.85rem" title="Hapus"><i class="bi bi-trash3"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile cards --}}
        <div class="d-md-none d-flex flex-column gap-2">
            @foreach($pegawai as $i=>$p)
            @php
                $borderColor = match(true) {
                    in_array($p->box, [7,8,9]) => 'var(--emerald-500)',
                    in_array($p->box, [4,5,6]) => 'var(--navy-500)',
                    default => 'var(--rose-500)',
                };
            @endphp
            <div class="card-custom p-3" style="border-left:3px solid {{ $borderColor }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div style="min-width:0;flex:1">
                        <div style="font-size:.82rem;font-weight:600;color:var(--slate-800)">{{ $p->nama ?? '-' }}</div>
                        <code class="mono" style="font-size:.68rem;color:var(--slate-500)">{{ $p->nip }}</code>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <span class="badge-box badge-box-{{ $p->box }}" style="width:26px;height:26px;font-size:.72rem">{{ $p->box }}</span>
                        <form action="{{ route('pegawai.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus data {{ $p->nama ?? $p->nip }}?')">@csrf @method('DELETE')
                            <button class="btn btn-sm py-0 px-1" style="color:var(--slate-400);font-size:.8rem"><i class="bi bi-trash3"></i></button>
                        </form>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-1" style="font-size:.68rem">
                    <span class="badge-ue1">{{ $p->ue1_short }}</span>
                    @if($p->kategori_kinerja==='Di atas ekspektasi')<span class="badge-kinerja badge-kinerja-atas">Di atas</span>
                    @elseif($p->kategori_kinerja==='Sesuai ekspektasi')<span class="badge-kinerja badge-kinerja-sesuai">Sesuai</span>
                    @else<span class="badge-kinerja badge-kinerja-bawah">Di bawah</span>@endif
                    @if($p->kategori_potensial==='potensial tinggi')<span class="badge-potensial badge-potensial-tinggi">Tinggi</span>
                    @elseif($p->kategori_potensial==='potensial menengah')<span class="badge-potensial badge-potensial-menengah">Menengah</span>
                    @else<span class="badge-potensial badge-potensial-rendah">Rendah</span>@endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mt-3">
            <small style="color:var(--slate-400);font-size:.7rem;font-weight:500">Halaman {{ $pegawai->currentPage() }} / {{ $pegawai->lastPage() }}</small>
            {{ $pegawai->links('pegawai.pagination') }}
        </div>
        @else
        <div class="text-center py-5">
            <div style="width:56px;height:56px;border-radius:50%;background:var(--slate-100);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px"><i class="bi bi-inbox" style="font-size:1.5rem;color:var(--slate-400)"></i></div>
            @if(request()->hasAny(['search','box','ue1']))
                <p style="color:var(--slate-500);font-size:.85rem;margin-bottom:12px">Tidak ada data sesuai filter</p>
                <a href="{{ route('pegawai.index') }}" class="btn btn-outline-custom btn-sm"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
            @else
                <p style="color:var(--slate-500);font-size:.85rem;margin-bottom:12px">Belum ada data pegawai</p>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a href="{{ route('pegawai.create') }}" class="btn btn-primary-custom btn-sm"><i class="bi bi-person-plus me-1"></i>Input Manual</a>
                    <a href="{{ route('pegawai.import.form') }}" class="btn btn-outline-custom btn-sm"><i class="bi bi-file-earmark-arrow-up me-1"></i>Import Excel</a>
                </div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
{{-- jsPDF for PDF (no html2canvas needed) --}}
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.2/dist/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.4/dist/jspdf.plugin.autotable.min.js"></script>

<script>
// ─── CHART DATA ───
const boxData = [@for($i=1;$i<=9;$i++){{ $boxCounts[$i]??0 }},@endfor];
const topC = {{ $topCount }}, midC = {{ $midCount }}, lowC = {{ $lowCount }};
const totalC = {{ $gridTotal ?: 1 }};

// Colors
const navy='#1a3d73', emerald='#15885f', amber='#c48820', rose='#da3a47';
const boxColors = ['#da3a47','#e07730','#c48820','#4aac6b','#2968b8','#8562c6','#1f5199','#15885f','#0f6e4e'];

// ─── DONUT: Distribusi Kinerja ───
const ctxK = document.getElementById('chartKinerja');
if(ctxK){
    new Chart(ctxK, {
        type: 'doughnut',
        data: {
            labels: ['Di Atas Ekspektasi','Sesuai Ekspektasi','Di Bawah Ekspektasi'],
            datasets: [{
                data: [topC, midC, lowC],
                backgroundColor: [emerald, navy, rose],
                borderWidth: 2, borderColor: '#fff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: true,
            cutout: '62%',
            plugins: {
                legend: { position:'bottom', labels:{ font:{size:10,family:'DM Sans',weight:'600'}, padding:8, usePointStyle:true, pointStyleWidth:8 }},
                tooltip: { callbacks: { label: ctx => ctx.label+': '+ctx.parsed+' ('+Math.round(ctx.parsed/totalC*100)+'%)' }}
            }
        }
    });
}

// ─── BAR: Per Box ───
const ctxB = document.getElementById('chartBox');
if(ctxB){
    new Chart(ctxB, {
        type: 'bar',
        data: {
            labels: ['Box 1','Box 2','Box 3','Box 4','Box 5','Box 6','Box 7','Box 8','Box 9'],
            datasets: [{
                data: boxData,
                backgroundColor: boxColors,
                borderRadius: 4, borderSkipped: false,
                barPercentage: .7
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend:{display:false}, tooltip:{ callbacks:{ label: ctx => ctx.parsed.x+' pegawai' }}},
            scales: {
                x: { grid:{display:false}, ticks:{font:{size:9,family:'DM Sans'}} },
                y: { grid:{display:false}, ticks:{font:{size:9,family:'DM Sans',weight:'600'}} }
            }
        }
    });
}

// ─── BAR: Per UE1 ───
@if($canAll)
const ctxU = document.getElementById('chartUe1');
if(ctxU){
    const ue1Labels = [@foreach(\App\Models\Pegawai::UE1_SHORT as $c=>$s)'{{ $s }}',@endforeach];
    const ue1Data = [@foreach(\App\Models\Pegawai::UE1_SHORT as $c=>$s){{ $ue1Counts[$c]??0 }},@endforeach];
    new Chart(ctxU, {
        type: 'bar',
        data: {
            labels: ue1Labels,
            datasets: [{
                data: ue1Data,
                backgroundColor: navy+'cc',
                hoverBackgroundColor: navy,
                borderRadius: 4, barPercentage: .65
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend:{display:false}, tooltip:{ callbacks:{ label: ctx => ctx.parsed.y+' pegawai' }}},
            scales: {
                x: { grid:{display:false}, ticks:{font:{size:8,family:'DM Sans',weight:'600'},maxRotation:45,minRotation:45} },
                y: { grid:{color:'#e2e8f020'}, ticks:{font:{size:9,family:'DM Sans'},stepSize:Math.max(1,Math.ceil(Math.max(...ue1Data)/5))}, beginAtZero:true }
            }
        }
    });
}
@endif

// ─── PDF DOWNLOAD (native jsPDF) ───
async function downloadPDF(){
    const btn = event.target.closest('button');
    const origHTML = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Memproses...';
    btn.disabled = true;

    try {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({ orientation:'landscape', unit:'mm', format:'a4' });
        const pW = pdf.internal.pageSize.getWidth();  // 297
        const pH = pdf.internal.pageSize.getHeight(); // 210
        const m = 12; // margin
        const cW = pW - m*2; // content width
        let y = m;

        // ───── COLORS ─────
        const cNavy = [26,61,115], cGold = [184,134,11], cWhite = [255,255,255];
        const cEmerald = [21,136,95], cAmber = [196,136,32], cRose = [218,58,71];
        const cSlate = [100,116,139], cSlateLt = [226,232,240], cBg = [248,250,252];

        // ───── UNIT INFO ─────
        const unitName = @json($isOp ? \App\Models\Pegawai::UE1_LIST[$user->ue1] ?? '' : ($gridUe1 ? \App\Models\Pegawai::UE1_LIST[$gridUe1] ?? '' : ''));
        const unitShort = @json($isOp ? \App\Models\Pegawai::UE1_SHORT[$user->ue1] ?? '' : ($gridUe1 ? \App\Models\Pegawai::UE1_SHORT[$gridUe1] ?? '' : ''));
        const isAllUnit = !unitName;
        const scopeText = isAllUnit ? 'Seluruh Unit Kementerian Keuangan' : unitName;

        // ═══════════════════════════════════════════
        // HEADER BAR
        // ═══════════════════════════════════════════
        pdf.setFillColor(...cNavy);
        pdf.rect(0, 0, pW, 28, 'F');

        // Gold accent line
        pdf.setFillColor(...cGold);
        pdf.rect(0, 28, pW, 1.2, 'F');

        // Title
        pdf.setTextColor(...cWhite);
        pdf.setFontSize(16);
        pdf.setFont(undefined, 'bold');
        pdf.text('LAPORAN PEMETAAN TALENTA', m, 11);

        pdf.setFontSize(9);
        pdf.setFont(undefined, 'normal');
        pdf.text('Kementerian Keuangan Republik Indonesia', m, 17);

        // Unit scope badge (right side)
        pdf.setFontSize(8.5);
        pdf.setFont(undefined, 'bold');
        const scopeLabel = isAllUnit ? 'SELURUH UNIT' : unitShort;
        const scopeW = pdf.getTextWidth(scopeLabel) + 10;
        pdf.setFillColor(255,255,255,40);
        pdf.roundedRect(pW - m - scopeW, 6, scopeW, 7, 2, 2, 'F');
        pdf.setTextColor(255,255,255);
        pdf.text(scopeLabel, pW - m - scopeW + 5, 11);

        // Date (right side, below badge)
        pdf.setFontSize(7.5);
        pdf.setFont(undefined, 'normal');
        pdf.setTextColor(180,200,230);
        pdf.text('{{ now()->translatedFormat("d F Y") }}', pW - m, 21, {align:'right'});

        y = 33;

        // ═══════════════════════════════════════════
        // SCOPE INFO BAR
        // ═══════════════════════════════════════════
        pdf.setFillColor(...cBg);
        pdf.roundedRect(m, y, cW, 10, 2, 2, 'F');
        pdf.setDrawColor(...cSlateLt);
        pdf.roundedRect(m, y, cW, 10, 2, 2, 'S');
        pdf.setFontSize(8);
        pdf.setFont(undefined, 'bold');
        pdf.setTextColor(...cNavy);
        pdf.text('Cakupan: ' + scopeText, m+4, y+6.5);
        pdf.setFont(undefined, 'normal');
        pdf.setTextColor(...cSlate);
        pdf.text('Total: ' + totalC.toLocaleString('id-ID') + ' Pegawai', pW-m-4, y+6.5, {align:'right'});

        y += 15;

        // ═══════════════════════════════════════════
        // SUMMARY BOXES
        // ═══════════════════════════════════════════
        const summaryData = [
            { label:'TOTAL PEGAWAI', value:totalC, color:cNavy, bg:[220,233,247] },
            { label:'DI ATAS / TINGGI', value:topC, color:cEmerald, bg:[209,245,228] },
            { label:'SESUAI / MENENGAH', value:midC, color:cAmber, bg:[254,245,216] },
            { label:'PERLU PERBAIKAN', value:lowC, color:cRose, bg:[253,226,228] },
        ];
        const boxW = (cW - 9) / 4; // 4 boxes with 3mm gaps

        summaryData.forEach((s, i) => {
            const bx = m + i*(boxW+3);
            pdf.setFillColor(...s.bg);
            pdf.roundedRect(bx, y, boxW, 18, 2, 2, 'F');
            // Accent bar top
            pdf.setFillColor(...s.color);
            pdf.rect(bx, y, boxW, 2, 'F');
            // Round top corners
            pdf.setFillColor(...s.bg);
            pdf.roundedRect(bx, y, boxW, 4, 2, 2, 'F');
            pdf.setFillColor(...s.color);
            pdf.rect(bx+1, y, boxW-2, 1.5, 'F');

            // Value
            pdf.setFontSize(16);
            pdf.setFont(undefined, 'bold');
            pdf.setTextColor(...s.color);
            pdf.text(s.value.toLocaleString('id-ID'), bx + boxW/2, y+11, {align:'center'});

            // Label
            pdf.setFontSize(5.5);
            pdf.setFont(undefined, 'bold');
            pdf.setTextColor(...cSlate);
            pdf.text(s.label, bx + boxW/2, y+15.5, {align:'center'});
        });

        y += 23;

        // ═══════════════════════════════════════════
        // 9-BOX GRID (drawn natively)
        // ═══════════════════════════════════════════
        const gridW = cW * 0.52;
        const gridH = 68;
        const gridX = m;
        const gridY = y;

        // Section title
        pdf.setFontSize(9);
        pdf.setFont(undefined, 'bold');
        pdf.setTextColor(...cNavy);
        pdf.text('Pemetaan 9-Box Grid', gridX, gridY);
        const gy = gridY + 5;

        const cellW = gridW / 3;
        const cellH = gridH / 3;
        const labelW = 18; // left labels width
        const headerH = 7; // top headers height

        // Grid cell definitions [row][col] = { box, count, colors }
        const gridCells = [
            [{b:4,c:boxData[3],bg:[237,252,244],fg:cEmerald},{b:7,c:boxData[6],bg:[220,233,247],fg:cNavy},{b:9,c:boxData[8],bg:[220,245,235],fg:cEmerald}],
            [{b:2,c:boxData[1],bg:[255,251,235],fg:cAmber},{b:5,c:boxData[4],bg:[237,252,244],fg:cEmerald},{b:8,c:boxData[7],bg:[220,233,247],fg:cNavy}],
            [{b:1,c:boxData[0],bg:[254,242,242],fg:cRose},{b:3,c:boxData[2],bg:[255,251,235],fg:cAmber},{b:6,c:boxData[5],bg:[255,251,235],fg:cAmber}],
        ];
        const rowLabels = ['Di Atas Ekspektasi','Sesuai Ekspektasi','Di Bawah Ekspektasi'];
        const colLabels = ['Rendah','Menengah','Tinggi'];

        const actualCellW = (gridW - labelW) / 3;
        const actualCellH = (gridH - headerH) / 3;

        // Column headers
        colLabels.forEach((l,ci) => {
            const cx = gridX + labelW + ci*actualCellW + actualCellW/2;
            pdf.setFontSize(6); pdf.setFont(undefined,'bold'); pdf.setTextColor(...cNavy);
            pdf.text(l.toUpperCase(), cx, gy+5, {align:'center'});
        });

        // Draw cells
        gridCells.forEach((row, ri) => {
            const cy = gy + headerH + ri*actualCellH;
            // Row label
            pdf.setFontSize(5); pdf.setFont(undefined,'bold'); pdf.setTextColor(...cNavy);
            const rlX = gridX + labelW/2;
            const rlY = cy + actualCellH/2;
            // Save, rotate, draw, restore
            pdf.saveGraphicsState();
            pdf.text(rowLabels[ri], gridX+1, cy + actualCellH/2 + pdf.getTextWidth(rowLabels[ri])/2, null, 90);
            pdf.restoreGraphicsState();

            row.forEach((cell, ci) => {
                const cx = gridX + labelW + ci*actualCellW;
                // Fill
                pdf.setFillColor(...cell.bg);
                pdf.rect(cx, cy, actualCellW, actualCellH, 'F');
                // Border
                pdf.setDrawColor(...cSlateLt);
                pdf.setLineWidth(0.3);
                pdf.rect(cx, cy, actualCellW, actualCellH, 'S');
                // Count (big number)
                pdf.setFontSize(14); pdf.setFont(undefined,'bold');
                pdf.setTextColor(...cell.fg);
                pdf.text(cell.c.toString(), cx+actualCellW/2, cy+actualCellH/2-1, {align:'center'});
                // Box label
                pdf.setFontSize(6); pdf.setFont(undefined,'normal');
                pdf.setTextColor(...cSlate);
                pdf.text('Box '+cell.b, cx+actualCellW/2, cy+actualCellH/2+5, {align:'center'});
            });
        });

        // Axis labels
        pdf.setFontSize(5.5); pdf.setFont(undefined,'bold'); pdf.setTextColor(...cSlate);
        pdf.text('KINERJA ↕', gridX+3, gy+headerH+gridH/2-headerH/2+18, null, 90);
        pdf.text('POTENSIAL →', gridX+labelW+(gridW-labelW)/2, gy+headerH+actualCellH*3+5, {align:'center'});

        // ═══════════════════════════════════════════
        // CHARTS (right side, from canvas)
        // ═══════════════════════════════════════════
        const chartX = m + gridW + 8;
        const chartW = cW - gridW - 8;

        // Kinerja donut chart
        pdf.setFontSize(9); pdf.setFont(undefined,'bold'); pdf.setTextColor(...cNavy);
        pdf.text('Distribusi Kinerja', chartX, gridY);

        const kinerjaCanvas = document.getElementById('chartKinerja');
        if(kinerjaCanvas){
            const kImg = kinerjaCanvas.toDataURL('image/png');
            pdf.addImage(kImg, 'PNG', chartX, gridY+3, chartW, chartW*0.65);
        }

        // Box bar chart
        const boxChartY = gridY + 3 + chartW*0.65 + 4;
        pdf.setFontSize(9); pdf.setFont(undefined,'bold'); pdf.setTextColor(...cNavy);
        pdf.text('Distribusi Per Box', chartX, boxChartY);

        const boxCanvas = document.getElementById('chartBox');
        if(boxCanvas){
            const bImg = boxCanvas.toDataURL('image/png');
            const bH = Math.min(38, pH - boxChartY - m - 15);
            pdf.addImage(bImg, 'PNG', chartX, boxChartY+3, chartW, bH);
        }

        // ═══════════════════════════════════════════
        // PAGE 2: UE1 Chart + Data Table
        // ═══════════════════════════════════════════
        @if($canAll)
        pdf.addPage();
        y = m;

        // Mini header
        pdf.setFillColor(...cNavy);
        pdf.rect(0, 0, pW, 14, 'F');
        pdf.setFillColor(...cGold);
        pdf.rect(0, 14, pW, 0.8, 'F');
        pdf.setTextColor(...cWhite);
        pdf.setFontSize(10); pdf.setFont(undefined,'bold');
        pdf.text('LAPORAN PEMETAAN TALENTA — ' + scopeText.toUpperCase(), m, 9);
        pdf.setFontSize(7); pdf.setFont(undefined,'normal');
        pdf.setTextColor(180,200,230);
        pdf.text('{{ now()->translatedFormat("d F Y") }}', pW-m, 9, {align:'right'});
        y = 20;

        // UE1 chart
        pdf.setFontSize(9); pdf.setFont(undefined,'bold'); pdf.setTextColor(...cNavy);
        pdf.text('Distribusi Per Unit Eselon 1', m, y);
        const ue1Canvas = document.getElementById('chartUe1');
        if(ue1Canvas){
            const uImg = ue1Canvas.toDataURL('image/png');
            pdf.addImage(uImg, 'PNG', m, y+3, cW, 55);
        }
        y += 62;

        // UE1 Table
        pdf.setFontSize(9); pdf.setFont(undefined,'bold'); pdf.setTextColor(...cNavy);
        pdf.text('Rincian Per Unit Eselon 1', m, y);
        y += 4;

        const ue1TableData = [
            @foreach(\App\Models\Pegawai::UE1_LIST as $code=>$name)
            ['{{ $code }}', '{{ \App\Models\Pegawai::UE1_SHORT[$code] }}', '{{ $name }}', '{{ number_format($ue1Counts[$code]??0) }}'],
            @endforeach
        ];

        pdf.autoTable({
            startY: y,
            margin: { left: m, right: m },
            head: [['Kode','Singkatan','Nama Unit','Jumlah']],
            body: ue1TableData,
            styles: { fontSize:7, font:'helvetica', cellPadding:2 },
            headStyles: { fillColor:cNavy, textColor:cWhite, fontStyle:'bold', fontSize:7 },
            alternateRowStyles: { fillColor:[245,248,252] },
            columnStyles: { 0:{cellWidth:12,halign:'center'}, 1:{cellWidth:25}, 3:{cellWidth:18,halign:'center',fontStyle:'bold'} },
        });
        @endif

        // ═══════════════════════════════════════════
        // BOX DETAIL TABLE (next page)
        // ═══════════════════════════════════════════
        pdf.addPage();
        y = m;

        // Mini header
        pdf.setFillColor(...cNavy);
        pdf.rect(0, 0, pW, 14, 'F');
        pdf.setFillColor(...cGold);
        pdf.rect(0, 14, pW, 0.8, 'F');
        pdf.setTextColor(...cWhite);
        pdf.setFontSize(10); pdf.setFont(undefined,'bold');
        pdf.text('RINCIAN PER BOX — ' + scopeText.toUpperCase(), m, 9);
        pdf.setFontSize(7); pdf.setFont(undefined,'normal');
        pdf.setTextColor(180,200,230);
        pdf.text('{{ now()->translatedFormat("d F Y") }}', pW-m, 9, {align:'right'});
        y = 20;

        const boxLabelsArr = [
            'Kinerja Di Bawah & Potensial Rendah',
            'Kinerja Sesuai & Potensial Rendah',
            'Kinerja Di Bawah & Potensial Menengah',
            'Kinerja Di Atas & Potensial Rendah',
            'Kinerja Sesuai & Potensial Menengah',
            'Kinerja Di Bawah & Potensial Tinggi',
            'Kinerja Di Atas & Potensial Menengah',
            'Kinerja Sesuai & Potensial Tinggi',
            'Kinerja Di Atas & Potensial Tinggi',
        ];

        const boxTableData = boxData.map((cnt,i) => {
            const pct = totalC > 0 ? (cnt/totalC*100).toFixed(1)+'%' : '0%';
            return [(i+1).toString(), boxLabelsArr[i], cnt.toLocaleString('id-ID'), pct];
        });
        boxTableData.push(['','TOTAL', totalC.toLocaleString('id-ID'), '100%']);

        pdf.autoTable({
            startY: y,
            margin: { left: m, right: m },
            head: [['Box','Keterangan','Jumlah','Persentase']],
            body: boxTableData,
            styles: { fontSize:8, font:'helvetica', cellPadding:2.5 },
            headStyles: { fillColor:cNavy, textColor:cWhite, fontStyle:'bold' },
            alternateRowStyles: { fillColor:[245,248,252] },
            columnStyles: { 0:{cellWidth:14,halign:'center',fontStyle:'bold'}, 2:{cellWidth:22,halign:'center'}, 3:{cellWidth:22,halign:'center'} },
            didParseCell: function(data) {
                // Bold total row
                if(data.row.index === boxTableData.length-1) {
                    data.cell.styles.fontStyle = 'bold';
                    data.cell.styles.fillColor = [226,232,240];
                }
            }
        });

        // ═══════════════════════════════════════════
        // FOOTER on all pages
        // ═══════════════════════════════════════════
        const totalPages = pdf.internal.getNumberOfPages();
        for(let p=1; p<=totalPages; p++){
            pdf.setPage(p);
            // Footer line
            pdf.setDrawColor(...cSlateLt);
            pdf.setLineWidth(0.3);
            pdf.line(m, pH-9, pW-m, pH-9);
            // Left text
            pdf.setFontSize(6.5);
            pdf.setFont(undefined,'normal');
            pdf.setTextColor(...cSlate);
            pdf.text('Laporan Pemetaan Talenta — Kementerian Keuangan RI', m, pH-5);
            // Right text
            pdf.text('Halaman '+p+' dari '+totalPages, pW-m, pH-5, {align:'right'});
            // Center: scope
            pdf.setFont(undefined,'bold');
            pdf.text(scopeText, pW/2, pH-5, {align:'center'});
        }

        // ═══════════════════════════════════════════
        // SAVE
        // ═══════════════════════════════════════════
        const fileName = isAllUnit
            ? 'Laporan_Talent_Mapping_Seluruh_Unit_{{ now()->format("Y-m-d") }}.pdf'
            : 'Laporan_Talent_Mapping_'+unitShort.replace(/\s+/g,'_')+'_{{ now()->format("Y-m-d") }}.pdf';
        pdf.save(fileName);

    } catch(e) {
        console.error('PDF Error:', e);
        alert('Gagal generate PDF. Silakan coba lagi.');
    } finally {
        btn.innerHTML = origHTML;
        btn.disabled = false;
    }
}
</script>
@endpush