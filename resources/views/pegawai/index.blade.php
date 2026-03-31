@extends('layouts.app')
@section('title', 'Dashboard - Pemetaan Talenta')
@section('topbar-title', $gridUe1 ? (\App\Models\Pegawai::UE1_SHORT[$gridUe1] ?? '') . ' — Pemetaan Talenta' : 'Pemetaan Talenta')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,500;0,600;1,400&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ── RESET & ROOT ─────────────────────────────────────────── */
:root {
    --ink:#0d1b2a; --pruss:#0f2744; --pruss-mid:#1a3d6e; --pruss-lt:#2a5298;
    --gold:#c9961a; --gold-lt:#f0c84e; --gold-bg:#fdf6e3;
    --jade:#0e7a5c; --jade-lt:#e4f5ef; --jade-m:#9FE1CB;
    --sienna:#b5530a; --sienna-lt:#fef0e4;
    --crimson:#c01b45; --crimson-lt:#fde8ef;
    --fog:#f4f6fb; --surface:#ffffff;
    --border:rgba(15,39,68,.10); --border-md:rgba(15,39,68,.18);
    --muted:#6b7a99; --subtle:#9aaac2;
    --red-solid:#dc2626; --red-soft:#fee2e2;
    --orange-dark:#ff5900; --orange-dark-soft:#ffbc75ab;
    --orange-light:#fc6900; --orange-light-soft:#fff7ed;
    --yellow-solid:#eab308; --yellow-soft:#fef9e3;
    --green-light:#10b981; --green-light-soft:#e8faf0;
    --green-dark:#01684b; --green-dark-soft:#21e288ad;
    --r-sm:10px; --r-md:16px; --r-lg:24px; --r-xl:32px;
    --header-h:56px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Sora',system-ui,sans-serif;font-size:15px;line-height:1.5;color:var(--ink);background:var(--fog);-webkit-font-smoothing:antialiased}

/* ── ANIMATIONS ───────────────────────────────────────────── */
@keyframes up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.a0{animation:up .5s cubic-bezier(.22,.68,0,1.1) both}
.a1{animation:up .5s .08s both}.a2{animation:up .5s .16s both}
.a3{animation:up .5s .24s both}.a4{animation:up .5s .32s both}
.a5{animation:up .5s .40s both}.a6{animation:up .5s .48s both}

/* ── LAYOUT ───────────────────────────────────────────────── */
.wrap{max-width:1440px;margin:0 auto;padding:0 24px}

/* ── PAGE HEADER ──────────────────────────────────────────── */
.phdr{
    display:grid;
    grid-template-columns:1fr auto;
    gap:20px;
    padding-bottom:28px;
    border-bottom:1px solid var(--border-md);
    margin-bottom:32px;
    align-items:end;
}
.phdr-left{}
.phdr-eye{font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--subtle);display:flex;align-items:center;gap:10px;margin-bottom:8px}
.phdr-eye::before{content:'';display:inline-block;width:28px;height:2px;background:var(--subtle);border-radius:2px;flex-shrink:0}
.phdr-title{font-family:'Lora',Georgia,serif;font-size:30px;font-weight:600;letter-spacing:-.02em;color:var(--pruss);margin-bottom:8px;line-height:1.2}
.phdr-sub{font-size:13px;color:var(--muted);display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.phdr-sub .sep{opacity:.4}
.phdr-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap;justify-content:flex-end}

/* ── UNIT SELECTOR FORM ───────────────────────────────────── */
.unit-form{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.unit-sel{
    font-family:'Sora',sans-serif;font-size:13px;font-weight:500;
    border:1px solid var(--border-md);border-radius:var(--r-md);
    padding:9px 14px;background:var(--surface);color:var(--pruss-mid);
    cursor:pointer;width:200px;transition:all .2s;
}
.unit-sel:focus{outline:none;border-color:var(--pruss-mid)}

/* ── BUTTONS ──────────────────────────────────────────────── */
.btn-primary{display:inline-flex;align-items:center;gap:8px;background:var(--pruss);color:white;border:none;border-radius:var(--r-md);font-family:'Sora',sans-serif;font-size:13px;font-weight:700;padding:10px 18px;cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap}
.btn-primary:hover{background:var(--pruss-mid);transform:translateY(-1px);color:white}
.btn-secondary{display:inline-flex;align-items:center;gap:8px;background:transparent;color:var(--muted);border:1px solid var(--border-md);border-radius:var(--r-md);font-family:'Sora',sans-serif;font-size:13px;font-weight:700;padding:9px 16px;cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap}
.btn-secondary:hover{border-color:var(--pruss-mid);background:#E6F1FB;color:var(--pruss-mid)}
.btn-icon{display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border:1px solid var(--border-md);border-radius:var(--r-md);background:var(--surface);color:var(--muted);cursor:pointer;transition:all .2s;font-size:1rem;text-decoration:none;flex-shrink:0}
.btn-icon:hover{border-color:var(--pruss-mid);color:var(--pruss-mid);background:#E6F1FB}

/* ── STAT CARDS ───────────────────────────────────────────── */
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:28px}
.stat{background:var(--surface);border-radius:var(--r-lg);border:1px solid var(--border-md);padding:20px 22px 18px;position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s;cursor:default}
.stat:hover{transform:translateY(-4px);box-shadow:0 12px 28px rgba(0,0,0,.08)}
.stat-bar{position:absolute;top:0;left:0;right:0;height:4px}
.stat-bg-icon{position:absolute;right:-8px;bottom:-8px;font-size:4.5rem;opacity:.04;pointer-events:none;transition:opacity .25s,transform .25s}
.stat:hover .stat-bg-icon{opacity:.08;transform:scale(1.08) rotate(-4deg)}
.stat-lbl{font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--muted);margin-bottom:10px}
.stat-val{font-family:'Sora',sans-serif;font-size:40px;font-weight:800;letter-spacing:-.03em;line-height:1;margin-bottom:8px}
.stat-hint{font-size:12px;color:var(--muted);display:flex;align-items:center;gap:6px}
.sv-blue{color:#185FA5}.sb-blue{background:linear-gradient(90deg,#185FA5,#85B7EB)}
.sv-teal{color:#0F6E56}.sb-teal{background:linear-gradient(90deg,#1D9E75,#5DCAA5)}
.sv-amber{color:#854F0B}.sb-amber{background:linear-gradient(90deg,#BA7517,#EF9F27)}
.sv-red{color:#A32D2D}.sb-red{background:linear-gradient(90deg,#E24B4A,#F09595)}

/* ── DUAL 9-BOX ───────────────────────────────────────────── */
.grids-row{display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:22px}

/* ── CARD ─────────────────────────────────────────────────── */
.card{background:var(--surface);border-radius:var(--r-lg);border:1px solid var(--border-md);overflow:hidden;transition:box-shadow .2s}
.card:hover{box-shadow:0 8px 24px rgba(0,0,0,.04)}
.card-hdr{padding:18px 22px 0;display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;gap:10px;flex-wrap:wrap}
.card-title{font-size:15px;font-weight:700;color:var(--ink);letter-spacing:-0.2px}
.card-tag{font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;padding:4px 10px;border-radius:40px;border:1px solid var(--border-md);color:var(--muted);background:var(--fog);white-space:nowrap}
.card-body{padding:0 22px 22px}
.sec-lbl{font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);margin-bottom:14px}

/* ── 9-BOX GRID ───────────────────────────────────────────── */
.nb-wrap{background:var(--fog);border-radius:var(--r-md);padding:16px;border:1px solid var(--border)}
.nb-grid{display:grid;grid-template-columns:44px repeat(3,1fr);gap:8px}
.nb-col-hdr{text-align:center;font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);padding:6px 4px;background:rgba(255,255,255,.8);border-radius:var(--r-sm);border:1px solid var(--border)}
.nb-row-lbl{font-size:9px;font-weight:600;color:var(--subtle);display:flex;align-items:center;justify-content:flex-end;text-align:right;padding-right:6px;line-height:1.3}
.nb-cell{border-radius:14px;border:1px solid transparent;min-height:90px;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:10px 6px 8px;text-align:center;cursor:pointer;transition:all .2s ease;position:relative;gap:4px}
.nb-cell:hover{transform:scale(1.04);box-shadow:0 6px 16px rgba(0,0,0,.1);z-index:3}
.nb-cell.is-active{box-shadow:0 0 0 3px currentColor;border-color:currentColor}
.cell-n{font-family:'Sora',sans-serif;font-size:48px;font-weight:800;line-height:1;letter-spacing:-.04em}
.cell-b{font-size:11px;font-weight:700;opacity:.6;margin-top:4px;letter-spacing:.04em}
.nb-axis{grid-column:2/5;text-align:center;font-size:9px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--subtle);padding-top:6px}
.nb-caption{text-align:center;font-size:11px;color:var(--subtle);margin-top:8px;font-style:italic}
.gc-red-solid{background:var(--red-soft);color:var(--red-solid);border-color:var(--red-solid)}
.gc-orange-dark{background:var(--orange-dark-soft);color:var(--orange-dark);border-color:var(--orange-dark)}
.gc-orange-light{background:var(--orange-light-soft);color:var(--orange-light);border-color:var(--orange-light)}
.gc-yellow{background:var(--yellow-soft);color:var(--yellow-solid);border-color:var(--yellow-solid)}
.gc-green-light{background:var(--green-light-soft);color:var(--green-light);border-color:var(--green-light)}
.gc-green-dark{background:var(--green-dark-soft);color:var(--green-dark);border-color:var(--green-dark)}
.gc-slate{background:var(--fog);color:var(--muted);border-color:var(--border-md)}

/* ── CHART SECTION ────────────────────────────────────────── */
.chart-row{display:grid;gap:18px;margin-bottom:22px}
.chart-row-4{grid-template-columns:repeat(4,1fr)}
.chart-row-2{grid-template-columns:1fr 1fr}
.chart-card{background:var(--surface);border-radius:var(--r-lg);border:1px solid var(--border-md);padding:18px;transition:box-shadow .2s}
.chart-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.04)}
.chart-title{font-size:13px;font-weight:700;color:var(--ink);margin-bottom:3px}
.chart-sub{font-size:11px;color:var(--muted);margin-bottom:14px;font-weight:500}
.chart-wrap{position:relative}
.leg{display:flex;flex-wrap:wrap;gap:7px;margin-top:10px}
.leg-item{display:flex;align-items:center;gap:5px;font-size:11px;color:var(--muted)}
.leg-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}

/* ── HORIZONTAL BAR ───────────────────────────────────────── */
.hbar-row{display:flex;align-items:center;gap:8px;margin-bottom:6px}
.hbar-lbl{font-size:11px;font-weight:700;color:var(--ink);flex-shrink:0;width:46px}
.hbar-track{flex:1;height:9px;background:var(--fog);border-radius:6px;overflow:hidden;min-width:0}
.hbar-fill{height:100%;border-radius:6px;transition:width .8s cubic-bezier(.22,.68,0,1.1)}
.hbar-val{font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--muted);flex-shrink:0;min-width:32px;text-align:right}
.hbar-pct{font-size:10px;color:var(--subtle);flex-shrink:0;width:28px;text-align:right}

/* ── UE1 BAR CHART ────────────────────────────────────────── */
.ue1-chart{height:130px;display:flex;align-items:flex-end;gap:5px;padding:0 4px}
.ue1-col-wrap{flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;height:100%}
.ue1-bar-area{flex:1;width:100%;display:flex;align-items:flex-end}
.ue1-fill{width:100%;background:#B5D4F4;border-radius:5px 5px 0 0;cursor:pointer;min-height:4px;transition:background .15s;position:relative}
.ue1-fill:hover{background:#378ADD}
.ue1-fill::after{content:attr(data-val);position:absolute;bottom:calc(100% + 5px);left:50%;transform:translateX(-50%);font-size:11px;font-weight:600;color:var(--ink);background:var(--surface);border:1px solid var(--border-md);border-radius:8px;padding:3px 8px;white-space:nowrap;opacity:0;pointer-events:none;transition:opacity .15s;z-index:5}
.ue1-fill:hover::after{opacity:1}
.ue1-lbl{font-size:9px;font-weight:700;color:var(--subtle);text-align:center;white-space:nowrap}

/* ── REF TABLES ───────────────────────────────────────────── */
.ref-row{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:28px}
.ref-tbl{width:100%;border-collapse:collapse;font-size:13px}
.ref-tbl th{font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--muted);padding:8px 10px;border-bottom:1px solid var(--border-md);text-align:left}
.ref-tbl td{padding:7px 10px;border-bottom:1px solid var(--border);color:var(--ink)}
.ref-tbl tr:last-child td{border-bottom:none}
.ref-tbl tr:hover td{background:var(--fog)}
.mono{font-family:'JetBrains Mono',monospace;font-weight:500}

/* ── BADGES ───────────────────────────────────────────────── */
.bdg{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:40px;font-size:11px;font-weight:600;white-space:nowrap;border:1px solid transparent}
.b-teal{background:#E1F5EE;color:#0F6E56;border-color:#9FE1CB}
.b-blue{background:#E6F1FB;color:#185FA5;border-color:#B5D4F4}
.b-red{background:#FCEBEB;color:#A32D2D;border-color:#F7C1C1}
.b-amber{background:#FAEEDA;color:#854F0B;border-color:#FAC775}
.b-gray{background:var(--fog);color:var(--muted);border-color:var(--border-md)}
.nkp-val{font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:700;display:block;margin-bottom:2px}
.nkp-kat{font-size:10px;font-weight:600;opacity:.75}
.box-b{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;font-family:'Sora',sans-serif;font-weight:800;font-size:13px;border:1px solid transparent}
.bb1,.bb2,.bb3,.bb4,.bb5,.bb6,.bb7,.bb8,.bb9{background:#dbeafe;color:#1d4ed8;border-color:#93c5fd}
.km-b{display:inline-flex;align-items:center;justify-content:center;min-width:38px;height:32px;padding:0 8px;border-radius:9px;font-weight:700;font-size:13px;background:#FAEEDA;color:#854F0B;border:1px solid #FAC775}

/* ── FILTER CHIPS ─────────────────────────────────────────── */
.chip{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:40px;font-size:11px;font-weight:700;border:1px solid var(--border-md);background:var(--fog);color:var(--muted)}

/* ── DATA TABLE CARD ──────────────────────────────────────── */
.tbl-card{background:var(--surface);border-radius:var(--r-lg);border:1px solid var(--border-md);overflow:hidden}

/* Table header toolbar */
.tbl-hdr{
    padding:18px 22px 0;
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:14px;
    gap:12px;
    flex-wrap:wrap;
}
.tbl-title{font-size:16px;font-weight:700;color:var(--ink)}

/* ── SEARCH BAR ───────────────────────────────────────────── */
.search-section{
    padding:0 22px 14px;
    display:flex;gap:10px;
    align-items:center;
    flex-wrap:wrap;
}
.search-wrap{
    position:relative;
    flex:0 0 auto;width:260px;
}
.search-ico{
    position:absolute;left:12px;top:50%;transform:translateY(-50%);
    color:var(--subtle);font-size:14px;pointer-events:none;
    transition:color .2s;
}
.search-inp{
    font-family:'Sora',sans-serif;font-size:13px;
    padding:9px 36px 9px 38px;
    border:1px solid var(--border-md);border-radius:var(--r-md);
    background:var(--fog);color:var(--ink);
    width:100%;outline:none;transition:all .2s;
}
.search-inp:focus{border-color:var(--pruss-mid);box-shadow:0 0 0 4px rgba(26,61,110,.08);background:white}
.search-inp::placeholder{color:var(--subtle)}
.live-filters{display:flex;gap:8px;align-items:center;flex-wrap:wrap;flex:1;min-width:0}
@keyframes spin{to{transform:translateY(-50%) rotate(360deg)}}
@keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
.tbl-fade{animation:fadeIn .2s ease both}
#tableBody.loading tr,#mobileCards.loading{opacity:.4;pointer-events:none;transition:opacity .15s}

/* Column filter selects */
.col-sel{
    font-family:'Sora',sans-serif;font-size:11px;
    padding:6px 8px;
    border:1px solid var(--border-md);border-radius:var(--r-sm);
    background:var(--fog);color:var(--muted);
    cursor:pointer;transition:all .2s;width:100%;margin-top:5px;
}
.col-sel:focus{outline:none;border-color:var(--pruss-mid)}
.col-sel.active{border-color:var(--pruss-mid);background:#E6F1FB;color:#185FA5;font-weight:700}

/* ── FILTER BAR (inline row filters) ─────────────────────── */
.filter-bar{
    padding:0 22px 14px;
    display:flex;gap:8px;flex-wrap:wrap;align-items:center;
}
.filter-group{display:flex;align-items:center;gap:6px}
.filter-label{font-size:11px;font-weight:600;color:var(--subtle);white-space:nowrap}
.filter-sel{
    font-family:'Sora',sans-serif;font-size:12px;
    padding:7px 12px;
    border:1px solid var(--border-md);border-radius:var(--r-md);
    background:var(--fog);color:var(--muted);cursor:pointer;transition:all .2s;
}
.filter-sel:focus{outline:none;border-color:var(--pruss-mid)}
.filter-sel.active{border-color:var(--pruss-mid);background:#E6F1FB;color:#185FA5;font-weight:700}

/* ── MAIN TABLE ───────────────────────────────────────────── */
.main-tbl{width:100%;border-collapse:collapse;font-size:13px}
.main-tbl thead th{
    padding:10px 14px 8px;
    font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;
    color:var(--muted);border-bottom:1px solid var(--border-md);
    background:var(--fog);text-align:left;vertical-align:top;white-space:nowrap;
}
.main-tbl thead th.th-gold{background:#fdf6e3}
.main-tbl tbody td{padding:12px 14px;border-bottom:1px solid var(--border);vertical-align:middle}
.main-tbl tbody td.td-gold{background:#fffbf0}
.main-tbl tbody tr:hover td{background:#f7f9fe}
.main-tbl tbody tr:hover td.td-gold{background:#fef8e0}
.pname{font-weight:700;font-size:13px;margin-bottom:1px}
.pnip{font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--subtle)}
.row-n{font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--border-md)}
.del-btn{background:none;border:none;padding:5px 7px;border-radius:var(--r-sm);color:var(--border-md);cursor:pointer;font-size:.9rem;transition:all .2s}
.del-btn:hover{color:#A32D2D;background:#FCEBEB}

/* ── MOBILE CARDS ─────────────────────────────────────────── */
.m-card{background:var(--surface);border-radius:var(--r-lg);border:1px solid var(--border-md);border-left-width:4px;padding:14px 16px 12px;transition:all .2s}
.m-card:hover{border-color:rgba(15,39,68,.3);box-shadow:0 4px 12px rgba(0,0,0,.05)}

/* ── PAGINATION ───────────────────────────────────────────── */
.pag{padding:14px 22px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
.pag-info{font-size:12px;color:var(--muted)}
.pagination{margin:0;gap:4px}
.pagination .page-link{padding:6px 12px;font-size:12px;font-weight:700;font-family:'Sora',sans-serif;border-radius:9px;border:1px solid var(--border-md);color:var(--muted);transition:all .2s}
.pagination .page-link:hover{background:#E6F1FB;border-color:var(--pruss-mid);color:var(--pruss-mid)}
.pagination .page-item.active .page-link{background:var(--pruss);border-color:var(--pruss);color:white}

/* ── EMPTY STATE ──────────────────────────────────────────── */
.empty{text-align:center;padding:60px 32px}
.empty-ico{width:72px;height:72px;border-radius:50%;background:var(--fog);border:1px solid var(--border-md);display:inline-flex;align-items:center;justify-content:center;font-size:1.8rem;color:var(--subtle);margin-bottom:16px}
.empty-txt{font-size:14px;color:var(--muted);margin-bottom:20px;line-height:1.6}

/* ── UTILITY ──────────────────────────────────────────────── */
.mb-4{margin-bottom:24px}
.gap-2{gap:8px}
.gap-3{gap:12px}

/* ════════════════════════════════════════════════
   RESPONSIVE BREAKPOINTS
   ════════════════════════════════════════════════ */

/* ── Tablet Landscape (≤1280px) ─────────────────────────── */
@media(max-width:1280px){
    .chart-row-4{grid-template-columns:repeat(2,1fr)}
    .ref-row{grid-template-columns:repeat(2,1fr)}
}

/* ── Tablet Portrait (≤1024px) ──────────────────────────── */
@media(max-width:1024px){
    .stats{grid-template-columns:repeat(2,1fr);gap:14px}
    .grids-row{grid-template-columns:1fr}
    .nb-grid{grid-template-columns:40px repeat(3,1fr);gap:5px}
    .cell-n{font-size:38px}
    .nb-col-hdr{font-size:9px;padding:5px 3px}
    .nb-row-lbl{font-size:8px}
    .phdr-title{font-size:26px}
    .stat-val{font-size:34px}
}

/* ── Mobile Large (≤767px) ──────────────────────────────── */
@media(max-width:767px){
    .wrap{padding:0 14px}

    /* Header */
    .phdr{
        grid-template-columns:1fr;
        gap:16px;
        padding-bottom:20px;
        margin-bottom:22px;
    }
    .phdr-title{font-size:22px}
    .phdr-eye{font-size:10px}
    .phdr-sub{font-size:12px;gap:8px}
    .phdr-actions{
        width:100%;
        justify-content:flex-start;
        gap:8px;
    }
    .unit-form{width:100%}
    .unit-sel{width:100%;flex:1;min-width:0}

    /* Stats */
    .stats{grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:20px}
    .stat{padding:14px 16px 12px}
    .stat-val{font-size:28px}
    .stat-bg-icon{font-size:3.5rem}
    .stat-lbl{font-size:10px;margin-bottom:8px}
    .stat-hint{font-size:11px}

    /* Dual grid */
    .grids-row{grid-template-columns:1fr;gap:14px;margin-bottom:14px}
    .nb-wrap{padding:12px}
    .nb-grid{grid-template-columns:36px repeat(3,1fr);gap:4px}
    .cell-n{font-size:28px}
    .cell-b{font-size:8px}
    .nb-col-hdr{font-size:8px;padding:4px 2px}
    .nb-row-lbl{font-size:8px;padding-right:4px}
    .nb-caption{font-size:10px}

    /* Charts */
    .chart-row-4{grid-template-columns:1fr 1fr}
    .chart-row-2{grid-template-columns:1fr}
    .chart-card{padding:14px}
    .chart-title{font-size:12px}
    .chart-sub{font-size:10px;margin-bottom:10px}

    /* Ref tables */
    .ref-row{grid-template-columns:1fr;gap:12px;margin-bottom:20px}

    /* Table card */
    .tbl-hdr{padding:14px 16px 0;flex-direction:column;gap:10px}
    .tbl-title{font-size:15px}
    .search-section{padding:0 16px 12px;flex-wrap:wrap;gap:8px}
    .search-wrap{width:100%;flex:0 0 100%}
    .live-filters{flex-wrap:wrap;gap:6px}
    .live-filters .filter-sel{flex:1;min-width:130px}

    /* Mobile cards list */
    .mobile-cards-list{padding:0 14px 16px;display:flex;flex-direction:column;gap:10px}
    .m-card{padding:12px 14px 10px}
    .pname{font-size:13px}
    .pnip{font-size:11px}

    /* Pagination */
    .pag{padding:12px 16px;flex-direction:column;align-items:flex-start;gap:8px}

    /* Card body */
    .card-hdr{padding:14px 16px 0;margin-bottom:10px}
    .card-body{padding:0 16px 16px}

    /* UE1 chart */
    .ue1-chart{height:100px;gap:3px}
    .ue1-lbl{font-size:8px}

    /* Empty */
    .empty{padding:40px 16px}
}

/* ── Mobile Small (≤480px) ──────────────────────────────── */
@media(max-width:480px){
    .stats{grid-template-columns:1fr 1fr;gap:8px}
    .stat-val{font-size:26px}
    .chart-row-4{grid-template-columns:1fr}
    .phdr-title{font-size:20px}

    /* 9-box even smaller */
    .nb-grid{grid-template-columns:30px repeat(3,1fr);gap:3px}
    .cell-n{font-size:22px}
    .nb-col-hdr{font-size:7px;padding:3px 2px;border-radius:6px}
    .nb-row-lbl{font-size:7px}
}
</style>
@endpush

@section('content')
@php
    $user     = auth()->user();
    $canAll   = $user->canAccessAllUnits();
    $isOp     = $user->isOperator();
    $topCount = ($boxCounts[9]??0)+($boxCounts[8]??0)+($boxCounts[7]??0);
    $midCount = ($boxCounts[5]??0)+($boxCounts[4]??0)+($boxCounts[2]??0);
    $lowCount = ($boxCounts[1]??0)+($boxCounts[3]??0)+($boxCounts[6]??0);
    $cfBox     = request('box');
    $cfKinerja = request('kinerja_filter');
    $cfPotensi = request('potensi_filter');
    $cfUe1     = request('ue1');
    $maxUe1    = max(array_values(array_map(fn($c)=>$ue1Counts[$c]??0, array_keys(\App\Models\Pegawai::UE1_SHORT)))+[1]);
    $total     = max($gridTotal, 1);

    $potTinggi   = ($boxCounts[6]??0)+($boxCounts[8]??0)+($boxCounts[9]??0);
    $potMenengah = ($boxCounts[3]??0)+($boxCounts[5]??0)+($boxCounts[7]??0);
    $potRendah   = ($boxCounts[1]??0)+($boxCounts[2]??0)+($boxCounts[4]??0);

    $kinAtas   = $topCount;
    $kinSesuai = $midCount;
    $kinBawah  = $lowCount;

    $kmKinAtas   = ($boxKemenkeuCounts['5']??0)+($boxKemenkeuCounts['8']??0)+($boxKemenkeuCounts['9']??0);
    $kmKinSesuai = ($boxKemenkeuCounts['4']??0)+($boxKemenkeuCounts['6']??0)+($boxKemenkeuCounts['7']??0);
    $kmKinBawah  = ($boxKemenkeuCounts['1']??0)+($boxKemenkeuCounts['2']??0)+($boxKemenkeuCounts['3']??0);

    $kompTinggi   = ($boxKemenkeuCounts['3']??0)+($boxKemenkeuCounts['7']??0)+($boxKemenkeuCounts['9']??0);
    $kompMenengah = ($boxKemenkeuCounts['2']??0)+($boxKemenkeuCounts['6']??0)+($boxKemenkeuCounts['8']??0);
    $kompRendah   = ($boxKemenkeuCounts['1']??0)+($boxKemenkeuCounts['4']??0)+($boxKemenkeuCounts['5']??0);

    $maxBox    = max(array_values($boxCounts)+[1]);
    $maxKmBox  = max(array_values($boxKemenkeuCounts)+[1]);
@endphp

<div class="wrap">

{{-- ════════════════════════════════════════════════
     PAGE HEADER
     ════════════════════════════════════════════════ --}}
<div class="phdr a0">
    <div class="phdr-left">
        <div class="phdr-eye">KEMENTERIAN KEUANGAN REPUBLIK INDONESIA</div>
        <h1 class="phdr-title">
            @if($isOp){{ \App\Models\Pegawai::UE1_LIST[$user->ue1] ?? '' }}
            @elseif($gridUe1){{ \App\Models\Pegawai::UE1_LIST[$gridUe1] ?? '' }}
            @else Pemetaan Talenta @endif
        </h1>
        <div class="phdr-sub">
            <i class="bi bi-calendar3"></i>
            <span>{{ now()->locale('id')->translatedFormat('d F Y') }}</span>            <span class="sep">·</span>
            <i class="bi bi-people"></i>
            <strong style="color:var(--ink)">{{ number_format($gridTotal) }}</strong>
            <span>Pegawai Terpetakan</span>
            @if(!$isOp&&!$gridUe1)
                <span class="sep">·</span><span>Seluruh Unit Eselon I</span>
            @endif
        </div>
    </div>

    <div class="phdr-actions">
        @if($canAll)
        <form method="GET" action="{{ route('pegawai.index') }}" class="unit-form">
            @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
            @if(request('box'))<input type="hidden" name="box" value="{{ request('box') }}">@endif
            @if(request('ue1'))<input type="hidden" name="ue1" value="{{ request('ue1') }}">@endif
            @if(request('kinerja_filter'))<input type="hidden" name="kinerja_filter" value="{{ request('kinerja_filter') }}">@endif
            @if(request('potensi_filter'))<input type="hidden" name="potensi_filter" value="{{ request('potensi_filter') }}">@endif
            <select name="grid_ue1" class="unit-sel" onchange="this.form.submit()">
                <option value="">Seluruh Unit Eselon I</option>
                @foreach(\App\Models\Pegawai::UE1_LIST as $code=>$name)
                    <option value="{{ $code }}" {{ $gridUe1==$code?'selected':'' }}>
                        {{ $code }} — {{ \App\Models\Pegawai::UE1_SHORT[$code] }}
                    </option>
                @endforeach
            </select>
            @if($gridUe1)
                <a href="{{ route('pegawai.index',array_filter(request()->except('grid_ue1'))) }}" class="btn-icon">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
        @endif
        <button onclick="downloadPDF()" class="btn-primary">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span class="d-none d-sm-inline">Unduh PDF</span>
            <span class="d-sm-none">PDF</span>
        </button>
    </div>
</div>

<div id="pdfArea">

{{-- ════════════════════════════════════════════════
     STAT CARDS
     ════════════════════════════════════════════════ --}}
<div class="stats a1">
    <div class="stat">
        <div class="stat-bar sb-blue"></div>
        <i class="bi bi-people-fill stat-bg-icon"></i>
        <div class="stat-lbl">Total Pegawai</div>
        <div class="stat-val sv-blue">{{ number_format($gridTotal) }}</div>
        <div class="stat-hint"><i class="bi bi-bar-chart-steps"></i> Keseluruhan data</div>
    </div>
    <div class="stat">
        <div class="stat-bar sb-teal"></div>
        <i class="bi bi-star-fill stat-bg-icon"></i>
        <div class="stat-lbl">Kinerja &amp; Potensi Tinggi</div>
        <div class="stat-val sv-teal">{{ number_format($topCount) }}</div>
        <div class="stat-hint"><i class="bi bi-arrow-up-circle"></i> Box 7, 8, 9</div>
    </div>
    <div class="stat">
        <div class="stat-bar sb-amber"></div>
        <i class="bi bi-diamond-fill stat-bg-icon"></i>
        <div class="stat-lbl">Kinerja &amp; Potensi Menengah</div>
        <div class="stat-val sv-amber">{{ number_format($midCount) }}</div>
        <div class="stat-hint"><i class="bi bi-dash-circle"></i> Box 2, 4, 5</div>
    </div>
    <div class="stat">
        <div class="stat-bar sb-red"></div>
        <i class="bi bi-exclamation-triangle-fill stat-bg-icon"></i>
        <div class="stat-lbl">Perlu Pengembangan</div>
        <div class="stat-val sv-red">{{ number_format($lowCount) }}</div>
        <div class="stat-hint"><i class="bi bi-arrow-down-circle"></i> Box 1, 3, 6</div>
    </div>
</div>

{{-- ════════════════════════════════════════════════
     DUAL 9-BOX GRID
     ════════════════════════════════════════════════ --}}
<div class="grids-row a2">
    {{-- PAN-RB --}}
    <div class="card">
        <div class="card-hdr">
            <div class="card-title">9‑Box PAN‑RB</div>
            <span class="card-tag">PermenPAN‑RB 25/2025</span>
        </div>
        <div class="card-body">
            <div class="nb-wrap">
                <div class="nb-grid">
                    <div></div>
                    <div class="nb-col-hdr">Potensi Rendah</div>
                    <div class="nb-col-hdr">Potensi Menengah</div>
                    <div class="nb-col-hdr">Potensi Tinggi</div>
    
                    <div class="nb-row-lbl">Kinerja<br>Di Atas</div>
                    <div class="nb-cell gc-orange-light">
                        <div class="cell-n">{{ number_format($boxCounts[4]??0) }}</div>
                        <div class="cell-b">BOX 4</div>
                    </div>
                    <div class="nb-cell gc-green-light">
                        <div class="cell-n">{{ number_format($boxCounts[7]??0) }}</div>
                        <div class="cell-b">BOX 7</div>
                    </div>
                    <div class="nb-cell gc-green-dark">
                        <div class="cell-n">{{ number_format($boxCounts[9]??0) }}</div>
                        <div class="cell-b">BOX 9</div>
                    </div>
    
                    <div class="nb-row-lbl">Kinerja<br>Sesuai</div>
                    <div class="nb-cell gc-orange-dark">
                        <div class="cell-n">{{ number_format($boxCounts[2]??0) }}</div>
                        <div class="cell-b">BOX 2</div>
                    </div>
                    <div class="nb-cell gc-orange-light">
                        <div class="cell-n">{{ number_format($boxCounts[5]??0) }}</div>
                        <div class="cell-b">BOX 5</div>
                    </div>
                    <div class="nb-cell gc-green-light">
                        <div class="cell-n">{{ number_format($boxCounts[8]??0) }}</div>
                        <div class="cell-b">BOX 8</div>
                    </div>
    
                    <div class="nb-row-lbl">Kinerja<br>Di Bawah</div>
                    <div class="nb-cell gc-red-solid">
                        <div class="cell-n">{{ number_format($boxCounts[1]??0) }}</div>
                        <div class="cell-b">BOX 1</div>
                    </div>
                    <div class="nb-cell gc-orange-dark">
                        <div class="cell-n">{{ number_format($boxCounts[3]??0) }}</div>
                        <div class="cell-b">BOX 3</div>
                    </div>
                    <div class="nb-cell gc-yellow">
                        <div class="cell-n">{{ number_format($boxCounts[6]??0) }}</div>
                        <div class="cell-b">BOX 6</div>
                    </div>
    
                    <div></div>
                    <div class="nb-axis">← Potensi →</div>
                </div>
            </div>
            <p class="nb-caption">↕ Kinerja (Predikat) · ↔ Potensi</p>
        </div>
    </div>

    {{-- KEMENKEU --}}
    <div class="card">
        <div class="card-hdr">
            <div class="card-title">9‑Box Kemenkeu</div>
            <span class="card-tag" style="background:#fdf6e3;color:#854F0B;border-color:#FAC775">PMK 38/2025</span>
        </div>
        <div class="card-body">
            <div class="nb-wrap" style="background:#fffcf5;border-color:#FAC77540">
                <div class="nb-grid">
                    <div></div>
                    <div class="nb-col-hdr">Kinerja Di Bawah</div>
                    <div class="nb-col-hdr">Kinerja Sesuai</div>
                    <div class="nb-col-hdr">Kinerja Di Atas</div>

                    <div class="nb-row-lbl">Kompetensi<br>Tinggi</div>
                    <div class="nb-cell gc-orange-dark"><div class="cell-n">{{ number_format($boxKemenkeuCounts['3']??0) }}</div><div class="cell-b">BOX 3</div></div>
                    <div class="nb-cell gc-green-light"><div class="cell-n">{{ number_format($boxKemenkeuCounts['7']??0) }}</div><div class="cell-b">BOX 7</div></div>
                    <div class="nb-cell gc-green-dark"><div class="cell-n">{{ number_format($boxKemenkeuCounts['9']??0) }}</div><div class="cell-b">BOX 9</div></div>

                    <div class="nb-row-lbl">Kompetensi<br>Menengah</div>
                    <div class="nb-cell gc-orange-dark"><div class="cell-n">{{ number_format($boxKemenkeuCounts['2']??0) }}</div><div class="cell-b">BOX 2</div></div>
                    <div class="nb-cell gc-yellow"><div class="cell-n">{{ number_format($boxKemenkeuCounts['6']??0) }}</div><div class="cell-b">BOX 6</div></div>
                    <div class="nb-cell gc-green-light"><div class="cell-n">{{ number_format($boxKemenkeuCounts['8']??0) }}</div><div class="cell-b">BOX 8</div></div>

                    <div class="nb-row-lbl">Kompetensi<br>Rendah</div>
                    <div class="nb-cell gc-red-solid"><div class="cell-n">{{ number_format($boxKemenkeuCounts['1']??0) }}</div><div class="cell-b">BOX 1</div></div>
                    <div class="nb-cell gc-orange-light"><div class="cell-n">{{ number_format($boxKemenkeuCounts['4']??0) }}</div><div class="cell-b">BOX 4</div></div>
                    <div class="nb-cell gc-orange-light"><div class="cell-n">{{ number_format($boxKemenkeuCounts['5']??0) }}</div><div class="cell-b">BOX 5</div></div>

                    <div></div>
                    <div class="nb-axis" style="color:#854F0B">← Kinerja (NKP) →</div>
                </div>
            </div>
            <p class="nb-caption">↕ Kompetensi · ↔ Kinerja (NKP)</p>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════
     ROW 1: 4 DONUT CHARTS
     ════════════════════════════════════════════════ --}}
<div class="chart-row chart-row-4 a3">
    <div class="chart-card">
        <div class="chart-title">Distribusi Kinerja PAN-RB</div>
        <div class="chart-sub">Berdasarkan predikat kinerja</div>
        <div class="chart-wrap" style="height:130px"><canvas id="cKinPanrb"></canvas></div>
        <div class="leg">
            <div class="leg-item"><div class="leg-dot" style="background:#0F6E56"></div>Di Atas ({{ number_format($kinAtas) }})</div>
            <div class="leg-item"><div class="leg-dot" style="background:#185FA5"></div>Sesuai ({{ number_format($kinSesuai) }})</div>
            <div class="leg-item"><div class="leg-dot" style="background:#A32D2D"></div>Di Bawah ({{ number_format($kinBawah) }})</div>
        </div>
    </div>

    <div class="chart-card" style="background:#fffcf5;border-color:#FAC77440">
        <div class="chart-title" style="color:#854F0B">Distribusi Kinerja Kemenkeu</div>
        <div class="chart-sub">Berdasarkan nilai NKP</div>
        <div class="chart-wrap" style="height:130px"><canvas id="cKinKemkeu"></canvas></div>
        <div class="leg">
            <div class="leg-item"><div class="leg-dot" style="background:#0F6E56"></div>Atas NKP>100 ({{ number_format($kmKinAtas) }})</div>
            <div class="leg-item"><div class="leg-dot" style="background:#c9961a"></div>Sesuai 90-100 ({{ number_format($kmKinSesuai) }})</div>
            <div class="leg-item"><div class="leg-dot" style="background:#A32D2D"></div>Bawah <90 ({{ number_format($kmKinBawah) }})</div>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-title">Distribusi Potensial</div>
        <div class="chart-sub">Sumbu ↔ PAN-RB</div>
        <div class="chart-wrap" style="height:130px"><canvas id="cPotensial"></canvas></div>
        <div class="leg">
            <div class="leg-item"><div class="leg-dot" style="background:#0F6E56"></div>Tinggi ({{ number_format($potTinggi) }})</div>
            <div class="leg-item"><div class="leg-dot" style="background:#BA7517"></div>Menengah ({{ number_format($potMenengah) }})</div>
            <div class="leg-item"><div class="leg-dot" style="background:#A32D2D"></div>Rendah ({{ number_format($potRendah) }})</div>
        </div>
    </div>

    <div class="chart-card" style="background:#fffcf5;border-color:#FAC77440">
        <div class="chart-title" style="color:#854F0B">Distribusi Kompetensi</div>
        <div class="chart-sub">Sumbu ↕ Kemenkeu</div>
        <div class="chart-wrap" style="height:130px"><canvas id="cKompetensi"></canvas></div>
        <div class="leg">
            <div class="leg-item"><div class="leg-dot" style="background:#0F6E56"></div>Tinggi ({{ number_format($kompTinggi) }})</div>
            <div class="leg-item"><div class="leg-dot" style="background:#c9961a"></div>Menengah ({{ number_format($kompMenengah) }})</div>
            <div class="leg-item"><div class="leg-dot" style="background:#A32D2D"></div>Rendah ({{ number_format($kompRendah) }})</div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════
     ROW 2: BOX DISTRIBUTION BARS
     ════════════════════════════════════════════════ --}}
<div class="chart-row chart-row-2 a4">
    <div class="chart-card">
        <div class="chart-title">Distribusi per Box PAN‑RB</div>
        <div class="chart-sub">PermenPAN-RB 25/2025 · Box 1–9</div>
        @php
            $panrbColors = ['#dc2626','#ea580c','#b45309','#0d7a52','#185FA5','#6d28d9','#1d4ed8','#047857','#065f46'];
        @endphp
        <div style="display:flex;flex-direction:column;gap:6px;margin-top:4px">
            @for($i=1;$i<=9;$i++)
            @php $pct = $maxBox > 0 ? round(($boxCounts[$i]??0)/$maxBox*100) : 0; @endphp
            <div class="hbar-row">
                <span class="hbar-lbl">Box {{ $i }}</span>
                <div class="hbar-track"><div class="hbar-fill" style="width:{{ $pct }}%;background:{{ $panrbColors[$i-1] }}cc"></div></div>
                <span class="hbar-val">{{ number_format($boxCounts[$i]??0) }}</span>
                <span class="hbar-pct">{{ $total>0 ? round(($boxCounts[$i]??0)/$total*100) : 0 }}%</span>
            </div>
            @endfor
        </div>
    </div>

    <div class="chart-card" style="background:#fffcf5;border-color:#FAC77440">
        <div class="chart-title" style="color:#854F0B">Distribusi per Box Kemenkeu</div>
        <div class="chart-sub">PMK 38/2025 · Box 1–9</div>
        @php
            $kmOrder  = ['1','2','3','4','5','6','7','8','9'];
            $kmColors = ['#dc2626','#ea580c','#b45309','#eab308','#0d7a52','#c9961a','#185FA5','#047857','#065f46'];
        @endphp
        <div style="display:flex;flex-direction:column;gap:6px;margin-top:4px">
            @foreach($kmOrder as $ki => $rom)
            @php $pct = $maxKmBox > 0 ? round(($boxKemenkeuCounts[$rom]??0)/$maxKmBox*100) : 0; @endphp
            <div class="hbar-row">
                <span class="hbar-lbl" style="color:#854F0B">Box {{ $rom }}</span>
                <div class="hbar-track"><div class="hbar-fill" style="width:{{ $pct }}%;background:{{ $kmColors[$ki] }}cc"></div></div>
                <span class="hbar-val">{{ number_format($boxKemenkeuCounts[$rom]??0) }}</span>
                <span class="hbar-pct">{{ $total>0 ? round(($boxKemenkeuCounts[$rom]??0)/$total*100) : 0 }}%</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════
     UE1 BAR CHART
     ════════════════════════════════════════════════ --}}
@if($canAll)
<div class="card a5 mb-4">
    <div class="card-hdr">
        <div class="card-title">Distribusi Pegawai per Unit Eselon I</div>
        <span class="card-tag">{{ count(\App\Models\Pegawai::UE1_SHORT) }} Unit Kerja</span>
    </div>
    <div class="card-body">
        <div class="ue1-chart">
            @foreach(\App\Models\Pegawai::UE1_SHORT as $code=>$short)
            @php $val=$ue1Counts[$code]??0; $pct=$maxUe1>0?round($val/$maxUe1*100):0; @endphp
            <div class="ue1-col-wrap">
                <div class="ue1-bar-area">
                    <div class="ue1-fill" style="height:{{ max(6,$pct) }}%" data-val="{{ number_format($val) }} pegawai"></div>
                </div>
                <div class="ue1-lbl">{{ $short }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

</div>{{-- /#pdfArea --}}

{{-- ════════════════════════════════════════════════
     REFERENCE TABLES
     ════════════════════════════════════════════════ --}}
<div class="a5 mb-4">
    <button class="btn btn-sm w-100 d-md-none mb-3" type="button"
            data-bs-toggle="collapse" data-bs-target="#refPanel"
            style="background:var(--fog);color:var(--muted);font-size:12px;font-weight:700;border-radius:var(--r-md);padding:10px;border:1px solid var(--border-md);font-family:'Sora',sans-serif">
        <i class="bi bi-table me-2"></i>Lihat Tabel Referensi
    </button>
    <div class="collapse d-md-block" id="refPanel">
        <div class="ref-row">
            <div class="card" style="padding:16px 18px">
                <div class="sec-lbl">Kategori Kinerja</div>
                <table class="ref-tbl">
                    <thead><tr><th>Predikat</th><th>Kategori</th><th>NKP</th></tr></thead>
                    <tbody>
                        <tr><td>Sangat Baik</td><td><span class="bdg b-teal">Di atas</span></td><td class="mono" style="font-size:11px">&gt; 100</td></tr>
                        <tr><td>Baik</td><td><span class="bdg b-blue">Sesuai</span></td><td class="mono" style="font-size:11px">90 – 100</td></tr>
                        <tr><td>Butuh Perbaikan</td><td><span class="bdg b-red">Di bawah</span></td><td class="mono" style="font-size:11px;color:var(--muted)">&lt; 90</td></tr>
                        <tr><td>Kurang</td><td><span class="bdg b-red">Di bawah</span></td><td class="mono" style="font-size:11px;color:var(--muted)">&lt; 90</td></tr>
                        <tr><td>Sangat Kurang</td><td><span class="bdg b-red">Di bawah</span></td><td class="mono" style="font-size:11px;color:var(--muted)">&lt; 90</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="card" style="padding:16px 18px">
                <div class="sec-lbl">Box PAN‑RB (1–9)</div>
                <table class="ref-tbl">
                    <thead><tr><th>Box</th><th>Jumlah</th><th>%</th></tr></thead>
                    <tbody>
                        @for($i=1;$i<=9;$i++)
                        <tr>
                            <td><span class="box-b bb{{$i}}">{{$i}}</span></td>
                            <td class="mono">{{ number_format($boxCounts[$i]??0) }}</td>
                            <td style="font-size:11px;color:var(--muted)">{{ $total>0?round(($boxCounts[$i]??0)/$total*100):0 }}%</td>
                        </tr>
                        @endfor
                        <tr style="border-top:1px solid var(--border-md)">
                            <td style="font-weight:700;font-size:12px;color:var(--pruss-mid)">Total</td>
                            <td class="mono" style="color:var(--pruss-mid)">{{ number_format($gridTotal) }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card" style="padding:16px 18px;background:#fffcf5">
                <div class="sec-lbl" style="color:#854F0B">Box Kemenkeu (1-9)</div>
                <table class="ref-tbl">
                    <thead><tr><th>Box</th><th>Jumlah</th><th>%</th></tr></thead>
                    <tbody>
                        @foreach(['1','2','3','4','5','6','7','8','9'] as $rom)
                        <tr>
                            <td><span class="km-b">{{ $rom }}</span></td>
                            <td class="mono">{{ number_format($boxKemenkeuCounts[$rom]??0) }}</td>
                            <td style="font-size:11px;color:var(--muted)">{{ $total>0?round(($boxKemenkeuCounts[$rom]??0)/$total*100):0 }}%</td>
                        </tr>
                        @endforeach
                        <tr style="border-top:1px solid var(--border-md)">
                            <td style="font-weight:700;font-size:12px;color:#854F0B">Total</td>
                            <td class="mono" style="color:#854F0B">{{ number_format($gridTotal) }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($canAll)
            <div class="card" style="padding:16px 18px">
                <div class="sec-lbl">Per Eselon I</div>
                <div style="max-height:280px;overflow-y:auto">
                    <table class="ref-tbl">
                        <thead style="position:sticky;top:0;background:var(--surface);z-index:2">
                            <tr><th>Kode</th><th>Unit</th><th>Jml</th></tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\Pegawai::UE1_LIST as $code=>$name)
                            <tr>
                                <td class="mono" style="font-size:11px;color:var(--subtle)">{{ $code }}</td>
                                <td><span class="bdg b-gray" style="font-size:10px" title="{{ $name }}">{{ \App\Models\Pegawai::UE1_SHORT[$code] }}</span></td>
                                <td class="mono">{{ number_format($ue1Counts[$code]??0) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.2/dist/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.4/dist/jspdf.plugin.autotable.min.js"></script>
<script>
// ── CHARTS ───────────────────────────────────────────────────
const total      = {{ $total }};
const kinPanrb   = [{{ $kinAtas }},{{ $kinSesuai }},{{ $kinBawah }}];
const kinKemkeu  = [{{ $kmKinAtas }},{{ $kmKinSesuai }},{{ $kmKinBawah }}];
const potensial  = [{{ $potTinggi }},{{ $potMenengah }},{{ $potRendah }}];
const kompetensi = [{{ $kompTinggi }},{{ $kompMenengah }},{{ $kompRendah }}];

Chart.defaults.font.family = 'Sora, system-ui, sans-serif';
const doughnutOpts = (labels, data, colors) => ({
    type: 'doughnut',
    data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 3, borderColor: '#fff', hoverOffset: 8 }] },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '65%',
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed.toLocaleString('id-ID') + ' (' + Math.round(ctx.parsed / total * 100) + '%)' } }
        }
    }
});
new Chart(document.getElementById('cKinPanrb'),   doughnutOpts(['Di Atas','Sesuai','Di Bawah'], kinPanrb,   ['#0F6E56','#185FA5','#A32D2D']));
new Chart(document.getElementById('cKinKemkeu'),  doughnutOpts(['Di Atas','Sesuai','Di Bawah'], kinKemkeu,  ['#0F6E56','#c9961a','#A32D2D']));
new Chart(document.getElementById('cPotensial'),  doughnutOpts(['Tinggi','Menengah','Rendah'],   potensial,  ['#0F6E56','#BA7517','#A32D2D']));
new Chart(document.getElementById('cKompetensi'), doughnutOpts(['Tinggi','Menengah','Rendah'],   kompetensi, ['#0F6E56','#c9961a','#A32D2D']));

// ── LIVE SEARCH & FILTER ─────────────────────────────────────
(function () {
    const AJAX_URL = '{{ route('pegawai.index') }}';
    const GRID_UE1 = '{{ $gridUe1 }}';
    const CAN_ALL  = {{ $canAll ? 'true' : 'false' }};

    // Filter state
    const state = {
        search   : '{{ request('search') }}',
        box      : '{{ $cfBox }}',
        kinerja  : '{{ $cfKinerja }}',
        potensi  : '{{ $cfPotensi }}',
        ue1      : '{{ $cfUe1 }}',
        page     : 1,
    };

    // DOM refs
    const $search   = document.getElementById('liveSearch');
    const $spinner  = document.getElementById('searchSpinner');
    const $fUe1     = document.getElementById('fUe1');
    const $fKinerja = document.getElementById('fKinerja');
    const $fPotensi = document.getElementById('fPotensi');
    const $fBox     = document.getElementById('fBox');
    const $reset    = document.getElementById('resetFilters');
    const $tbody    = document.getElementById('tableBody');
    const $cards    = document.getElementById('mobileCards');
    const $empty    = document.getElementById('emptyState');
    const $pagInfo  = document.getElementById('pagInfo');
    const $pagLinks = document.getElementById('pagLinks');
    const $chips    = document.getElementById('activeFilterChips');
    const $countInfo= document.getElementById('countInfo');

    let debounceTimer = null;
    let currentXhr    = null;

    // ── Fetch results via AJAX ──────────────────────────────
    function fetchResults() {
        if (currentXhr) currentXhr.abort();

        // Build query params
        const params = new URLSearchParams();
        if (GRID_UE1)        params.set('grid_ue1', GRID_UE1);
        if (state.search)    params.set('search', state.search);
        if (state.box)       params.set('box', state.box);
        if (state.kinerja)   params.set('kinerja_filter', state.kinerja);
        if (state.potensi)   params.set('potensi_filter', state.potensi);
        if (state.ue1)       params.set('ue1', state.ue1);
        if (state.page > 1)  params.set('page', state.page);
        params.set('ajax', '1'); // signal for controller to return JSON

        // Show spinner + dim table
        $spinner.style.display = 'block';
        if ($tbody) $tbody.classList.add('loading');
        if ($cards) $cards.classList.add('loading');

        currentXhr = new XMLHttpRequest();
        currentXhr.open('GET', AJAX_URL + '?' + params.toString());
        currentXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        currentXhr.setRequestHeader('Accept', 'application/json');
        currentXhr.onload = function () {
            $spinner.style.display = 'none';
            if ($tbody) $tbody.classList.remove('loading');
            if ($cards) $cards.classList.remove('loading');

            if (currentXhr.status === 200) {
                try {
                    const json = JSON.parse(currentXhr.responseText);
                    renderResults(json);
                } catch (e) {
                    // Fallback: controller doesn't support JSON yet — render via HTML fragment
                    renderFallback(currentXhr.responseText);
                }
            }
        };
        currentXhr.onerror = function () {
            $spinner.style.display = 'none';
            if ($tbody) $tbody.classList.remove('loading');
            if ($cards) $cards.classList.remove('loading');
        };
        currentXhr.send();

        // Update URL without reload
        updateURL(params);
        // Update chips
        renderChips();
    }

    // ── Render JSON response ────────────────────────────────
    function renderResults(json) {
        // json.table_html  = rendered <tr> rows HTML
        // json.cards_html  = rendered mobile cards HTML
        // json.pag_html    = pagination links HTML
        // json.from, json.to, json.total
        // json.empty       = bool

        if (json.table_html !== undefined && $tbody) {
            $tbody.innerHTML = json.table_html;
            $tbody.classList.add('tbl-fade');
            setTimeout(() => $tbody.classList.remove('tbl-fade'), 300);
        }
        if (json.cards_html !== undefined && $cards) {
            $cards.innerHTML = json.cards_html;
        }
        if ($empty) {
            $empty.style.display = json.empty ? 'block' : 'none';
        }
        // Count info
        if ($countInfo) {
            if (json.empty) {
                $countInfo.style.display = 'none';
            } else {
                $countInfo.style.display = '';
                $countInfo.innerHTML = 'Menampilkan <strong style="color:var(--pruss-mid)">'
                    + json.from + '–' + json.to
                    + '</strong> dari <strong style="color:var(--pruss-mid)">'
                    + Number(json.total).toLocaleString('id-ID')
                    + '</strong> pegawai';
            }
        }
        // Pagination
        if ($pagInfo && $pagLinks) {
            if (!json.empty) {
                $pagInfo.innerHTML = 'Menampilkan <strong style="color:var(--pruss-mid)">'
                    + json.from + '–' + json.to
                    + '</strong> dari <strong style="color:var(--pruss-mid)">'
                    + Number(json.total).toLocaleString('id-ID')
                    + '</strong> data';
                $pagLinks.innerHTML = json.pag_html || '';
                // Re-bind pagination links
                bindPagLinks();
            } else {
                $pagInfo.innerHTML = '';
                $pagLinks.innerHTML = '';
            }
        }
    }

    // ── Fallback: parse HTML response (no JSON support yet) ─
    function renderFallback(html) {
        const parser = new DOMParser();
        const doc    = parser.parseFromString(html, 'text/html');
        const newTbody = doc.getElementById('tableBody');
        const newCards = doc.getElementById('mobileCards');
        const newPagLinks = doc.getElementById('pagLinks');
        const newCountInfo = doc.getElementById('countInfo');
        const newEmpty = doc.getElementById('emptyState');

        if (newTbody && $tbody) {
            $tbody.innerHTML = newTbody.innerHTML;
            $tbody.classList.add('tbl-fade');
            setTimeout(() => $tbody.classList.remove('tbl-fade'), 300);
        }
        if (newCards && $cards) $cards.innerHTML = newCards.innerHTML;
        if (newPagLinks && $pagLinks) {
            $pagLinks.innerHTML = newPagLinks.innerHTML;
            bindPagLinks();
        }
        if (newCountInfo && $countInfo) $countInfo.innerHTML = newCountInfo.innerHTML;
        if (newEmpty && $empty) $empty.style.display = newEmpty.style.display;
    }

    // ── Re-bind pagination links after render ───────────────
    function bindPagLinks() {
        const links = document.querySelectorAll('#pagLinks a[href]');
        links.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const url = new URL(this.href);
                state.page = parseInt(url.searchParams.get('page') || 1);
                fetchResults();
                // Scroll to table top
                document.querySelector('.tbl-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    // ── Update browser URL bar ──────────────────────────────
    function updateURL(params) {
        const cleanParams = new URLSearchParams(params);
        cleanParams.delete('ajax');
        const newUrl = window.location.pathname + (cleanParams.toString() ? '?' + cleanParams.toString() : '');
        history.replaceState(null, '', newUrl);
    }

    // ── Render active filter chips ──────────────────────────
    const filterLabels = {
        box      : v => '<i class="bi bi-grid-3x3-gap-fill"></i> Box ' + v,
        kinerja  : v => ({ atas: 'Kinerja Di Atas', sesuai: 'Kinerja Sesuai', bawah: 'Kinerja Di Bawah' }[v] || v),
        potensi  : v => ({ tinggi: 'Potensi Tinggi', menengah: 'Potensi Menengah', rendah: 'Potensi Rendah' }[v] || v),
        ue1      : v => v,
        search   : v => '<i class="bi bi-search"></i> ' + v,
    };
    function renderChips() {
        const chips = [];
        ['box','kinerja','potensi','ue1','search'].forEach(key => {
            if (state[key]) {
                chips.push('<span class="chip">' + filterLabels[key](state[key])
                    + ' <span onclick="clearFilter(\'' + key + '\')" style="cursor:pointer;margin-left:4px;opacity:.6;font-size:13px">×</span></span>');
            }
        });
        $chips.innerHTML = chips.join('');
        // Show reset btn boldly if any filter active
        $reset.style.borderColor = chips.length ? 'var(--pruss-mid)' : '';
        $reset.style.color = chips.length ? 'var(--pruss-mid)' : '';
    }
    window.clearFilter = function(key) {
        state[key] = '';
        state.page = 1;
        // Sync UI
        if (key === 'search' && $search) $search.value = '';
        if (key === 'box'     && $fBox)     { $fBox.value = '';     $fBox.classList.remove('active'); }
        if (key === 'kinerja' && $fKinerja) { $fKinerja.value = ''; $fKinerja.classList.remove('active'); }
        if (key === 'potensi' && $fPotensi) { $fPotensi.value = ''; $fPotensi.classList.remove('active'); }
        if (key === 'ue1'     && $fUe1)     { $fUe1.value = '';     $fUe1.classList.remove('active'); }
        fetchResults();
    };

    // ── Debounce helper ─────────────────────────────────────
    function debounce(fn, ms) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fn, ms);
    }

    // ── Event listeners ─────────────────────────────────────
    if ($search) {
        $search.addEventListener('input', function () {
            state.search = this.value.trim();
            state.page = 1;
            debounce(fetchResults, 350); // 350ms debounce — feels instant
        });
    }

    function bindSelect(el, key) {
        if (!el) return;
        el.addEventListener('change', function () {
            state[key] = this.value;
            state.page = 1;
            this.classList.toggle('active', !!this.value);
            fetchResults();
        });
    }
    bindSelect($fUe1,     'ue1');
    bindSelect($fKinerja, 'kinerja');
    bindSelect($fPotensi, 'potensi');
    bindSelect($fBox,     'box');

    if ($reset) {
        $reset.addEventListener('click', function () {
            state.search = ''; state.box = ''; state.kinerja = '';
            state.potensi = ''; state.ue1 = ''; state.page = 1;
            if ($search)   $search.value = '';
            if ($fBox)     { $fBox.value = '';     $fBox.classList.remove('active'); }
            if ($fKinerja) { $fKinerja.value = ''; $fKinerja.classList.remove('active'); }
            if ($fPotensi) { $fPotensi.value = ''; $fPotensi.classList.remove('active'); }
            if ($fUe1)     { $fUe1.value = '';     $fUe1.classList.remove('active'); }
            fetchResults();
        });
    }

    // ── 9-box cell filter integration ───────────────────────
    // Override applyColFilter from 9-box to also drive live search
    window.applyColFilter = function(param, value) {
        if (param === 'box') {
            state.box = String(value);
            if ($fBox) { $fBox.value = String(value); $fBox.classList.toggle('active', !!value); }
        }
        state.page = 1;
        fetchResults();
    };

    // Initial chip render (for server-side pre-filtered state)
    renderChips();
    // Bind initial pagination links
    bindPagLinks();
    // Sync initial filter select active states
    if ($fBox     && state.box)     $fBox.classList.add('active');
    if ($fKinerja && state.kinerja) $fKinerja.classList.add('active');
    if ($fPotensi && state.potensi) $fPotensi.classList.add('active');
    if ($fUe1     && state.ue1)     $fUe1.classList.add('active');

})();

// ── PDF ───────────────────────────────────────────────────────
async function downloadPDF() {
    const btn = event.target.closest('button');
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Memproses...';
    btn.disabled = true;
    try {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({ orientation:'landscape', unit:'mm', format:'a4' });
        const pW = pdf.internal.pageSize.getWidth(), pH = pdf.internal.pageSize.getHeight(), m = 15;
        let y = m;

        const cNavyP=[15,39,68], cGoldP=[201,150,26], cW=[255,255,255];
        const cTealP=[14,122,92], cTealLt=[228,245,239];
        const cAmberP=[181,83,10], cAmberLt=[254,240,228];
        const cRedP=[192,27,69], cRedLt=[253,232,239];
        const cSlateP=[107,122,153], cBorderP=[226,232,243];

        const unitName  = @json($isOp ? \App\Models\Pegawai::UE1_LIST[$user->ue1] ?? '' : ($gridUe1 ? \App\Models\Pegawai::UE1_LIST[$gridUe1] ?? '' : ''));
        const unitShort = @json($isOp ? \App\Models\Pegawai::UE1_SHORT[$user->ue1] ?? '' : ($gridUe1 ? \App\Models\Pegawai::UE1_SHORT[$gridUe1] ?? '' : ''));
        const isAll = !unitName;
        const scope = isAll ? 'SELURUH UNIT KEMENTERIAN KEUANGAN' : unitName.toUpperCase();

        pdf.setFillColor(...cNavyP); pdf.rect(0,0,pW,30,'F');
        pdf.setFillColor(...cGoldP); pdf.rect(0,30,pW,1.5,'F');
        pdf.setTextColor(...cW); pdf.setFontSize(16); pdf.setFont(undefined,'bold');
        pdf.text('LAPORAN PEMETAAN TALENTA',m,13);
        pdf.setFontSize(9); pdf.setFont(undefined,'normal');
        pdf.text('Kementerian Keuangan Republik Indonesia',m,20);
        pdf.setFontSize(10); pdf.setFont(undefined,'bold'); pdf.setTextColor(...cGoldP);
        pdf.text(scope,pW-m,20,{align:'right'});
        pdf.setFontSize(8); pdf.setFont(undefined,'normal'); pdf.setTextColor(180,200,230);
        pdf.text('{{ now()->translatedFormat("d F Y") }}',pW-m,27,{align:'right'});
        y = 37;

        const stats=[
            {l:'TOTAL PEGAWAI',v:{{ $gridTotal }},bg:[220,232,248],fg:cNavyP},
            {l:'KINERJA & POTENSI TINGGI',v:{{ $topCount }},bg:cTealLt,fg:cTealP},
            {l:'KINERJA & POTENSI MENENGAH',v:{{ $midCount }},bg:cAmberLt,fg:cAmberP},
            {l:'PERLU PENGEMBANGAN',v:{{ $lowCount }},bg:cRedLt,fg:cRedP},
        ];
        const bW=(pW-2*m-12)/4;
        stats.forEach((s,i)=>{
            const bx=m+i*(bW+4);
            pdf.setFillColor(...s.bg); pdf.roundedRect(bx,y,bW,22,3,3,'F');
            pdf.setFillColor(...s.fg); pdf.rect(bx,y,bW,2.5,'F');
            pdf.setFontSize(14); pdf.setFont(undefined,'bold'); pdf.setTextColor(...s.fg);
            pdf.text(s.v.toLocaleString('id-ID'),bx+bW/2,y+13,{align:'center'});
            pdf.setFontSize(6); pdf.setFont(undefined,'bold'); pdf.setTextColor(...cSlateP);
            pdf.text(s.l,bx+bW/2,y+18.5,{align:'center'});
        });
        y += 30;

        const half=(pW-2*m-8)/2, gridH=72, lbW=22, hdrH=8;
        const aCW=(half-lbW)/3, aCH=(gridH-hdrH)/3;
        pdf.setFontSize(9); pdf.setFont(undefined,'bold'); pdf.setTextColor(...cNavyP);
        pdf.text('9-Box PAN-RB (PermenPAN-RB 25/2025)',m,y);
        pdf.text('9-Box Kemenkeu (PMK 38/2025)',m+half+8,y);

        const panrbCells=[
            [{b:4,c:{{ $boxCounts[4]??0 }},bg:[255,247,237],fg:cAmberP},{b:7,c:{{ $boxCounts[7]??0 }},bg:[232,250,240],fg:cTealP},{b:9,c:{{ $boxCounts[9]??0 }},bg:[236,253,245],fg:[4,120,82]}],
            [{b:2,c:{{ $boxCounts[2]??0 }},bg:[255,188,117],fg:cAmberP},{b:5,c:{{ $boxCounts[5]??0 }},bg:[255,247,237],fg:cAmberP},{b:8,c:{{ $boxCounts[8]??0 }},bg:[232,250,240],fg:cTealP}],
            [{b:1,c:{{ $boxCounts[1]??0 }},bg:[254,226,226],fg:cRedP},{b:3,c:{{ $boxCounts[3]??0 }},bg:[255,188,117],fg:cAmberP},{b:6,c:{{ $boxCounts[6]??0 }},bg:[254,249,227],fg:[165,120,8]}],
        ];
        const kmCells=[
            [{b:'3',c:{{ $boxKemenkeuCounts['3']??0 }},bg:[255,188,117],fg:cAmberP},{b:'7',c:{{ $boxKemenkeuCounts['7']??0 }},bg:[232,250,240],fg:cTealP},{b:'9',c:{{ $boxKemenkeuCounts['9']??0 }},bg:[236,253,245],fg:[4,120,82]}],
            [{b:'2',c:{{ $boxKemenkeuCounts['2']??0 }},bg:[255,188,117],fg:cAmberP},{b:'6',c:{{ $boxKemenkeuCounts['6']??0 }},bg:[254,249,227],fg:[165,120,8]},{b:'8',c:{{ $boxKemenkeuCounts['8']??0 }},bg:[232,250,240],fg:cTealP}],
            [{b:'1',c:{{ $boxKemenkeuCounts['1']??0 }},bg:[254,226,226],fg:cRedP},{b:'4',c:{{ $boxKemenkeuCounts['4']??0 }},bg:[255,247,237],fg:cAmberP},{b:'5',c:{{ $boxKemenkeuCounts['5']??0 }},bg:[244,246,251],fg:cSlateP}],
        ];
        const rowLbPan=['Di Atas','Sesuai','Di Bawah'], colLbPan=['Rendah','Menengah','Tinggi'];
        const rowLbKm=['Komp. Tinggi','Komp. Menengah','Komp. Rendah'], colLbKm=['Kin. Bawah','Kin. Sesuai','Kin. Atas'];

        function drawGrid(gx,cells,rLbls,cLbls){
            const gy=y+5;
            cLbls.forEach((l,ci)=>{const cx=gx+lbW+ci*aCW+aCW/2;pdf.setFontSize(5.5);pdf.setFont(undefined,'bold');pdf.setTextColor(...cNavyP);pdf.text(l.toUpperCase(),cx,gy+5,{align:'center'});});
            cells.forEach((row,ri)=>{
                const cy=gy+hdrH+ri*aCH;
                pdf.saveGraphicsState();pdf.setFontSize(5);pdf.setFont(undefined,'bold');pdf.setTextColor(...cSlateP);pdf.text(rLbls[ri],gx+1,cy+aCH/2+3,null,90);pdf.restoreGraphicsState();
                row.forEach((cell,ci)=>{
                    const cx=gx+lbW+ci*aCW;
                    pdf.setFillColor(...cell.bg);pdf.rect(cx,cy,aCW,aCH,'F');
                    pdf.setDrawColor(...cBorderP);pdf.setLineWidth(0.3);pdf.rect(cx,cy,aCW,aCH,'S');
                    pdf.setFontSize(11);pdf.setFont(undefined,'bold');pdf.setTextColor(...cell.fg);pdf.text(cell.c.toString(),cx+aCW/2,cy+aCH/2-1,{align:'center'});
                    pdf.setFontSize(5);pdf.setFont(undefined,'normal');pdf.setTextColor(...cSlateP);pdf.text('Box '+cell.b,cx+aCW/2,cy+aCH/2+5,{align:'center'});
                });
            });
        }
        drawGrid(m,panrbCells,rowLbPan,colLbPan);
        drawGrid(m+half+8,kmCells,rowLbKm,colLbKm);

        const totalPages=pdf.internal.getNumberOfPages();
        for(let p=1;p<=totalPages;p++){
            pdf.setPage(p);
            pdf.setDrawColor(...cBorderP);pdf.setLineWidth(0.3);pdf.line(m,pH-12,pW-m,pH-12);
            pdf.setFontSize(7);pdf.setFont(undefined,'normal');pdf.setTextColor(...cSlateP);
            pdf.text('Laporan Pemetaan Talenta — Kementerian Keuangan RI',m,pH-7);
            pdf.text('Halaman '+p+' dari '+totalPages,pW-m,pH-7,{align:'right'});
            pdf.setFont(undefined,'bold');pdf.text(scope,pW/2,pH-7,{align:'center'});
        }
        const fname = isAll
            ? 'Laporan_Talent_Mapping_Seluruh_Unit_{{ now()->format("Y-m-d") }}.pdf'
            : 'Laporan_Talent_Mapping_'+unitShort.replace(/\s+/g,'_')+'_{{ now()->format("Y-m-d") }}.pdf';
        pdf.save(fname);
    } catch(e) {
        console.error(e);
        alert('Gagal generate PDF. Silakan coba lagi.');
    } finally {
        btn.innerHTML = orig;
        btn.disabled = false;
    }
}
</script>
@endpush