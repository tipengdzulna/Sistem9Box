{{-- resources/views/pegawai/_mobile_cards.blade.php --}}
{{-- Variables: $pegawai (paginator), $canAll (bool), $search (string) --}}
@php
    if (!function_exists('hlText')) {
        function hlText(string $text, string $keyword): string {
            if (!$keyword || strlen(trim($keyword)) < 1) return e($text);
            $safe = preg_quote($keyword, '/');
            return preg_replace('/(' . $safe . ')/iu', '<mark class="hl">$1</mark>', e($text));
        }
    }
@endphp

@forelse($pegawai as $p)
@php
    $bc         = in_array($p->box, [7,8,9]) ? '#0F6E56' : (in_array($p->box, [4,5,6]) ? '#185FA5' : '#A32D2D');
    $boxKm      = $p->box_kemenkeu ?? \App\Models\Pegawai::calculateBoxKemenkeu($p->kategori_potensial, $p->kategori_kinerja_nkp ?? $p->kategori_kinerja);
    $nkpColor   = match($p->kategori_kinerja_nkp ?? '') {
        'Di atas ekspektasi' => '#0F6E56',
        'Sesuai ekspektasi'  => '#185FA5',
        default              => '#A32D2D',
    };
    $nilaiTotal = $p->nilai_potensi_total !== null ? (float)$p->nilai_potensi_total : null;
    $nilaiColor = $nilaiTotal !== null
        ? ($nilaiTotal >= 90 ? '#0F6E56' : ($nilaiTotal >= 78 ? '#854F0B' : '#A32D2D'))
        : null;
    $kw         = trim($search ?? '');
@endphp
<div class="m-card" style="border-left-color:{{ $bc }}">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:8px">
        <div style="flex:1;min-width:0">
            <div class="pname">{!! hlText($p->nama ?? '-', $kw) !!}</div>
            <div class="pnip">{!! hlText($p->nip, $kw) !!}</div>
            @if($p->jabatan)
                <div class="pjab">{{ $p->jabatan }}@if($p->jenjang_jabatan) · {{ $p->jenjang_jabatan }}@endif</div>
            @endif
            @if($p->nkp !== null)
                <div style="font-size:11px;font-weight:700;color:{{ $nkpColor }};margin-top:3px;font-family:'JetBrains Mono',monospace">
                    NKP: {{ number_format((float)$p->nkp, 2) }}
                </div>
            @endif
            @if($nilaiTotal !== null)
                <div style="font-size:11px;font-weight:800;color:{{ $nilaiColor }};margin-top:2px;font-family:'JetBrains Mono',monospace">
                    Nilai Potensi: {{ number_format($nilaiTotal, 2) }}
                </div>
            @endif
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
            <form action="{{ route('pegawai.destroy', $p->id) }}" method="POST"
                  onsubmit="return confirm('Hapus pegawai ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="del-btn"><i class="bi bi-trash3"></i></button>
            </form>
        </div>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:4px">
        @if($canAll)
            <span class="bdg b-gray">{{ $p->ue1_short }}</span>
        @endif
        @if($p->predikat_kinerja)
            @php $cls = $p->kategori_kinerja === 'Di atas ekspektasi' ? 'b-teal' : ($p->kategori_kinerja === 'Sesuai ekspektasi' ? 'b-blue' : 'b-red') @endphp
            <span class="bdg {{ $cls }}">{{ $p->predikat_kinerja }}</span>
        @endif
        @if($p->kategori_potensial === 'potensial tinggi')
            <span class="bdg b-teal">Potensi Tinggi</span>
        @elseif($p->kategori_potensial === 'potensial menengah')
            <span class="bdg b-amber">Potensi Menengah</span>
        @else
            <span class="bdg b-red">Potensi Rendah</span>
        @endif
    </div>
</div>
@empty
@endforelse