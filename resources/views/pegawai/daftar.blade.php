{{-- resources/views/pegawai/daftar.blade.php --}}
@extends('layouts.app')
@section('title', 'Daftar Pegawai')
@section('topbar-title', 'Daftar Pegawai')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root{
    --ink:#0d1b2a;--pruss:#0f2744;--pruss-mid:#1a3d6e;--pruss-lt:#2a5298;
    --gold:#c9961a;--gold-bg:#fdf6e3;
    --fog:#f4f6fb;--surface:#ffffff;
    --border:rgba(15,39,68,.10);--border-md:rgba(15,39,68,.18);
    --muted:#6b7a99;--subtle:#9aaac2;
    --r-sm:10px;--r-md:16px;--r-lg:20px;
}
*,*::before,*::after{box-sizing:border-box}
body{font-family:'Sora',system-ui,sans-serif;font-size:15px;color:var(--ink);background:var(--fog)}

.daftar-layout{
    display:grid;
    grid-template-columns:300px 1fr;
    gap:24px;
    max-width:1600px;
    margin:0 auto;
}

.filter-sidebar{
    position:sticky;
    top:80px;
    height:fit-content;
    background:var(--surface);
    border-radius:var(--r-lg);
    border:1px solid var(--border-md);
    overflow:hidden;
}
.filter-head{
    padding:16px 18px;
    background:var(--pruss);
    display:flex;align-items:center;gap:10px;
}
.filter-head-icon{
    width:34px;height:34px;border-radius:9px;
    background:rgba(255,255,255,.15);
    display:inline-flex;align-items:center;justify-content:center;
    font-size:16px;color:white;flex-shrink:0;
}
.filter-head-title{font-size:13px;font-weight:700;color:white;line-height:1.2}
.filter-head-sub{font-size:10px;color:rgba(255,255,255,.6);line-height:1}
.filter-section{padding:14px 16px;border-bottom:1px solid var(--border)}
.filter-section:last-child{border-bottom:none}
.filter-label{font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--subtle);margin-bottom:8px}
.filter-sub-label{font-size:11px;font-weight:600;color:var(--subtle);margin-bottom:4px;margin-top:8px}
.filter-sub-label:first-child{margin-top:0}
.filter-select{
    width:100%;font-family:'Sora',sans-serif;font-size:12px;
    padding:8px 10px;border:1px solid var(--border-md);
    border-radius:var(--r-sm);background:var(--fog);
    color:var(--muted);cursor:pointer;transition:all .2s;
    margin-bottom:4px;
}
.filter-select:focus{outline:none;border-color:var(--pruss-mid);background:white}
.filter-select.active{border-color:var(--pruss-mid);background:#E6F1FB;color:var(--pruss-mid);font-weight:700}
.filter-reset{
    width:100%;display:flex;align-items:center;justify-content:center;
    gap:6px;padding:8px;border-radius:var(--r-sm);font-size:12px;
    font-weight:700;color:var(--muted);border:1px solid var(--border-md);
    background:transparent;cursor:pointer;font-family:'Sora',sans-serif;
    transition:all .2s;margin-top:2px;
}
.filter-reset:hover{border-color:#dc2626;color:#dc2626;background:#FCEBEB}
.filter-reset.has-filter{border-color:var(--pruss-mid);color:var(--pruss-mid);background:#E6F1FB}
.filter-stats{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.stat-card{background:var(--fog);border-radius:var(--r-sm);border:1px solid var(--border);padding:10px 10px 8px;text-align:center}
.stat-value{font-size:18px;font-weight:800;letter-spacing:-.02em;line-height:1;margin-bottom:3px}
.stat-label{font-size:9px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:var(--subtle)}
.stat-blue{color:#185FA5}.stat-teal{color:#0F6E56}.stat-amber{color:#854F0B}.stat-red{color:#A32D2D}

.main-head{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;margin-bottom:18px;flex-wrap:wrap}
.main-title{font-size:22px;font-weight:800;color:var(--pruss);letter-spacing:-.02em;margin-bottom:3px}
.main-sub{font-size:13px;color:var(--muted)}

.search-row{
    display:flex;gap:10px;align-items:center;
    background:var(--surface);border-radius:var(--r-lg);border:1px solid var(--border-md);
    padding:14px 16px;margin-bottom:16px;flex-wrap:wrap;
}
.search-wrap{position:relative;flex:2;min-width:200px}
.search-ico{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--subtle);font-size:14px;pointer-events:none}
.search-inp{
    font-family:'Sora',sans-serif;font-size:13px;padding:9px 36px 9px 38px;
    border:1px solid var(--border-md);border-radius:var(--r-md);
    background:var(--fog);color:var(--ink);width:100%;outline:none;transition:all .2s;
}
.search-inp:focus{border-color:var(--pruss-mid);box-shadow:0 0 0 4px rgba(26,61,110,.08);background:white}
.search-inp::placeholder{color:var(--subtle)}
@keyframes spin{to{transform:translateY(-50%) rotate(360deg)}}
@keyframes fadeIn{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:none}}
.tbl-fade{animation:fadeIn .2s ease both}
.loading{opacity:.4;pointer-events:none;transition:opacity .15s}

.tbl-card{background:var(--surface);border-radius:var(--r-lg);border:1px solid var(--border-md);overflow:hidden}
.tbl-card-hdr{padding:14px 18px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);gap:10px;flex-wrap:wrap}
.tbl-title-sm{font-size:14px;font-weight:700;color:var(--ink)}
.count-info{font-size:12px;color:var(--muted)}

.chip-bar{display:flex;gap:6px;flex-wrap:wrap;align-items:center;padding:0 18px 12px}
.chip{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:40px;font-size:11px;font-weight:700;border:1px solid var(--border-md);background:var(--fog);color:var(--muted)}
.chip-close{cursor:pointer;opacity:.5;margin-left:2px;font-size:12px}
.chip-close:hover{opacity:1}

.main-tbl{width:100%;border-collapse:collapse;font-size:13px}
.main-tbl thead th{
    padding:10px 14px 8px;font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;
    color:var(--muted);border-bottom:1px solid var(--border-md);
    background:var(--fog);text-align:left;vertical-align:middle;white-space:nowrap;
}
/* Kolom grup warna */
.th-kinerja{background:#E6F1FB!important}
.th-potensial{background:#E1F5EE!important}
.th-box{background:#f4f6fb!important}
.td-potensial{background:#f7fdf9}
.main-tbl tbody td{padding:11px 14px;border-bottom:1px solid var(--border);vertical-align:middle}
.main-tbl tbody tr:hover td{background:#f7f9fe}
.main-tbl tbody tr:hover td.td-potensial{background:#edfaf3}
.pname{font-weight:700;font-size:13px;margin-bottom:1px}
.pnip{font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--subtle)}
.pjab{font-size:11px;color:var(--muted);margin-top:1px}
.row-n{font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--border-md)}
.del-btn{background:none;border:none;padding:5px 7px;border-radius:var(--r-sm);color:var(--border-md);cursor:pointer;font-size:.9rem;transition:all .2s}
.del-btn:hover{color:#A32D2D;background:#FCEBEB}
.nkp-val{font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:700;display:block;margin-bottom:1px}
.nkp-kat{font-size:10px;font-weight:600;opacity:.75}
.nilai-total{font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:800;display:block}
.nilai-total-sub{font-size:10px;font-weight:600;opacity:.7;margin-top:1px;display:block}
.box-b{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;font-family:'Sora',sans-serif;font-weight:800;font-size:13px;border:1px solid transparent}
.bb1,.bb2,.bb3,.bb4,.bb5,.bb6,.bb7,.bb8,.bb9{background:#dbeafe;color:#1d4ed8;border-color:#93c5fd}
.km-b{display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:30px;padding:0 7px;border-radius:9px;font-weight:700;font-size:13px;background:#FAEEDA;color:#854F0B;border:1px solid #FAC775}
.bdg{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:40px;font-size:11px;font-weight:600;white-space:nowrap;border:1px solid transparent}
.b-teal{background:#E1F5EE;color:#0F6E56;border-color:#9FE1CB}
.b-blue{background:#E6F1FB;color:#185FA5;border-color:#B5D4F4}
.b-red{background:#FCEBEB;color:#A32D2D;border-color:#F7C1C1}
.b-amber{background:#FAEEDA;color:#854F0B;border-color:#FAC775}
.b-gray{background:var(--fog);color:var(--muted);border-color:var(--border-md)}

/* Detail popover untuk breakdown nilai */
.detail-btn{
    display:inline-flex;align-items:center;justify-content:center;
    width:20px;height:20px;border-radius:50%;
    background:var(--fog);border:1px solid var(--border-md);
    color:var(--subtle);font-size:10px;cursor:pointer;
    transition:all .2s;margin-left:4px;vertical-align:middle;
    font-family:'Sora',sans-serif;font-weight:700;
}
.detail-btn:hover{background:#E6F1FB;border-color:var(--pruss-mid);color:var(--pruss-mid)}

.m-card{background:var(--surface);border-radius:var(--r-lg);border:1px solid var(--border-md);border-left-width:4px;padding:13px 15px 11px;transition:all .2s;margin-bottom:10px}
.m-card:hover{box-shadow:0 4px 12px rgba(0,0,0,.05)}

.pag{padding:14px 18px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
.pag-info{font-size:12px;color:var(--muted)}
.pagination{margin:0;gap:4px}
.pagination .page-link{padding:6px 12px;font-size:12px;font-weight:700;font-family:'Sora',sans-serif;border-radius:9px;border:1px solid var(--border-md);color:var(--muted);transition:all .2s}
.pagination .page-link:hover{background:#E6F1FB;border-color:var(--pruss-mid);color:var(--pruss-mid)}
.pagination .page-item.active .page-link{background:var(--pruss);border-color:var(--pruss);color:white}

.btn-primary-custom{display:inline-flex;align-items:center;gap:7px;background:var(--pruss);color:white;border:none;border-radius:var(--r-md);font-family:'Sora',sans-serif;font-size:12px;font-weight:700;padding:9px 16px;cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap}
.btn-primary-custom:hover{background:var(--pruss-mid);transform:translateY(-1px);color:white}
.btn-outline-custom{display:inline-flex;align-items:center;gap:7px;background:transparent;color:var(--muted);border:1px solid var(--border-md);border-radius:var(--r-md);font-family:'Sora',sans-serif;font-size:12px;font-weight:700;padding:8px 14px;cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap}
.btn-outline-custom:hover{border-color:var(--pruss-mid);background:#E6F1FB;color:var(--pruss-mid)}

.empty{text-align:center;padding:60px 24px}
.empty-ico{width:64px;height:64px;border-radius:50%;background:var(--fog);border:1px solid var(--border-md);display:inline-flex;align-items:center;justify-content:center;font-size:1.7rem;color:var(--subtle);margin-bottom:14px}
.empty-txt{font-size:13px;color:var(--muted);margin-bottom:18px;line-height:1.6}

.filter-actions{display:flex;gap:10px;margin-top:10px}
.btn-apply-filter{
    flex:1;display:flex;align-items:center;justify-content:center;gap:6px;
    padding:8px;border-radius:var(--r-sm);font-size:12px;font-weight:700;
    font-family:'Sora',sans-serif;cursor:pointer;transition:all .2s;
    border:1px solid var(--border-md);background:var(--pruss-mid);color:white;
}
.btn-apply-filter:hover{background:var(--pruss);transform:translateY(-1px)}

/* Breakdown tooltip panel */
.breakdown-panel{
    display:none;position:absolute;z-index:100;
    background:white;border:1px solid var(--border-md);border-radius:14px;
    box-shadow:0 8px 24px rgba(0,0,0,.12);
    padding:14px 16px;min-width:260px;font-size:12px;
}
.breakdown-panel.show{display:block}
.bp-row{display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border)}
.bp-row:last-child{border-bottom:none}
.bp-label{color:var(--muted);font-size:11px}
.bp-val{font-family:'JetBrains Mono',monospace;font-weight:700;font-size:12px}
.bp-total{font-weight:800;color:var(--pruss)}
.bp-header{font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--subtle);margin-bottom:6px;margin-top:8px}
.bp-header:first-child{margin-top:0}

.table-container{position:relative;min-height:400px}

@media(max-width:1024px){.daftar-layout{grid-template-columns:1fr;gap:16px}}
@media(max-width:767px){
    .daftar-layout{padding:0}
    .main-head{flex-direction:column}
    .search-row{flex-direction:column;align-items:stretch}
    .filter-actions{flex-direction:column}
}
</style>
@endpush

@section('content')
@php
    $user    = auth()->user();
    $canAll  = $user->canAccessAllUnits();
    $isOp    = $user->isOperator();
    $cfBox      = request('box');
    $cfKinerja  = request('kinerja_filter');
    $cfPotensi  = request('potensi_filter');
    $cfUe1      = request('ue1');
    $cfNkp      = request('nkp_filter');
    $cfKmBox    = request('box_kemenkeu');
    $cfJenjang  = request('jenjang');
    $cfSearch   = request('search');
    $cfNilaiPot = request('nilai_potensi_filter');

    $all       = \App\Models\Pegawai::query()->when($isOp, fn($q)=>$q->where('ue1',$user->ue1));
    $totalAll  = $all->count();
    $topAll    = $all->clone()->whereIn('box',[7,8,9])->count();
    $midAll    = $all->clone()->whereIn('box',[2,4,5])->count();
    $lowAll    = $all->clone()->whereIn('box',[1,3,6])->count();
@endphp

<div class="daftar-layout">

    {{-- ══ FILTER SIDEBAR ══ --}}
    <aside class="filter-sidebar">
        <div class="filter-head">
            <div class="filter-head-icon"><i class="bi bi-funnel-fill"></i></div>
            <div>
                <div class="filter-head-title">Filter Data</div>
                <div class="filter-head-sub">{{ number_format($totalAll) }} pegawai terdaftar</div>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="filter-section">
            <div class="filter-label">Ringkasan</div>
            <div class="filter-stats">
                <div class="stat-card"><div class="stat-value stat-blue">{{ number_format($totalAll) }}</div><div class="stat-label">Total</div></div>
                <div class="stat-card"><div class="stat-value stat-teal">{{ number_format($topAll) }}</div><div class="stat-label">Tinggi</div></div>
                <div class="stat-card"><div class="stat-value stat-amber">{{ number_format($midAll) }}</div><div class="stat-label">Menengah</div></div>
                <div class="stat-card"><div class="stat-value stat-red">{{ number_format($lowAll) }}</div><div class="stat-label">Perlu Dev</div></div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="filter-section " >
            <div class="filter-label bi bi-funnel-fill"   > Filter Data</div>

            @if($canAll)
            <div class="filter-sub-label">Unit Eselon I</div>
            <select class="filter-select {{ $cfUe1?'active':'' }}" id="filterUe1">
                <option value="">Semua Unit</option>
                @foreach(\App\Models\Pegawai::UE1_SHORT as $code=>$short)
                    <option value="{{ $code }}" {{ $cfUe1==$code?'selected':'' }}>{{ $code }} – {{ $short }}</option>
                @endforeach
            </select>
            @endif

            <div class="filter-sub-label">Jenjang Jabatan</div>
            <select class="filter-select {{ $cfJenjang?'active':'' }}" id="filterJenjang">
                <option value="">Semua Jenjang</option>
                @foreach(\App\Models\Pegawai::JENJANG_LIST as $j)
                    <option value="{{ $j }}" {{ $cfJenjang===$j?'selected':'' }}>{{ $j }}</option>
                @endforeach
            </select>

            <div class="filter-sub-label" style="color:#185FA5;border-top:1px solid var(--border);padding-top:8px;margin-top:4px">
                </i>Kinerja (Predikat)
            </div>
            <select class="filter-select {{ $cfKinerja?'active':'' }}" id="filterKinerja">
                <option value="">Semua Kinerja</option>
                <option value="atas"   {{ $cfKinerja==='atas'  ?'selected':'' }}>Di Atas Ekspektasi</option>
                <option value="sesuai" {{ $cfKinerja==='sesuai'?'selected':'' }}>Sesuai Ekspektasi</option>
                <option value="bawah"  {{ $cfKinerja==='bawah' ?'selected':'' }}>Di Bawah Ekspektasi</option>
            </select>

            <div class="filter-sub-label" style="color:#185FA5;border-top:1px solid var(--border);padding-top:8px;margin-top:4px">
                </i>Nilai NKP
            </div>            
            <select class="filter-select {{ $cfNkp?'active':'' }}" id="filterNkp">
                <option value="">Semua NKP</option>
                <option value="atas"   {{ $cfNkp==='atas'  ?'selected':'' }}>Di Atas Ekspektasi (&gt;100)</option>
                <option value="sesuai" {{ $cfNkp==='sesuai'?'selected':'' }}>Sesuai Ekspektasi (90–100)</option>
                <option value="bawah"  {{ $cfNkp==='bawah' ?'selected':'' }}>Di Bawah Ekspektasi (&lt;90)</option>
            </select>

            <div class="filter-sub-label" style="color:#185FA5;border-top:1px solid var(--border);padding-top:8px;margin-top:4px">
                </i>Kategori Potensial
            </div>
            <select class="filter-select {{ $cfPotensi?'active':'' }}" id="filterPotensi">
                <option value="">Semua Potensi</option>
                <option value="tinggi"   {{ $cfPotensi==='tinggi'  ?'selected':'' }}>Potensi Tinggi (≥90)</option>
                <option value="menengah" {{ $cfPotensi==='menengah'?'selected':'' }}>Potensi Menengah (78–90)</option>
                <option value="rendah"   {{ $cfPotensi==='rendah'  ?'selected':'' }}>Potensi Rendah (&lt;78)</option>
            </select>

            {{-- <div class="filter-sub-label" style="color:#0F6E56">Nilai Potensi Total</div>
            <select class="filter-select {{ $cfNilaiPot?'active':'' }}" id="filterNilaiPot">
                <option value="">Semua Nilai</option>
                <option value="ada"    {{ $cfNilaiPot==='ada'  ?'selected':'' }}>Sudah Dihitung</option>
                <option value="belum"  {{ $cfNilaiPot==='belum'?'selected':'' }}>Belum Dihitung</option>
            </select> --}}

            <div class="filter-sub-label" style="border-top:1px solid var(--border);padding-top:8px;margin-top:4px">Box PAN-RB</div>
            <select class="filter-select {{ $cfBox?'active':'' }}" id="filterBox">
                <option value="">Semua Box</option>
                @for($i=1;$i<=9;$i++)<option value="{{ $i }}" {{ $cfBox==$i?'selected':'' }}>Box {{ $i }}</option>@endfor
            </select>

            <div class="filter-sub-label">Box Kemenkeu</div>
            <select class="filter-select {{ $cfKmBox?'active':'' }}" id="filterKmBox">
                <option value="">Semua Box</option>
                @foreach(['1','2','3','4','5','6','7','8','9'] as $rom)
                    <option value="{{ $rom }}" {{ $cfKmBox===$rom?'selected':'' }}>Box {{ $rom }}</option>
                @endforeach
            </select>

            <div class="filter-actions">
                <button id="applyFilterButton" class="btn-apply-filter">
                    <i class="bi bi-funnel-fill"></i> Terapkan
                </button>
                <button class="filter-reset" id="resetFilters" style="margin-top:0">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>
        </div>

        {{-- Tindakan Massal --}}
        <div class="filter-section">
            <div class="filter-label">Tindakan Massal</div>
            @if($totalAll > 0)
            <form action="{{ route($canAll?'pegawai.destroyAll':'pegawai.destroyAllUnit') }}" method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus semua data pegawai? Tindakan ini tidak dapat dibatalkan.')">
                @csrf @method('DELETE')
                <button type="submit" style="width:100%;display:flex;align-items:center;gap:7px;justify-content:center;padding:8px;border-radius:var(--r-sm);font-size:12px;font-weight:700;color:#A32D2D;border:1px solid #F7C1C1;background:#FCEBEB;cursor:pointer;font-family:'Sora',sans-serif;transition:all .2s"
                        onmouseover="this.style.background='#f5c6c6'" onmouseout="this.style.background='#FCEBEB'">
                    <i class="bi bi-trash3"></i>
                    @if($isOp) Hapus Semua Data Unit {{ $user->ue1_short }} @else Hapus Semua Data @endif
                </button>
            </form>
            @endif
        </div>
    </aside>

    {{-- ══ MAIN CONTENT ══ --}}
    <main>
        <div class="main-head">
            <div>
                <div class="main-title">Daftar Pegawai</div>
                <div class="main-sub">
                    @if($isOp){{ \App\Models\Pegawai::UE1_LIST[$user->ue1] ?? '' }}
                    @else Seluruh Unit Eselon I @endif
                    &nbsp;·&nbsp; <strong style="color:var(--pruss-mid)" id="totalCount">{{ number_format($pegawai->total()) }}</strong> pegawai ditemukan
                </div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <a href="{{ route('pegawai.create') }}" class="btn-primary-custom"><i class="bi bi-person-plus-fill"></i>Input Baru</a>
                <a href="{{ route('pegawai.import.form') }}" class="btn-outline-custom"><i class="bi bi-file-earmark-arrow-up"></i>Import</a>
                <a href="{{ route('pegawai.export') }}" class="btn-outline-custom"><i class="bi bi-download"></i>Export</a>
            </div>
        </div>

        {{-- Search --}}
        <div class="search-row">
            <div class="search-wrap">
                <i class="bi bi-search search-ico"></i>
                <input type="text" id="liveSearch" class="search-inp"
                       placeholder="Cari NIP atau nama pegawai..."
                       value="{{ $cfSearch }}" autocomplete="off">
                <span id="searchSpinner"
                      style="display:none;position:absolute;right:12px;top:50%;transform:translateY(-50%);width:13px;height:13px;border:2px solid var(--border-md);border-top-color:var(--pruss-mid);border-radius:50%;animation:spin .6s linear infinite"></span>
            </div>
            <button id="searchButton" class="btn-primary-custom">
                <i class="bi bi-search"></i> Cari
            </button>
        </div>

        <div class="chip-bar" id="chipBar"></div>

        <div class="tbl-card">
            <div class="tbl-card-hdr">
                <div class="tbl-title-sm">Hasil Pencarian</div>
                <div class="count-info" id="countInfo">
                    @if($pegawai->count() > 0)
                    Menampilkan <strong style="color:var(--pruss-mid)">{{ $pegawai->firstItem() }}</strong>–<strong style="color:var(--pruss-mid)">{{ $pegawai->lastItem() }}</strong>
                    dari <strong style="color:var(--pruss-mid)">{{ number_format($pegawai->total()) }}</strong> pegawai
                    @else
                    Tidak ada data
                    @endif
                </div>
            </div>

            <div class="table-container">
                {{-- Desktop Table --}}
                <div class="d-none d-md-block" style="overflow-x:auto">
                    <table class="main-tbl">
                        <thead>
                            <tr>
                                <th style="width:44px">#</th>
                                <th style="min-width:180px">NIP / Nama / Jabatan</th>
                                @if($canAll)<th style="min-width:80px">Unit</th>@endif
                                <th style="min-width:100px">Jenjang</th>
                                {{-- Kinerja --}}
                                <th class="th-kinerja" style="width:90px">NKP</th>
                                <th class="th-kinerja" style="min-width:130px">Predikat Kinerja</th>
                                {{-- Potensial --}}
                                <th class="th-potensial" style="min-width:120px">Potensial</th>
                                <th class="th-potensial" style="min-width:110px">Nilai Potensi</th>
                                {{-- Box --}}
                                <th class="th-box" style="width:90px">Box PAN-RB</th>
                                <th class="th-box" style="width:90px">Box Kemenkeu</th>
                                <th style="width:42px"></th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @if($pegawai->count() > 0)
                                @foreach($pegawai as $i => $p)
                                @php
                                    $boxKm    = $p->box_kemenkeu ?? \App\Models\Pegawai::calculateBoxKemenkeu($p->kategori_potensial, $p->kategori_kinerja_nkp ?? $p->kategori_kinerja);
                                    $nkpVal   = $p->nkp !== null ? number_format((float)$p->nkp, 2) : null;
                                    $nkpKat   = $p->kategori_kinerja_nkp ?? null;
                                    $nkpColor = match($nkpKat){'Di atas ekspektasi'=>'#0F6E56','Sesuai ekspektasi'=>'#185FA5',default=>'#A32D2D'};
                                    $nilaiTotal = $p->nilai_potensi_total !== null ? (float)$p->nilai_potensi_total : null;
                                    $nilaiColor = $nilaiTotal !== null ? ($nilaiTotal >= 90 ? '#0F6E56' : ($nilaiTotal >= 78 ? '#854F0B' : '#A32D2D')) : '#9aaac2';
                                @endphp
                                <tr>
                                    <td><span class="row-n">{{ $pegawai->firstItem() + $i }}</span></td>
                                    <td>
                                        <div class="pname">{{ $p->nama ?? '-' }}</div>
                                        <div class="pnip">{{ $p->nip }}</div>
                                        @if($p->jabatan)<div class="pjab"><i class="bi bi-briefcase" style="font-size:10px"></i> {{ $p->jabatan }}@if($p->jenjang_jabatan) · {{ $p->jenjang_jabatan }}@endif</div>@endif
                                    </td>
                                    @if($canAll)<td><span class="bdg b-gray" title="{{ $p->ue1_nama }}">{{ $p->ue1_short }}</span></td>@endif
                                    <td>
                                        @if($p->jenjang_jabatan)
                                            <span class="bdg b-gray" style="font-size:10px">{{ $p->jenjang_jabatan }}</span>
                                        @else<span style="color:var(--border-md);font-size:12px">—</span>@endif
                                    </td>
                                    {{-- NKP --}}
                                    <td>
                                        @if($nkpVal)
                                            <span class="nkp-val" style="color:{{ $nkpColor }}">{{ $nkpVal }}</span>
                                            @if($nkpKat)<span class="nkp-kat" style="color:{{ $nkpColor }}">{{ $nkpKat==='Di atas ekspektasi'?'↑ Atas':($nkpKat==='Sesuai ekspektasi'?'= Sesuai':'↓ Bawah') }}</span>@endif
                                        @else<span style="color:var(--border-md);font-size:12px">—</span>@endif
                                    </td>
                                    {{-- Predikat Kinerja --}}
                                    <td>
                                        @if($p->predikat_kinerja)
                                            @if($p->kategori_kinerja==='Di atas ekspektasi')<span class="bdg b-teal">{{ $p->predikat_kinerja }}</span>
                                            @elseif($p->kategori_kinerja==='Sesuai ekspektasi')<span class="bdg b-blue">{{ $p->predikat_kinerja }}</span>
                                            @else<span class="bdg b-red">{{ $p->predikat_kinerja }}</span>@endif
                                        @else
                                            @if($p->kategori_kinerja==='Di atas ekspektasi')<span class="bdg b-teal">Di Atas</span>
                                            @elseif($p->kategori_kinerja==='Sesuai ekspektasi')<span class="bdg b-blue">Sesuai</span>
                                            @else<span class="bdg b-red">Di Bawah</span>@endif
                                        @endif
                                    </td>
                                    {{-- Potensial --}}
                                    <td class="td-potensial">
                                        @if($p->kategori_potensial==='potensial tinggi')<span class="bdg b-teal">Tinggi</span>
                                        @elseif($p->kategori_potensial==='potensial menengah')<span class="bdg b-amber">Menengah</span>
                                        @else<span class="bdg b-red">Rendah</span>@endif
                                    </td>
                                    {{-- Nilai Potensi Total --}}
                                    <td class="td-potensial">
                                        @if($nilaiTotal !== null)
                                            <span class="nilai-total" style="color:{{ $nilaiColor }}">{{ number_format($nilaiTotal, 2) }}</span>
                                            <span class="nilai-total-sub" style="color:{{ $nilaiColor }}">
                                                @if($nilaiTotal >= 90) ★ Tinggi
                                                @elseif($nilaiTotal >= 78) ◆ Menengah
                                                @else ▼ Rendah @endif
                                            </span>
                                            {{-- Tombol breakdown --}}
                                            <button type="button" class="detail-btn"
                                                    onclick="showBreakdown(this, {{ $p->id }},
                                                        {{ $p->kompetensi_teknis ?? 'null' }},
                                                        {{ $p->kompetensi_mansoskul ?? 'null' }},
                                                        [{{ implode(',', array_map(fn($k)=>$p->{'potensi_'.$k} ?? 'null', range(1,9))) }}],
                                                        {{ $p->rj_pendidikan_formal ?? 'null' }},
                                                        {{ $p->rj_pelatihan ?? 'null' }},
                                                        {{ $p->rj_pengalaman ?? 'null' }},
                                                        {{ $p->rj_integritas ?? 'null' }},
                                                        {{ $p->rj_moralitas ?? 'null' }}
                                                    )"
                                                    title="Lihat breakdown nilai">
                                                <i class="bi bi-info"></i>
                                            </button>
                                        @else
                                            <span style="color:var(--border-md);font-size:11px;display:block">—</span>
                                            <span style="color:var(--subtle);font-size:10px">Belum dihitung</span>
                                        @endif
                                    </td>
                                    {{-- Box --}}
                                    <td><span class="box-b bb{{ $p->box }}">{{ $p->box }}</span></td>
                                    <td><span class="km-b">{{ $boxKm }}</span></td>
                                    <td>
                                        <form action="{{ route('pegawai.destroy',$p->id) }}" method="POST" onsubmit="return confirm('Hapus pegawai ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="del-btn"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="d-md-none" style="padding:10px 14px" id="mobileCards">
                    @if($pegawai->count() > 0)
                        @foreach($pegawai as $p)
                        @php
                            $bc    = in_array($p->box,[7,8,9])?'#0F6E56':(in_array($p->box,[4,5,6])?'#185FA5':'#A32D2D');
                            $boxKm = $p->box_kemenkeu ?? \App\Models\Pegawai::calculateBoxKemenkeu($p->kategori_potensial, $p->kategori_kinerja_nkp ?? $p->kategori_kinerja);
                            $nkpColor = match($p->kategori_kinerja_nkp??''){'Di atas ekspektasi'=>'#0F6E56','Sesuai ekspektasi'=>'#185FA5',default=>'#A32D2D'};
                            $nilaiTotal = $p->nilai_potensi_total !== null ? (float)$p->nilai_potensi_total : null;
                            $nilaiColor = $nilaiTotal !== null ? ($nilaiTotal >= 90 ? '#0F6E56' : ($nilaiTotal >= 78 ? '#854F0B' : '#A32D2D')) : null;
                        @endphp
                        <div class="m-card" style="border-left-color:{{ $bc }}">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:8px">
                                <div style="flex:1;min-width:0">
                                    <div class="pname">{{ $p->nama ?? '-' }}</div>
                                    <div class="pnip">{{ $p->nip }}</div>
                                    @if($p->jabatan)<div class="pjab">{{ $p->jabatan }}@if($p->jenjang_jabatan) · {{ $p->jenjang_jabatan }}@endif</div>@endif
                                    @if($p->nkp!==null)<div style="font-size:11px;font-weight:700;color:{{ $nkpColor }};margin-top:3px;font-family:'JetBrains Mono',monospace">NKP: {{ number_format((float)$p->nkp,2) }}</div>@endif
                                    @if($nilaiTotal !== null)
                                        <div style="font-size:11px;font-weight:800;color:{{ $nilaiColor }};margin-top:2px;font-family:'JetBrains Mono',monospace">
                                            Nilai Potensi: {{ number_format($nilaiTotal, 2) }}
                                        </div>
                                    @endif
                                    {{-- Kompetensi ringkas --}}
                                    @if($p->kompetensi_teknis !== null || $p->kompetensi_mansoskul !== null)
                                    <div style="font-size:10px;color:var(--subtle);margin-top:3px">
                                        @if($p->kompetensi_teknis !== null)Teknis: <strong>{{ number_format((float)$p->kompetensi_teknis,1) }}</strong>@endif
                                        @if($p->kompetensi_mansoskul !== null) · Mansoskul: <strong>{{ number_format((float)$p->kompetensi_mansoskul,1) }}</strong>@endif
                                    </div>
                                    @endif
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
                                    <div style="display:flex;flex-direction:column;align-items:center;gap:3px">
                                        <span class="box-b bb{{ $p->box }}" style="width:30px;height:30px;font-size:12px">{{ $p->box }}</span>
                                        <span class="km-b" style="font-size:10px;padding:1px 6px;min-width:30px;height:22px">{{ $boxKm }}</span>
                                    </div>
                                    <form action="{{ route('pegawai.destroy',$p->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="del-btn"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </div>
                            </div>
                            <div style="display:flex;flex-wrap:wrap;gap:4px">
                                @if($canAll)<span class="bdg b-gray">{{ $p->ue1_short }}</span>@endif
                                @if($p->predikat_kinerja)
                                    @if($p->kategori_kinerja==='Di atas ekspektasi')<span class="bdg b-teal">{{ $p->predikat_kinerja }}</span>
                                    @elseif($p->kategori_kinerja==='Sesuai ekspektasi')<span class="bdg b-blue">{{ $p->predikat_kinerja }}</span>
                                    @else<span class="bdg b-red">{{ $p->predikat_kinerja }}</span>@endif
                                @endif
                                @if($p->kategori_potensial==='potensial tinggi')<span class="bdg b-teal">Potensi Tinggi</span>
                                @elseif($p->kategori_potensial==='potensial menengah')<span class="bdg b-amber">Potensi Menengah</span>
                                @else<span class="bdg b-red">Potensi Rendah</span>@endif
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

                {{-- Empty State --}}
                <div id="emptyState" style="display:{{ $pegawai->count() === 0 ? 'block' : 'none' }}" class="empty">
                    <div class="empty-ico"><i class="bi bi-inbox-fill"></i></div>
                    <p class="empty-txt">Tidak ada data yang sesuai dengan filter.<br>Coba ubah atau reset filter.</p>
                </div>
            </div>

            <div class="pag">
                <div class="pag-info" id="pagInfo">
                    Halaman {{ $pegawai->currentPage() }} / {{ $pegawai->lastPage() }}
                </div>
                <div id="pagLinks">{{ $pegawai->links('pagination::bootstrap-4') }}</div>
            </div>
        </div>
    </main>
</div>

{{-- Breakdown Panel (floating) --}}
<div id="breakdownPanel" class="breakdown-panel">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
        <div style="font-size:13px;font-weight:700;color:var(--pruss)">Breakdown Nilai Potensi</div>
        <button onclick="closeBreakdown()" style="background:none;border:none;cursor:pointer;color:var(--subtle);font-size:16px;padding:0 2px">×</button>
    </div>

    <div class="bp-header" style="color:#185FA5"><i class="bi bi-clipboard-data me-1"></i>Kompetensi (bobot 70%)</div>
    <div class="bp-row"><span class="bp-label">Teknis</span><span class="bp-val" id="bp-teknis">—</span></div>
    <div class="bp-row"><span class="bp-label">Mansoskul</span><span class="bp-val" id="bp-mansoskul">—</span></div>
    <div class="bp-row" style="background:#E6F1FB;border-radius:6px;padding:4px 6px;margin-top:2px">
        <span class="bp-label" style="font-weight:700;color:#185FA5">Nilai Kompetensi</span>
        <span class="bp-val" id="bp-nilai-k" style="color:#185FA5">—</span>
    </div>

    <div class="bp-header" style="color:#0F6E56;margin-top:10px"><i class="bi bi-graph-up me-1"></i>Potensi 9 Komponen (bobot 20%)</div>
    <div id="bp-potensi-rows"></div>
    <div class="bp-row" style="background:#E1F5EE;border-radius:6px;padding:4px 6px;margin-top:2px">
        <span class="bp-label" style="font-weight:700;color:#0F6E56">Nilai Potensi</span>
        <span class="bp-val" id="bp-nilai-p" style="color:#0F6E56">—</span>
    </div>

    <div class="bp-header" style="color:#854F0B;margin-top:10px"><i class="bi bi-journal-bookmark me-1"></i>Rekam Jejak (bobot 10%)</div>
    <div class="bp-row"><span class="bp-label">Pend. Formal</span><span class="bp-val" id="bp-pend">—</span></div>
    <div class="bp-row"><span class="bp-label">Pelatihan</span><span class="bp-val" id="bp-pel">—</span></div>
    <div class="bp-row"><span class="bp-label">Pengalaman</span><span class="bp-val" id="bp-peng">—</span></div>
    <div class="bp-row"><span class="bp-label">Integritas</span><span class="bp-val" id="bp-int">—</span></div>
    <div class="bp-row"><span class="bp-label">Moralitas</span><span class="bp-val" id="bp-mor">—</span></div>
    <div class="bp-row" style="background:#FAEEDA;border-radius:6px;padding:4px 6px;margin-top:2px">
        <span class="bp-label" style="font-weight:700;color:#854F0B">Nilai Rekam Jejak</span>
        <span class="bp-val" id="bp-nilai-rj" style="color:#854F0B">—</span>
    </div>

    <div style="margin-top:10px;background:var(--pruss);border-radius:8px;padding:8px 10px;display:flex;justify-content:space-between;align-items:center">
        <span style="font-size:11px;font-weight:700;color:rgba(255,255,255,.7)">TOTAL NILAI POTENSI</span>
        <span id="bp-total" style="font-size:16px;font-weight:800;color:white;font-family:'JetBrains Mono',monospace">—</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
const DAFTAR_URL = '{{ route('pegawai.daftar') }}';
let currentXhr = null;

let currentFilters = {
    search: '{{ $cfSearch }}',
    box: '{{ $cfBox }}',
    kinerja: '{{ $cfKinerja }}',
    potensi: '{{ $cfPotensi }}',
    ue1: '{{ $cfUe1 }}',
    nkp: '{{ $cfNkp }}',
    kmbox: '{{ $cfKmBox }}',
    jenjang: '{{ $cfJenjang }}',
    nilai_potensi: '{{ $cfNilaiPot }}',
    page: 1
};

const filterLabels = {
    box         : v => 'Box PAN-RB: ' + v,
    kinerja     : v => ({atas:'Kinerja Di Atas',sesuai:'Kinerja Sesuai',bawah:'Kinerja Di Bawah'}[v] || v),
    potensi     : v => ({tinggi:'Potensi Tinggi',menengah:'Potensi Menengah',rendah:'Potensi Rendah'}[v] || v),
    ue1         : v => 'UE1: ' + v,
    nkp         : v => ({atas:'NKP Di Atas',sesuai:'NKP Sesuai',bawah:'NKP Di Bawah'}[v] || v),
    kmbox       : v => 'Box Kemenkeu: ' + v,
    jenjang     : v => 'Jenjang: ' + v,
    search      : v => 'Cari: ' + v,
    nilai_potensi: v => ({ada:'Nilai Potensi: Sudah',belum:'Nilai Potensi: Belum'}[v] || v),
};

function $id(id) { return document.getElementById(id); }

function collectFilters() {
    return {
        search       : $id('liveSearch')?.value.trim() || '',
        box          : $id('filterBox')?.value || '',
        kinerja      : $id('filterKinerja')?.value || '',
        potensi      : $id('filterPotensi')?.value || '',
        ue1          : $id('filterUe1')?.value || '',
        nkp          : $id('filterNkp')?.value || '',
        kmbox        : $id('filterKmBox')?.value || '',
        jenjang      : $id('filterJenjang')?.value || '',
        nilai_potensi: $id('filterNilaiPot')?.value || '',
    };
}

function syncSelects(filters) {
    const syncMap = {
        box          : ['filterBox'],
        kinerja      : ['filterKinerja'],
        potensi      : ['filterPotensi'],
        ue1          : ['filterUe1'],
        nkp          : ['filterNkp'],
        kmbox        : ['filterKmBox'],
        jenjang      : ['filterJenjang'],
        nilai_potensi: ['filterNilaiPot'],
    };
    Object.entries(syncMap).forEach(([key, ids]) => {
        ids.forEach(id => {
            const el = $id(id);
            if (el) { el.value = filters[key]; el.classList.toggle('active', !!filters[key]); }
        });
    });
}

function applyFilters() {
    const filters = collectFilters();
    currentFilters = { ...filters, page: 1 };
    syncSelects(filters);
    fetchResults();
}

function resetAllFilters() {
    const allIds = ['liveSearch','filterBox','filterKinerja','filterPotensi','filterUe1',
                    'filterNkp','filterKmBox','filterJenjang','filterNilaiPot'];
    allIds.forEach(id => { const el=$id(id); if(el){el.value='';el.classList.remove('active');} });
    currentFilters = { search:'',box:'',kinerja:'',potensi:'',ue1:'',nkp:'',kmbox:'',jenjang:'',nilai_potensi:'',page:1 };
    fetchResults();
}

window.clearFilter = function(key) {
    currentFilters[key] = '';
    currentFilters.page = 1;
    const maps = {
        search: ['liveSearch'], box: ['filterBox'], kinerja: ['filterKinerja'],
        potensi: ['filterPotensi'], ue1: ['filterUe1'], nkp: ['filterNkp'],
        kmbox: ['filterKmBox'], jenjang: ['filterJenjang'], nilai_potensi: ['filterNilaiPot'],
    };
    (maps[key] || []).forEach(id => { const el=$id(id); if(el){el.value='';el.classList.remove('active');} });
    fetchResults();
};

function fetchResults() {
    if (currentXhr) currentXhr.abort();

    const params = new URLSearchParams();
    if (currentFilters.search)        params.set('search', currentFilters.search);
    if (currentFilters.box)           params.set('box', currentFilters.box);
    if (currentFilters.kinerja)       params.set('kinerja_filter', currentFilters.kinerja);
    if (currentFilters.potensi)       params.set('potensi_filter', currentFilters.potensi);
    if (currentFilters.ue1)           params.set('ue1', currentFilters.ue1);
    if (currentFilters.nkp)           params.set('nkp_filter', currentFilters.nkp);
    if (currentFilters.kmbox)         params.set('box_kemenkeu', currentFilters.kmbox);
    if (currentFilters.jenjang)       params.set('jenjang', currentFilters.jenjang);
    if (currentFilters.nilai_potensi) params.set('nilai_potensi_filter', currentFilters.nilai_potensi);
    if (currentFilters.page > 1)      params.set('page', currentFilters.page);
    params.set('ajax', '1');

    const spinner   = $id('searchSpinner');
    const tbody     = $id('tableBody');
    const cards     = $id('mobileCards');

    if (spinner) spinner.style.display = 'block';
    if (tbody) tbody.style.opacity = '0.5';
    if (cards) cards.style.opacity = '0.5';

    currentXhr = new XMLHttpRequest();
    currentXhr.open('GET', DAFTAR_URL + '?' + params.toString());
    currentXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    currentXhr.setRequestHeader('Accept', 'application/json');

    currentXhr.onload = function() {
        if (spinner) spinner.style.display = 'none';
        if (tbody) tbody.style.opacity = '';
        if (cards) cards.style.opacity = '';

        if (currentXhr.status === 200) {
            try {
                const json = JSON.parse(currentXhr.responseText);
                if (tbody && json.table_html !== undefined) {
                    tbody.innerHTML = json.table_html;
                    tbody.classList.add('tbl-fade');
                    setTimeout(() => tbody.classList.remove('tbl-fade'), 250);
                }
                if (cards && json.cards_html !== undefined) cards.innerHTML = json.cards_html;
                const empty = $id('emptyState');
                if (empty) empty.style.display = json.empty ? 'block' : 'none';
                const countInfo = $id('countInfo');
                if (countInfo) {
                    countInfo.innerHTML = json.empty
                        ? 'Tidak ada data'
                        : 'Menampilkan <strong style="color:var(--pruss-mid)">' + json.from + '–' + json.to +
                          '</strong> dari <strong style="color:var(--pruss-mid)">' + Number(json.total).toLocaleString('id-ID') + '</strong> pegawai';
                }
                const totalCount = $id('totalCount');
                if (totalCount) totalCount.innerHTML = Number(json.total).toLocaleString('id-ID');
                const pagInfo = $id('pagInfo');
                if (pagInfo) pagInfo.innerHTML = 'Halaman ' + json.current_page + ' / ' + json.last_page;
                const pagLinks = $id('pagLinks');
                if (pagLinks && json.pag_html) { pagLinks.innerHTML = json.pag_html; bindPagination(); }
                renderChips();
            } catch(e) { console.error('Parse error:', e); }
        }
    };
    currentXhr.onerror = function() {
        if (spinner) spinner.style.display = 'none';
        if (tbody) tbody.style.opacity = '';
        if (cards) cards.style.opacity = '';
    };
    currentXhr.send();

    const cleanP = new URLSearchParams(params);
    cleanP.delete('ajax');
    history.replaceState(null, '', window.location.pathname + (cleanP.toString() ? '?' + cleanP.toString() : ''));
}

function bindPagination() {
    document.querySelectorAll('#pagLinks a[href]').forEach(link => {
        const newLink = link.cloneNode(true);
        link.parentNode.replaceChild(newLink, link);
        newLink.addEventListener('click', function(e) {
            e.preventDefault();
            currentFilters.page = parseInt(new URL(this.href).searchParams.get('page') || 1);
            fetchResults();
            document.querySelector('.tbl-card').scrollIntoView({ behavior:'smooth', block:'start' });
        });
    });
}

function renderChips() {
    const bar = $id('chipBar');
    if (!bar) return;
    const chips = [];
    const keys = { box:'box', kinerja:'kinerja', potensi:'potensi', ue1:'ue1',
                   nkp:'nkp', kmbox:'kmbox', jenjang:'jenjang', search:'search', nilai_potensi:'nilai_potensi' };
    Object.entries(keys).forEach(([lk, sk]) => {
        if (currentFilters[sk]) {
            chips.push(`<span class="chip">${filterLabels[lk](currentFilters[sk])}<span class="chip-close" onclick="clearFilter('${sk}')">×</span></span>`);
        }
    });
    bar.innerHTML = chips.join('');
    const resetBtn = $id('resetFilters');
    if (resetBtn) resetBtn.classList.toggle('has-filter', chips.length > 0);
}

// ── Breakdown popup ───────────────────────────────────────────
function showBreakdown(btn, id, teknis, mansoskul, komponen, pend, pel, peng, intg, mor) {
    const panel = $id('breakdownPanel');

    const fmt = v => v !== null && v !== undefined ? Number(v).toFixed(2) : '—';

    // Kompetensi
    $id('bp-teknis').textContent    = fmt(teknis);
    $id('bp-mansoskul').textContent = fmt(mansoskul);
    let nilaiK = null;
    if (teknis !== null && mansoskul !== null) {
        nilaiK = ((parseFloat(teknis) + parseFloat(mansoskul)) / 2) * 0.70;
        $id('bp-nilai-k').textContent = nilaiK.toFixed(2);
    } else { $id('bp-nilai-k').textContent = '—'; }

    // Potensi 9 komponen
    const pRows = $id('bp-potensi-rows');
    pRows.innerHTML = '';
    const pVals = [];
    komponen.forEach((v, i) => {
        const row = document.createElement('div');
        row.className = 'bp-row';
        const vParsed = (v !== null && v !== undefined) ? parseFloat(v) : null;
        if (vParsed !== null) pVals.push(vParsed);
        row.innerHTML = `<span class="bp-label">Komponen ${i+1}</span><span class="bp-val">${fmt(v)}</span>`;
        pRows.appendChild(row);
    });
    let nilaiP = null;
    if (pVals.length > 0) {
        nilaiP = (pVals.reduce((a,b)=>a+b,0) / pVals.length) * 0.20;
        $id('bp-nilai-p').textContent = nilaiP.toFixed(2);
    } else { $id('bp-nilai-p').textContent = '—'; }

    // Rekam Jejak
    $id('bp-pend').textContent = fmt(pend);
    $id('bp-pel').textContent  = fmt(pel);
    $id('bp-peng').textContent = fmt(peng);
    $id('bp-int').textContent  = fmt(intg);
    $id('bp-mor').textContent  = fmt(mor);
    const rjVals = [pend, pel, peng, intg, mor].filter(v => v !== null && v !== undefined).map(parseFloat);
    let nilaiRJ = null;
    if (rjVals.length > 0) {
        nilaiRJ = (rjVals.reduce((a,b)=>a+b,0) / rjVals.length) * 0.10;
        $id('bp-nilai-rj').textContent = nilaiRJ.toFixed(2);
    } else { $id('bp-nilai-rj').textContent = '—'; }

    // Total
    const total = (nilaiK ?? 0) + (nilaiP ?? 0) + (nilaiRJ ?? 0);
    $id('bp-total').textContent = total.toFixed(2);

    // Position panel near button
    const rect = btn.getBoundingClientRect();
    panel.style.top  = (window.scrollY + rect.bottom + 8) + 'px';
    const left = Math.min(rect.left, window.innerWidth - 280);
    panel.style.left = Math.max(8, left) + 'px';
    panel.style.position = 'absolute';
    panel.classList.add('show');
}


function closeBreakdown() {
    $id('breakdownPanel').classList.remove('show');
}

// Close on outside click
document.addEventListener('click', function(e) {
    const panel = $id('breakdownPanel');
    if (panel && panel.classList.contains('show') && !panel.contains(e.target) && !e.target.closest('.detail-btn')) {
        panel.classList.remove('show');
    }
});

// ── Init ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    $id('searchButton')?.addEventListener('click', applyFilters);
    $id('applyFilterButton')?.addEventListener('click', applyFilters);
    $id('resetFilters')?.addEventListener('click', resetAllFilters);
    $id('liveSearch')?.addEventListener('keypress', e => { if (e.key==='Enter') applyFilters(); });
    bindPagination();
    renderChips();
});
</script>
@endpush