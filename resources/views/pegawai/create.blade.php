@extends('layouts.app')
@section('title', 'Input Pegawai')
@section('topbar-title', 'Input Data Pegawai')

@section('content')
<div class="ip-page">
<div class="ip-wrap">

    {{-- ══ HEADER ══ --}}
    <div class="ip-header">
        <a href="{{ route('pegawai.daftar') }}" class="ip-back-btn">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="ip-title">Input Data Pegawai</h2>
            <p class="ip-subtitle">Isi form di bawah untuk menambah data pegawai baru</p>
        </div>
    </div>

    {{-- ══ ERROR BOX ══ --}}
    @if($errors->any())
    <div class="ip-error-box">
        <div class="ip-error-title"><i class="bi bi-exclamation-triangle-fill me-1"></i>Terdapat kesalahan:</div>
        <ul class="ip-error-list">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- ══ FORM ══ --}}
    <form method="POST" action="{{ route('pegawai.store') }}" id="mainForm" novalidate>
        @csrf

        <div class="ip-body">

            {{-- ══ TOP GRID (3 COLUMNS) ══ --}}
            <div class="ip-grid-3col">

                {{-- LEFT COLUMN --}}
                <div class="ip-col">

                    {{-- IDENTITAS --}}
                    <div class="ip-card ip-card--default">
                        <div class="ip-card-head">
                            <i class="bi bi-person-badge me-1"></i>Identitas Pegawai
                        </div>
                        <div class="ip-card-body">
                            <div class="ip-field">
                                <label class="ip-label">NIP <span class="ip-req">*</span></label>
                                <input
                                    type="text"
                                    name="nip"
                                    value="{{ old('nip') }}"
                                    class="ip-input ip-required"
                                    placeholder="18 digit NIP"
                                    maxlength="18"
                                    data-label="NIP"
                                >
                                @error('nip')<p class="ip-err">{{ $message }}</p>@enderror
                            </div>
                            <div class="ip-field">
                                <label class="ip-label">Nama <span class="ip-opt">(opsional)</span></label>
                                <input
                                    type="text"
                                    name="nama"
                                    value="{{ old('nama') }}"
                                    class="ip-input"
                                    placeholder="Nama lengkap"
                                >
                            </div>
                            @if(auth()->user()->canAccessAllUnits())
                            <div class="ip-field">
                                <label class="ip-label">Unit Eselon I <span class="ip-req">*</span></label>
                                <select name="ue1" class="ip-input ip-required" data-label="Unit Eselon I">
                                    <option value="">— Pilih Unit —</option>
                                    @foreach(\App\Models\Pegawai::UE1_LIST as $code => $name)
                                        <option value="{{ $code }}" {{ old('ue1') == $code ? 'selected' : '' }}>
                                            {{ $code }} — {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ue1')<p class="ip-err">{{ $message }}</p>@enderror
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- JABATAN --}}
                    <div class="ip-card ip-card--default">
                        <div class="ip-card-head">
                            <i class="bi bi-briefcase me-1"></i>Jabatan
                            <span class="ip-optional-badge">opsional</span>
                        </div>
                        <div class="ip-card-body">
                            <div class="ip-field">
                                <label class="ip-label">Nama Jabatan</label>
                                <input
                                    type="text"
                                    name="jabatan"
                                    value="{{ old('jabatan') }}"
                                    class="ip-input"
                                    placeholder="Contoh: Analis Kebijakan"
                                >
                            </div>
                            <div class="ip-field">
                                <label class="ip-label">Jenjang Jabatan</label>
                                <select name="jenjang_jabatan" class="ip-input">
                                    <option value="">— Pilih Jenjang —</option>
                                    @foreach(\App\Models\Pegawai::JENJANG_LIST as $j)
                                        <option value="{{ $j }}" {{ old('jenjang_jabatan') === $j ? 'selected' : '' }}>{{ $j }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- KINERJA --}}
                    <div class="ip-card ip-card--default">
                        <div class="ip-card-head">
                            <i class="bi bi-activity me-1"></i>Data Kinerja
                            <span class="ip-req-badge">wajib diisi</span>
                        </div>
                        <div class="ip-card-body">
                            <div class="ip-field">
                                <label class="ip-label">
                                    NKP <span class="ip-req">*</span>
                                    <span class="ip-opt">Box Kemenkeu (0–120)</span>
                                </label>
                                <input
                                    type="number"
                                    name="nkp"
                                    id="nkpInput"
                                    value="{{ old('nkp') }}"
                                    class="ip-input ip-required"
                                    placeholder="Contoh: 95.50"
                                    step="0.01"
                                    min="0"
                                    max="120"
                                    data-label="NKP"
                                >
                                <div class="ip-hint" id="nkpHint"></div>
                                <div class="ip-warn" id="nkpWarn">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>NKP tidak boleh lebih dari 120!
                                </div>
                                @error('nkp')<p class="ip-err">{{ $message }}</p>@enderror
                            </div>
                            <div class="ip-field">
                                <label class="ip-label">
                                    Predikat Kinerja <span class="ip-req">*</span>
                                    <span class="ip-opt">Box PAN-RB</span>
                                </label>
                                <select
                                    name="predikat_kinerja"
                                    id="predikatSelect"
                                    class="ip-input ip-required"
                                    data-label="Predikat Kinerja"
                                >
                                    <option value="">— Pilih Predikat —</option>
                                    @foreach(\App\Models\Pegawai::PREDIKAT_LIST as $p)
                                        <option value="{{ $p }}" {{ old('predikat_kinerja') === $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                                <div class="ip-hint" id="predikatHint"></div>
                                @error('predikat_kinerja')<p class="ip-err">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                </div>{{-- /LEFT --}}

                {{-- RIGHT COLUMN --}}
                <div class="ip-col">

                    {{-- KOMPETENSI 70% --}}
                    <div class="ip-card ip-card--blue">
                        <div class="ip-card-head">
                            <i class="bi bi-clipboard-data me-1"></i>Kompetensi
                        </div>
                        <div class="ip-card-body">
                            <div class="ip-field">
                                <label class="ip-label">Kompetensi Teknis <span class="ip-opt">(0–100)</span></label>
                                <input
                                    type="number"
                                    name="kompetensi_teknis"
                                    id="kompTeknis"
                                    value="{{ old('kompetensi_teknis') }}"
                                    class="ip-input ip-recalc"
                                    placeholder="Contoh: 85.00"
                                    step="0.01" min="0" max="100"
                                >
                                @error('kompetensi_teknis')<p class="ip-err">{{ $message }}</p>@enderror
                            </div>
                            <div class="ip-field">
                                <label class="ip-label">Kompetensi Manajerial &amp; Sosio-Kultural <span class="ip-opt">(0–100)</span></label>
                                <input
                                    type="number"
                                    name="kompetensi_mansoskul"
                                    id="kompMansoskul"
                                    value="{{ old('kompetensi_mansoskul') }}"
                                    class="ip-input ip-recalc"
                                    placeholder="Contoh: 80.00"
                                    step="0.01" min="0" max="100"
                                >
                                @error('kompetensi_mansoskul')<p class="ip-err">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- POTENSI 20% --}}
                    <div class="ip-card ip-card--teal">
                        <div class="ip-card-head">
                            <i class="bi bi-graph-up-arrow me-1"></i>Potensi — 9 Komponen
                        </div>
                        <div class="ip-card-body">
                            <div class="ip-grid-3">
                                @for($i = 1; $i <= 9; $i++)
                                <div class="ip-field">
                                    <label class="ip-label">K{{ $i }} <span class="ip-opt">(0–100)</span></label>
                                    <input
                                        type="number"
                                        name="potensi_{{ $i }}"
                                        value="{{ old('potensi_' . $i) }}"
                                        class="ip-input ip-potensi ip-recalc"
                                        placeholder="0–100"
                                        step="0.01" min="0" max="100"
                                    >
                                    @error('potensi_' . $i)<p class="ip-err">{{ $message }}</p>@enderror
                                </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                </div>{{-- /RIGHT --}}

                {{-- THIRD COLUMN: REKAM JEJAK --}}
                <div class="ip-col">
                    <div class="ip-card ip-card--amber">
                        <div class="ip-card-head">
                            <i class="bi bi-journal-bookmark me-1"></i>Rekam Jejak
                        </div>
                        <div class="ip-card-body">
                            <div class="ip-grid-rj">
                                @foreach([
                                    'rj_pendidikan_formal' => 'Pendidikan Formal',
                                    'rj_pelatihan'         => 'Pelatihan',
                                    'rj_pengalaman'        => 'Pengalaman',
                                    'rj_integritas'        => 'Integritas',
                                    'rj_moralitas'         => 'Moralitas',
                                ] as $field => $label)
                                <div class="ip-field">
                                    <label class="ip-label">{{ $label }} <span class="ip-opt">(0–100)</span></label>
                                    <input
                                        type="number"
                                        name="{{ $field }}"
                                        value="{{ old($field) }}"
                                        class="ip-input ip-rj ip-recalc"
                                        placeholder="0–100"
                                        step="0.01" min="0" max="100"
                                    >
                                    @error($field)<p class="ip-err">{{ $message }}</p>@enderror
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>{{-- /THIRD --}}

            </div>{{-- /grid-3col --}}

            {{-- ══ PREVIEW KALKULASI ══ --}}
            <div class="ip-preview">
                <div class="ip-preview-label">
                    <i class="bi bi-calculator me-1"></i>Preview Kalkulasi Otomatis
                </div>

                <div class="ip-preview-grid">
                    {{-- Kompetensi --}}
                    <div class="ip-pcard ip-pcard--blue">
                        <div class="ip-pcard-label">Kompetensi</div>
                        <div class="ip-pcard-val" id="prevKomp">—</div>
                    </div>
                    {{-- Potensi --}}
                    <div class="ip-pcard ip-pcard--teal">
                        <div class="ip-pcard-label">Potensi</div>
                        <div class="ip-pcard-val" id="prevPot">—</div>
                    </div>
                    {{-- Rekam Jejak --}}
                    <div class="ip-pcard ip-pcard--amber">
                        <div class="ip-pcard-label">Rekam Jejak</div>
                        <div class="ip-pcard-val" id="prevRJ">—</div>
                    </div>
                    {{-- Total --}}
                    <div class="ip-pcard ip-pcard--total">
                        <div class="ip-pcard-label">Nilai Total → Kategori</div>
                        <div class="ip-pcard-val" id="prevTotal">—</div>
                        <div class="ip-kat-pill" id="prevKat">Belum dihitung</div>
                        <div class="ip-pcard-sub">≥90 Tinggi · ≥78 Menengah <90 · &lt;78 Rendah</div>
                    </div>
                </div>

                {{-- Box preview --}}
                <div class="ip-box-row">
                    <div class="ip-box-item">
                        <div class="ip-box-label">Box PAN-RB</div>
                        <div class="ip-box-circle ip-box-circle--panrb" id="boxPanrb">—</div>
                    </div>
                    <div class="ip-box-sep"></div>
                    <div class="ip-box-item">
                        <div class="ip-box-label">Box Kemenkeu</div>
                        <div class="ip-box-circle ip-box-circle--kemkeu" id="boxKemkeu">—</div>
                    </div>
                </div>
            </div>

        </div>{{-- /ip-body --}}

        {{-- ══ FOOTER ACTIONS ══ --}}
        <div class="ip-footer">
            <a href="{{ route('pegawai.daftar') }}" class="ip-btn-cancel">
                <i class="bi bi-arrow-left"></i> Batal
            </a>
            <button type="submit" class="ip-btn-submit" id="submitBtn">
                <i class="bi bi-check-circle"></i> Simpan Data
            </button>
        </div>

    </form>
</div>
</div>
@endsection


{{-- ════════════════════════════════════════════════════════════
     STYLES
════════════════════════════════════════════════════════════ --}}
@push('styles')
<style>
/* ── Root variables ────────────────────────────────────────── */
:root {
    --ip-navy:       #0f2744;
    --ip-navy2:      #1a3d6e;
    --ip-navy3:      #f4f6fb;
    --ip-border:     rgba(15,39,68,.15);
    --ip-radius:     10px;
    --ip-radius-lg:  14px;
    --ip-text:       #0f2744;
    --ip-muted:      #6b7a99;
    --ip-faint:      #9aaac2;

    --ip-blue50:     #E6F1FB;
    --ip-blue200:    #85B7EB;
    --ip-blue600:    #185FA5;
    --ip-blue800:    #0C447C;

    --ip-teal50:     #E1F5EE;
    --ip-teal200:    #5DCAA5;
    --ip-teal600:    #0F6E56;
    --ip-teal800:    #085041;

    --ip-amber50:    #FAEEDA;
    --ip-amber600:   #854F0B;
    --ip-amber800:   #633806;

    --ip-red50:      #FCEBEB;
    --ip-red400:     #E24B4A;
    --ip-red600:     #A32D2D;

    --ip-green50:    #EAF3DE;
    --ip-green600:   #3B6D11;
}

/* ── Page wrapper ──────────────────────────────────────────── */
.ip-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 16px 32px;
}
.ip-wrap {
    background: white;
    border-radius: 20px;
    border: 1px solid var(--ip-border);
    overflow: hidden;
}

/* ── Header ────────────────────────────────────────────────── */
.ip-header {
    padding: 20px 28px;
    border-bottom: 1px solid var(--ip-border);
    display: flex;
    align-items: center;
    gap: 14px;
}
.ip-back-btn {
    width: 38px; height: 38px;
    border: 1px solid var(--ip-border);
    border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--ip-muted);
    text-decoration: none;
    flex-shrink: 0;
    transition: border-color .2s, color .2s, background .2s;
}
.ip-back-btn:hover {
    border-color: var(--ip-navy2);
    color: var(--ip-navy2);
    background: var(--ip-navy3);
}
.ip-title {
    font-family: 'Lora', Georgia, serif;
    font-size: 22px;
    font-weight: 700;
    color: var(--ip-navy);
    margin: 0 0 3px;
    line-height: 1.2;
}
.ip-subtitle {
    font-size: 12px;
    color: var(--ip-faint);
    margin: 0;
}

/* ── Error box ─────────────────────────────────────────────── */
.ip-error-box {
    margin: 16px 28px 0;
    background: var(--ip-red50);
    border: 1px solid #F7C1C1;
    border-radius: 12px;
    padding: 14px 16px;
}
.ip-error-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--ip-red600);
    margin-bottom: 6px;
}
.ip-error-list {
    margin: 0;
    padding-left: 18px;
    font-size: 12px;
    color: var(--ip-red600);
}

/* ── Body ──────────────────────────────────────────────────── */
.ip-body { padding: 24px 28px; }

/* ── Grids ─────────────────────────────────────────────────── */
.ip-grid-3col {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 24px;
    align-items: start;
    margin-bottom: 24px;
}
.ip-col { display: flex; flex-direction: column; gap: 20px; }
.ip-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}
.ip-grid-rj {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}
.ip-grid-5 {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
}

/* ── Section cards ─────────────────────────────────────────── */
.ip-card {
    border-radius: var(--ip-radius-lg);
    border: 1px solid var(--ip-border);
    overflow: hidden;
}


.ip-card-head {
    padding: 10px 16px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 6px;
}
.ip-card-body { padding: 18px; }

/* Default card */
.ip-card--default .ip-card-head {
    background: var(--ip-navy3);
    color: var(--ip-muted);
    border-bottom: 1px solid var(--ip-border);
}

/* Blue card (Kompetensi) */
.ip-card--blue .ip-card-head {
    background: var(--ip-blue50);
    color: var(--ip-blue600);
    border-bottom: 1px solid #B5D4F4;
}
.ip-card--blue .ip-card-body { background: #fafcff; }
.ip-card--blue .ip-info-note { color: var(--ip-blue600); }

/* Teal card (Potensi) */
.ip-card--teal .ip-card-head {
    background: var(--ip-teal50);
    color: var(--ip-teal600);
    border-bottom: 1px solid #9FE1CB;
}
.ip-card--teal .ip-card-body { background: #f5fdf9; }
.ip-card--teal .ip-info-note { color: var(--ip-teal600); }

/* Amber card (Rekam Jejak) */
.ip-card--amber .ip-card-head {
    background: var(--ip-amber50);
    color: var(--ip-amber600);
    border-bottom: 1px solid #FAC775;
}
.ip-card--amber .ip-card-body { background: #fffcf5; }
.ip-card--amber .ip-info-note { color: var(--ip-amber600); }

/* ── Bobot tags ────────────────────────────────────────────── */
.ip-bobot-tag {
    margin-left: auto;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 10px;
    border-radius: 20px;
    letter-spacing: .04em;
}
.ip-bobot-tag--blue  { background: var(--ip-blue600);  color: white; }
.ip-bobot-tag--teal  { background: var(--ip-teal600);  color: white; }
.ip-bobot-tag--amber { background: var(--ip-amber600); color: white; }

/* Optional / required inline badges */
.ip-optional-badge {
    margin-left: auto;
    font-size: 10px;
    font-weight: 600;
    padding: 1px 8px;
    border-radius: 20px;
    background: rgba(15,39,68,.08);
    color: var(--ip-muted);
    text-transform: none;
    letter-spacing: 0;
}
.ip-req-badge {
    margin-left: auto;
    font-size: 10px;
    font-weight: 700;
    padding: 1px 8px;
    border-radius: 20px;
    background: var(--ip-red50);
    color: var(--ip-red600);
    text-transform: none;
    letter-spacing: 0;
    border: 1px solid #F7C1C1;
}

/* ── Info note ─────────────────────────────────────────────── */
.ip-info-note {
    font-size: 11px;
    font-weight: 600;
    margin-bottom: 14px;
    line-height: 1.6;
}

/* ── Fields ────────────────────────────────────────────────── */
.ip-field { margin-bottom: 14px; }
.ip-field:last-child { margin-bottom: 0; }

.ip-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: var(--ip-navy);
    margin-bottom: 5px;
}
.ip-req  { color: #dc2626; }
.ip-opt  { font-weight: 400; color: var(--ip-faint); font-size: 11px; }

.ip-input {
    width: 100%;
    font-size: 13px;
    padding: 9px 12px;
    border: 1px solid var(--ip-border);
    border-radius: var(--ip-radius);
    font-family: 'Sora', system-ui, sans-serif;
    outline: none;
    background: #f8f9fc;
    color: var(--ip-text);
    transition: border-color .15s, background .15s, box-shadow .15s;
    -moz-appearance: textfield;
    appearance: textfield;
}
.ip-input::-webkit-outer-spin-button,
.ip-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.ip-input:focus {
    border-color: var(--ip-navy2);
    background: white;
    box-shadow: 0 0 0 3px rgba(26,61,110,.07);
}

/* Validation states */
.ip-input.is-invalid {
    border-color: #dc2626;
    background: var(--ip-red50);
}
.ip-input.is-valid {
    border-color: #10b981;
    background: #f0fdf4;
}

.ip-err {
    font-size: 11px;
    color: #dc2626;
    margin: 4px 0 0;
}

/* ── Hint + warn pills ─────────────────────────────────────── */
.ip-hint {
    display: none;
    font-size: 11px;
    font-weight: 700;
    margin-top: 5px;
    padding: 3px 9px;
    border-radius: 6px;
}
.ip-hint--above  { background: var(--ip-teal50);  color: var(--ip-teal600); }
.ip-hint--sesuai { background: var(--ip-blue50);  color: var(--ip-blue600); }
.ip-hint--below  { background: var(--ip-red50);   color: var(--ip-red600); }

.ip-warn {
    display: none;
    font-size: 11px;
    font-weight: 700;
    margin-top: 5px;
    padding: 4px 9px;
    border-radius: 6px;
    background: var(--ip-red50);
    color: var(--ip-red600);
    border: 1px solid #F7C1C1;
}

/* per-field max-100 warning — injected by JS, same style */
.ip-max-warn {
    display: none;
    font-size: 11px;
    font-weight: 700;
    margin-top: 5px;
    padding: 3px 9px;
    border-radius: 6px;
    background: var(--ip-red50);
    color: var(--ip-red600);
    border: 1px solid #F7C1C1;
}

/* ── Preview section ───────────────────────────────────────── */
.ip-preview {
    background: var(--ip-navy3);
    border: 1px solid var(--ip-border);
    border-radius: var(--ip-radius-lg);
    padding: 20px;
    margin-bottom: 0;
}
.ip-preview-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: var(--ip-faint);
    margin-bottom: 16px;
}
.ip-preview-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}

/* Preview cards */
.ip-pcard {
    background: white;
    border-radius: 10px;
    border: 1px solid var(--ip-border);
    padding: 14px 12px;
    text-align: center;
}
.ip-pcard--total {
    border-width: 2px;
    border-color: var(--ip-navy);
}
.ip-pcard-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 8px;
}
.ip-pcard--blue  .ip-pcard-label { color: var(--ip-blue600); }
.ip-pcard--teal  .ip-pcard-label { color: var(--ip-teal600); }
.ip-pcard--amber .ip-pcard-label { color: var(--ip-amber600); }
.ip-pcard--total .ip-pcard-label { color: var(--ip-navy); }

.ip-pcard-val {
    font-size: 24px;
    font-weight: 800;
    font-family: 'Sora', system-ui, sans-serif;
    line-height: 1;
    transition: color .2s;
}
.ip-pcard--blue  .ip-pcard-val { color: var(--ip-blue600); }
.ip-pcard--teal  .ip-pcard-val { color: var(--ip-teal600); }
.ip-pcard--amber .ip-pcard-val { color: var(--ip-amber600); }
.ip-pcard--total .ip-pcard-val { color: var(--ip-navy); }

.ip-pcard-sub {
    font-size: 10px;
    color: var(--ip-faint);
    margin-top: 5px;
    line-height: 1.4;
}

.ip-kat-pill {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    margin-top: 6px;
    padding: 3px 10px;
    border-radius: 20px;
    background: var(--ip-navy3);
    color: var(--ip-faint);
    transition: background .2s, color .2s;
}

/* Box preview row */
.ip-box-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
}
.ip-box-sep {
    width: 1px;
    height: 48px;
    background: var(--ip-border);
    margin: 0 28px;
}
.ip-box-item { text-align: center; }
.ip-box-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--ip-navy);
    margin-bottom: 6px;
}
.ip-box-circle {
    width: 50px; height: 50px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 20px;
    font-family: 'Sora', system-ui, sans-serif;
    transition: all .2s;
}
.ip-box-circle--panrb {
    background: var(--ip-blue50);
    color: #1d4ed8;
    border: 1px solid #93c5fd;
}
.ip-box-circle--kemkeu {
    background: var(--ip-amber50);
    color: var(--ip-amber600);
    border: 1px solid #FAC775;
}

/* ── Footer ────────────────────────────────────────────────── */
.ip-footer {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    padding: 16px 28px;
    border-top: 1px solid var(--ip-border);
    background: var(--ip-navy3);
}
.ip-btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: transparent;
    color: var(--ip-muted);
    border: 1px solid var(--ip-border);
    border-radius: 12px;
    font-family: 'Sora', system-ui, sans-serif;
    font-size: 13px;
    font-weight: 700;
    padding: 10px 18px;
    text-decoration: none;
    cursor: pointer;
    transition: all .2s;
}
.ip-btn-cancel:hover {
    border-color: var(--ip-navy2);
    color: var(--ip-navy2);
    background: white;
}
.ip-btn-submit {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: var(--ip-navy);
    color: white;
    border: none;
    border-radius: 12px;
    font-family: 'Sora', system-ui, sans-serif;
    font-size: 13px;
    font-weight: 700;
    padding: 10px 22px;
    cursor: pointer;
    transition: background .2s, transform .1s;
}
.ip-btn-submit:hover  { background: var(--ip-navy2); }
.ip-btn-submit:active { transform: scale(.98); }
.ip-btn-submit:disabled {
    opacity: .6;
    cursor: not-allowed;
    transform: none;
}

/* ── Responsive ────────────────────────────────────────────── */
@media (max-width: 1100px) {
    .ip-grid-3col { grid-template-columns: 1fr 1fr; }
    .ip-grid-3col .ip-col:last-child { grid-column: 1 / -1; }
    .ip-grid-rj { grid-template-columns: repeat(5, 1fr); }
}
@media (max-width: 900px) {
    .ip-grid-3col { grid-template-columns: 1fr; }
    .ip-grid-3col .ip-col:last-child { grid-column: auto; }
    .ip-preview-grid { grid-template-columns: 1fr 1fr; }
    .ip-grid-5 { grid-template-columns: repeat(3, 1fr); }
    .ip-grid-rj { grid-template-columns: repeat(5, 1fr); }
}
@media (max-width: 680px) {
    .ip-body { padding: 16px; }
    .ip-header { padding: 16px; }
    .ip-footer { padding: 12px 16px; }
    .ip-error-box { margin: 12px 16px 0; }
    .ip-grid-3 { grid-template-columns: repeat(2, 1fr); }
    .ip-grid-5 { grid-template-columns: repeat(2, 1fr); }
    .ip-grid-rj { grid-template-columns: 1fr 1fr; }
    .ip-preview-grid { grid-template-columns: 1fr 1fr; }
    .ip-title { font-size: 18px; }
}
@media (max-width: 480px) {
    .ip-preview-grid { grid-template-columns: 1fr; }
    .ip-grid-5 { grid-template-columns: 1fr 1fr; }
    .ip-grid-rj { grid-template-columns: 1fr 1fr; }
    .ip-btn-cancel span { display: none; }
}
</style>
@endpush


{{-- ════════════════════════════════════════════════════════════
     SCRIPTS
════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
(function () {
    'use strict';

    /* ── Lookup tables ───────────────────────────────────────── */
    const BOX_PANRB = {
        rendah:   { 'Di bawah ekspektasi': 1, 'Sesuai ekspektasi': 2, 'Di atas ekspektasi': 4 },
        menengah: { 'Di bawah ekspektasi': 3, 'Sesuai ekspektasi': 5, 'Di atas ekspektasi': 7 },
        tinggi:   { 'Di bawah ekspektasi': 6, 'Sesuai ekspektasi': 8, 'Di atas ekspektasi': 9 },
    };
    const BOX_KEMKEU = {
        rendah:   { 'Di bawah ekspektasi': '1', 'Sesuai ekspektasi': '4', 'Di atas ekspektasi': '5' },
        menengah: { 'Di bawah ekspektasi': '2', 'Sesuai ekspektasi': '6', 'Di atas ekspektasi': '8' },
        tinggi:   { 'Di bawah ekspektasi': '3', 'Sesuai ekspektasi': '7', 'Di atas ekspektasi': '9' },
    };
    const PREDIKAT_MAP = {
        'Sangat Baik':       'Di atas ekspektasi',
        'Baik':              'Sesuai ekspektasi',
        'Butuh Perbaikan':   'Di bawah ekspektasi',
        'Kurang':            'Di bawah ekspektasi',
        'Sangat Kurang':     'Di bawah ekspektasi',
    };

    /* ── Reactive state ──────────────────────────────────────── */
    const state = { nkp: null, predikat: null };

    /* ── Pure helpers ────────────────────────────────────────── */
    const avg  = arr => arr.length ? arr.reduce((a, b) => a + b, 0) / arr.length : null;
    const fmt  = v   => v === null ? '—' : v.toFixed(2);
    const fnum = v   => v === null ? '—' : v;

    function getNumericVals(selector) {
        return [...document.querySelectorAll(selector)]
            .map(el => parseFloat(el.value))
            .filter(v => !isNaN(v));
    }

    function nkpToKat(raw) {
        const n = parseFloat(raw);
        if (isNaN(n) || n > 120) return null;
        if (n > 100) return 'Di atas ekspektasi';
        if (n >= 90) return 'Sesuai ekspektasi';
        return 'Di bawah ekspektasi';
    }

    function totalToTier(total) {
        if (total === null) return null;
        if (total >= 90) return 'tinggi';
        if (total >= 78) return 'menengah';
        return 'rendah';
    }

    const TIER_COLOR = {
        tinggi:   '#0F6E56',
        menengah: '#854F0B',
        rendah:   '#A32D2D',
    };
    const TIER_LABEL = {
        tinggi:   'Potensi Tinggi',
        menengah: 'Potensi Menengah',
        rendah:   'Potensi Rendah',
    };
    const TIER_BG = {
        tinggi:   '#E1F5EE',
        menengah: '#FAEEDA',
        rendah:   '#FCEBEB',
    };

    function hintClass(kat) {
        const map = {
            'Di atas ekspektasi': 'ip-hint--above',
            'Sesuai ekspektasi':  'ip-hint--sesuai',
            'Di bawah ekspektasi':'ip-hint--below',
        };
        return map[kat] || '';
    }

    function showHint(el, kat) {
        if (!kat) { el.style.display = 'none'; return; }
        el.textContent  = '→ ' + kat;
        el.className    = 'ip-hint ' + hintClass(kat);
        el.style.display = 'block';
    }

    /* ── Core recalculation ──────────────────────────────────── */
    function recalc() {
        /* Kompetensi (70%) */
        const teknis    = parseFloat(document.getElementById('kompTeknis').value);
        const mansoskul = parseFloat(document.getElementById('kompMansoskul').value);
        const nilaiK    = (!isNaN(teknis) && !isNaN(mansoskul))
            ? ((teknis + mansoskul) / 2) * 0.70
            : null;

        /* Potensi (20%) */
        const pVals  = getNumericVals('.ip-potensi');
        const nilaiP = pVals.length ? avg(pVals) * 0.20 : null;

        /* Rekam Jejak (10%) */
        const rVals  = getNumericVals('.ip-rj');
        const nilaiR = rVals.length ? avg(rVals) * 0.10 : null;

        /* Total */
        const hasAny = nilaiK !== null || nilaiP !== null || nilaiR !== null;
        const total  = hasAny ? (nilaiK ?? 0) + (nilaiP ?? 0) + (nilaiR ?? 0) : null;
        const tier   = totalToTier(total);

        /* Update cards */
        document.getElementById('prevKomp').textContent = fmt(nilaiK);
        document.getElementById('prevPot').textContent  = fmt(nilaiP);
        document.getElementById('prevRJ').textContent   = fmt(nilaiR);

        const totalEl = document.getElementById('prevTotal');
        const katEl   = document.getElementById('prevKat');
        totalEl.textContent  = fmt(total);
        totalEl.style.color  = tier ? TIER_COLOR[tier] : 'var(--ip-navy)';

        if (tier) {
            katEl.textContent      = TIER_LABEL[tier];
            katEl.style.background = TIER_BG[tier];
            katEl.style.color      = TIER_COLOR[tier];
        } else {
            katEl.textContent      = 'Belum dihitung';
            katEl.style.background = 'var(--ip-navy3)';
            katEl.style.color      = 'var(--ip-faint)';
        }

        /* Update box previews */
        const katP = PREDIKAT_MAP[state.predikat] || null;
        const katN = nkpToKat(state.nkp);

        const pEl = document.getElementById('boxPanrb');
        const kEl = document.getElementById('boxKemkeu');
        pEl.textContent = (tier && katP) ? (BOX_PANRB[tier]?.[katP] ?? '?') : '—';
        kEl.textContent = (tier && katN) ? (BOX_KEMKEU[tier]?.[katN] ?? '?') : '—';
    }

    /* ── NKP handler ─────────────────────────────────────────── */
    const nkpInput   = document.getElementById('nkpInput');
    const nkpHintEl  = document.getElementById('nkpHint');
    const nkpWarnEl  = document.getElementById('nkpWarn');

    nkpInput.addEventListener('input', function () {
        const n = parseFloat(this.value);

        if (!isNaN(n) && n > 120) {
            nkpWarnEl.style.display = 'block';
            nkpHintEl.style.display = 'none';
            this.classList.add('is-invalid');
            state.nkp = null;
            recalc();
            return;
        }

        nkpWarnEl.style.display = 'none';
        this.classList.remove('is-invalid');
        state.nkp = this.value;
        showHint(nkpHintEl, nkpToKat(this.value));
        recalc();
    });

    /* ── Predikat handler ────────────────────────────────────── */
    const predikatEl     = document.getElementById('predikatSelect');
    const predikatHintEl = document.getElementById('predikatHint');

    predikatEl.addEventListener('change', function () {
        state.predikat = this.value;
        showHint(predikatHintEl, PREDIKAT_MAP[this.value] || null);
        recalc();
    });

    /* ── Max-100 guard for Kompetensi, Potensi, Rekam Jejak ─── */
    function attachMax100Guard(selector) {
        document.querySelectorAll(selector).forEach(el => {
            /* Inject a warning element right after the input */
            const warn = document.createElement('div');
            warn.className = 'ip-max-warn';
            warn.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Nilai tidak boleh lebih dari 100!';
            el.insertAdjacentElement('afterend', warn);

            el.addEventListener('input', function () {
                const n = parseFloat(this.value);
                const over = !isNaN(n) && n > 100;

                if (over) {
                    /* Clamp the value hard — user cannot type beyond 100 */
                    this.value = 100;
                    warn.style.display = 'block';
                    this.classList.add('is-invalid');
                } else {
                    warn.style.display = 'none';
                    this.classList.remove('is-invalid');
                }
            });

            /* Also block on keyup for copy-paste edge cases */
            el.addEventListener('keyup', function () {
                const n = parseFloat(this.value);
                if (!isNaN(n) && n > 100) {
                    this.value = 100;
                }
            });
        });
    }

    attachMax100Guard('.ip-recalc[max="100"]');

    /* ── Recalc on every relevant input ─────────────────────── */
    document.querySelectorAll('.ip-recalc').forEach(el => {
        el.addEventListener('input', recalc);
    });

    /* ── Required-field validation UX ───────────────────────── */
    function validateField(el) {
        const empty = !el.value.trim();
        el.classList.toggle('is-invalid', empty);
        el.classList.toggle('is-valid', !empty);
        return !empty;
    }

    document.querySelectorAll('.ip-required').forEach(el => {
        /* Show red only after user has left the field */
        el.addEventListener('blur', () => validateField(el));
        /* Recover immediately while typing */
        el.addEventListener('input', () => {
            if (el.classList.contains('is-invalid')) validateField(el);
        });
    });

    /* ── Form submit ─────────────────────────────────────────── */
    document.getElementById('mainForm').addEventListener('submit', function (e) {
        let isValid = true;

        document.querySelectorAll('.ip-required').forEach(el => {
            if (!validateField(el)) isValid = false;
        });

        /* Block invalid NKP */
        const nkpVal = parseFloat(nkpInput.value);
        if (!isNaN(nkpVal) && nkpVal > 120) {
            nkpWarnEl.style.display = 'block';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            const firstErr = document.querySelector('.is-invalid');
            if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        /* Visual feedback on submit */
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
    });

    /* ── Init: restore old() values ──────────────────────────── */
    document.addEventListener('DOMContentLoaded', function () {
        if (nkpInput.value)      nkpInput.dispatchEvent(new Event('input'));
        if (predikatEl.value)    predikatEl.dispatchEvent(new Event('change'));
        recalc();
    });

})();
</script>
@endpush