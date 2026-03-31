{{-- resources/views/pegawai/_table_rows.blade.php --}}
{{-- Variables: $pegawai (paginator), $canAll (bool), $search (string) --}}
@php
    // Helper: highlight keyword dalam string
    function hlText(string $text, string $keyword): string {
        if (!$keyword || strlen(trim($keyword)) < 1) return e($text);
        $safe = preg_quote($keyword, '/');
        return preg_replace(
            '/(' . $safe . ')/iu',
            '<mark class="hl">$1</mark>',
            e($text)
        );
    }
@endphp

@forelse($pegawai as $i => $p)
@php
    $boxKm      = $p->box_kemenkeu ?? \App\Models\Pegawai::calculateBoxKemenkeu($p->kategori_potensial, $p->kategori_kinerja_nkp ?? $p->kategori_kinerja);
    $nkpVal     = $p->nkp !== null ? number_format((float)$p->nkp, 2) : null;
    $nkpKat     = $p->kategori_kinerja_nkp ?? null;
    $nkpColor   = match($nkpKat) {
        'Di atas ekspektasi' => '#0F6E56',
        'Sesuai ekspektasi'  => '#185FA5',
        default              => '#A32D2D',
    };
    $nilaiTotal = $p->nilai_potensi_total !== null ? (float)$p->nilai_potensi_total : null;
    $nilaiColor = $nilaiTotal !== null
        ? ($nilaiTotal >= 90 ? '#0F6E56' : ($nilaiTotal >= 78 ? '#854F0B' : '#A32D2D'))
        : '#9aaac2';
    $kw         = trim($search ?? '');
@endphp
<tr>
    {{-- No --}}
    <td><span class="row-n">{{ $pegawai->firstItem() + $i }}</span></td>

    {{-- NIP / Nama / Jabatan --}}
    <td>
        <div class="pname">{!! hlText($p->nama ?? '-', $kw) !!}</div>
        <div class="pnip">{!! hlText($p->nip, $kw) !!}</div>
        @if($p->jabatan)
            <div class="pjab">
                <i class="bi bi-briefcase" style="font-size:10px"></i>
                {{ $p->jabatan }}@if($p->jenjang_jabatan) · {{ $p->jenjang_jabatan }}@endif
            </div>
        @endif
    </td>

    {{-- Unit --}}
    @if($canAll)
    <td><span class="bdg b-gray" title="{{ $p->ue1_nama }}">{{ $p->ue1_short }}</span></td>
    @endif

    {{-- Jenjang --}}
    <td>
        @if($p->jenjang_jabatan)
            <span class="bdg b-gray" style="font-size:10px">{{ $p->jenjang_jabatan }}</span>
        @else
            <span style="color:var(--border-md);font-size:12px">—</span>
        @endif
    </td>

    {{-- NKP --}}
    <td>
        @if($nkpVal)
            <span class="nkp-val" style="color:{{ $nkpColor }}">{{ $nkpVal }}</span>
            @if($nkpKat)
                <span class="nkp-kat" style="color:{{ $nkpColor }}">
                    {{ $nkpKat === 'Di atas ekspektasi' ? '↑ Atas' : ($nkpKat === 'Sesuai ekspektasi' ? '= Sesuai' : '↓ Bawah') }}
                </span>
            @endif
        @else
            <span style="color:var(--border-md);font-size:12px">—</span>
        @endif
    </td>

    {{-- Predikat Kinerja --}}
    <td>
        @if($p->predikat_kinerja)
            @php $cls = $p->kategori_kinerja === 'Di atas ekspektasi' ? 'b-teal' : ($p->kategori_kinerja === 'Sesuai ekspektasi' ? 'b-blue' : 'b-red') @endphp
            <span class="bdg {{ $cls }}">{{ $p->predikat_kinerja }}</span>
        @else
            @php $cls = $p->kategori_kinerja === 'Di atas ekspektasi' ? 'b-teal' : ($p->kategori_kinerja === 'Sesuai ekspektasi' ? 'b-blue' : 'b-red') @endphp
            @php $lbl = $p->kategori_kinerja === 'Di atas ekspektasi' ? 'Di Atas' : ($p->kategori_kinerja === 'Sesuai ekspektasi' ? 'Sesuai' : 'Di Bawah') @endphp
            <span class="bdg {{ $cls }}">{{ $lbl }}</span>
        @endif
    </td>

    {{-- Kategori Potensial --}}
    <td class="td-potensial">
        @if($p->kategori_potensial === 'potensial tinggi')
            <span class="bdg b-teal">Tinggi</span>
        @elseif($p->kategori_potensial === 'potensial menengah')
            <span class="bdg b-amber">Menengah</span>
        @else
            <span class="bdg b-red">Rendah</span>
        @endif
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
            <button type="button" class="detail-btn"
                    onclick="showBreakdown(this, {{ $p->id }},
                        {{ $p->kompetensi_teknis ?? 'null' }},
                        {{ $p->kompetensi_mansoskul ?? 'null' }},
                        [{{ implode(',', array_map(fn($k) => $p->{'potensi_'.$k} ?? 'null', range(1,9))) }}],
                        {{ $p->rj_pendidikan_formal ?? 'null' }},
                        {{ $p->rj_pelatihan ?? 'null' }},
                        {{ $p->rj_pengalaman ?? 'null' }},
                        {{ $p->rj_integritas ?? 'null' }},
                        {{ $p->rj_moralitas ?? 'null' }}
                    )" title="Lihat breakdown nilai">
                <i class="bi bi-info"></i>
            </button>
        @else
            <span style="color:var(--border-md);font-size:11px;display:block">—</span>
            <span style="color:var(--subtle);font-size:10px">Belum dihitung</span>
        @endif
    </td>

    {{-- Box PAN-RB --}}
    <td><span class="box-b bb{{ $p->box }}">{{ $p->box }}</span></td>

    {{-- Box Kemenkeu --}}
    <td><span class="km-b">{{ $boxKm }}</span></td>

    {{-- Hapus --}}
    <td>
        <form action="{{ route('pegawai.destroy', $p->id) }}" method="POST"
              onsubmit="return confirm('Hapus pegawai ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="del-btn"><i class="bi bi-trash3"></i></button>
        </form>
    </td>
</tr>
@empty
{{-- Kosong ditangani oleh emptyState di parent --}}
@endforelse