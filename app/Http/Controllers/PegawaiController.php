<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    private function currentUser(): User { /** @var User $u */ $u = auth()->user(); return $u; }

    private function scopedQuery()
    {
        $q = Pegawai::query();
        if ($this->currentUser()->isOperator()) $q->where('ue1', $this->currentUser()->ue1);
        return $q;
    }

    // ─── DASHBOARD ───────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $user    = $this->currentUser();
        $gridUe1 = $user->isOperator() ? $user->ue1 : $request->input('grid_ue1');

        // Box counts PAN-RB
        $boxQ = $this->scopedQuery()->select('box', DB::raw('count(*) as jumlah'));
        if ($gridUe1 && $user->canAccessAllUnits()) $boxQ->where('ue1', $gridUe1);
        $boxCounts = $boxQ->groupBy('box')->pluck('jumlah', 'box')->toArray();

        // Box counts Kemenkeu
        $kmOrder = ['1','2','3','4','5','6','7','8','9'];
        $boxKemenkeuCounts = array_fill_keys($kmOrder, 0);
        $rawKm = $this->scopedQuery()
            ->when($gridUe1 && $user->canAccessAllUnits(), fn($q) => $q->where('ue1', $gridUe1))
            ->select('kategori_kinerja_nkp', 'kategori_potensial', DB::raw('count(*) as jumlah'))
            ->groupBy('kategori_kinerja_nkp', 'kategori_potensial')
            ->get();
        foreach ($rawKm as $row) {
            if (!$row->kategori_kinerja_nkp || !$row->kategori_potensial) continue;
            $k = Pegawai::calculateBoxKemenkeu($row->kategori_potensial, $row->kategori_kinerja_nkp);
            if ($k !== '-') $boxKemenkeuCounts[$k] = ($boxKemenkeuCounts[$k] ?? 0) + $row->jumlah;
        }

        $gridTotal = $user->isOperator()
            ? $this->scopedQuery()->count()
            : ($gridUe1 ? Pegawai::where('ue1', $gridUe1)->count() : Pegawai::count());

        $ue1Counts = $user->isOperator()
            ? [$user->ue1 => $this->scopedQuery()->count()]
            : Pegawai::select('ue1', DB::raw('count(*) as jumlah'))
                ->groupBy('ue1')->orderBy('ue1')
                ->pluck('jumlah', 'ue1')->toArray();

        $totalPegawai = $this->scopedQuery()->count();

        $pegawai = $this->scopedQuery()
            ->orderBy('box')->orderBy('nip')
            ->paginate(25)->withQueryString();

        return view('pegawai.index', compact(
            'pegawai', 'boxCounts', 'boxKemenkeuCounts', 'ue1Counts',
            'totalPegawai', 'gridTotal', 'gridUe1'
        ));
    }

    // ─── DAFTAR PEGAWAI ──────────────────────────────────────────────────────
     public function daftar(Request $request)
    {
        $user = auth()->user();
        $canAll = $user->canAccessAllUnits();
        $isOp = $user->isOperator();
        
        // Build query
        $query = Pegawai::query();
        
        if ($isOp) {
            $query->where('ue1', $user->ue1);
        }
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nip', 'LIKE', "%{$search}%")
                  ->orWhere('nama', 'LIKE', "%{$search}%");
            });
        }
        
        if ($canAll && $request->filled('ue1')) {
            $query->where('ue1', $request->ue1);
        }
        
        if ($request->filled('jenjang')) {
            $query->where('jenjang_jabatan', $request->jenjang);
        }
        
        if ($request->filled('kinerja_filter')) {
            $kinerja = $request->kinerja_filter;
            if ($kinerja === 'atas') {
                $query->where('kategori_kinerja', 'Di atas ekspektasi');
            } elseif ($kinerja === 'sesuai') {
                $query->where('kategori_kinerja', 'Sesuai ekspektasi');
            } elseif ($kinerja === 'bawah') {
                $query->where('kategori_kinerja', 'Di bawah ekspektasi');
            }
        }
        
        if ($request->filled('nkp_filter')) {
            $nkp = $request->nkp_filter;
            if ($nkp === 'atas') {
                $query->where('nkp', '>', 100);
            } elseif ($nkp === 'sesuai') {
                $query->whereBetween('nkp', [90, 100]);
            } elseif ($nkp === 'bawah') {
                $query->where('nkp', '<', 90);
            }
        }
        
        if ($request->filled('nkp_min')) {
            $query->where('nkp', '>=', (float)$request->nkp_min);
        }
        
        if ($request->filled('nkp_max')) {
            $query->where('nkp', '<=', (float)$request->nkp_max);
        }
        
        if ($request->filled('potensi_filter')) {
            $potensi = $request->potensi_filter;
            if ($potensi === 'tinggi') {
                $query->where('kategori_potensial', 'potensial tinggi');
            } elseif ($potensi === 'menengah') {
                $query->where('kategori_potensial', 'potensial menengah');
            } elseif ($potensi === 'rendah') {
                $query->where('kategori_potensial', 'potensial rendah');
            }
        }
        
        if ($request->filled('nilai_min')) {
            $query->where('nilai_potensi_total', '>=', (float)$request->nilai_min);
        }
        
        if ($request->filled('nilai_max')) {
            $query->where('nilai_potensi_total', '<=', (float)$request->nilai_max);
        }
        
        if ($request->filled('nilai_potensi_filter')) {
            if ($request->nilai_potensi_filter === 'ada') {
                $query->whereNotNull('nilai_potensi_total');
            } elseif ($request->nilai_potensi_filter === 'belum') {
                $query->whereNull('nilai_potensi_total');
            }
        }
        
        if ($request->filled('box')) {
            $query->where('box', $request->box);
        }
        
        if ($request->filled('box_kemenkeu')) {
            $query->where('box_kemenkeu', $request->box_kemenkeu);
        }
        
        // Get paginated results
        $pegawai = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // For AJAX request
        if ($request->ajax() || $request->has('ajax')) {
            // Prepare breakdown data for each pegawai
            $tableHtml = $this->renderTableRows($pegawai, $canAll);
            $cardsHtml = $this->renderMobileCards($pegawai, $canAll);
            
            // Get stats
            $baseQuery = Pegawai::query();
            if ($isOp) {
                $baseQuery->where('ue1', $user->ue1);
            }
            
            $stats = [
                'total' => $baseQuery->count(),
                'tinggi' => (clone $baseQuery)->whereIn('box', [7,8,9])->count(),
                'menengah' => (clone $baseQuery)->whereIn('box', [2,4,5])->count(),
                'rendah' => (clone $baseQuery)->whereIn('box', [1,3,6])->count(),
            ];
            
            return response()->json([
                'table_html' => $tableHtml,
                'cards_html' => $cardsHtml,
                'empty' => $pegawai->count() === 0,
                'total' => $pegawai->total(),
                'from' => $pegawai->firstItem(),
                'to' => $pegawai->lastItem(),
                'current_page' => $pegawai->currentPage(),
                'last_page' => $pegawai->lastPage(),
                'pag_html' => $pegawai->links('pagination::bootstrap-4')->toHtml(),
                'stats' => $stats,
                'total_all' => $stats['total'],
            ]);
        }
        
        return view('pegawai.daftar', compact('pegawai', 'canAll', 'isOp'));
    }

    private function ajaxResponse($pegawai, $request)
    {
        $canAll = $this->currentUser()->canAccessAllUnits();
        return response()->json([
            'table_html'   => $this->renderTableRows($pegawai, $canAll),
            'cards_html'   => $this->renderCards($pegawai, $canAll),
            'pag_html'     => $pegawai->links('pagination::bootstrap-4')->render(),
            'total'        => $pegawai->total(),
            'from'         => $pegawai->firstItem(),
            'to'           => $pegawai->lastItem(),
            'current_page' => $pegawai->currentPage(),
            'last_page'    => $pegawai->lastPage(),
            'empty'        => $pegawai->count() === 0,
        ]);
    }

    private function renderTableRows($pegawai, $canAll)
    {
        $html = '';
        foreach ($pegawai as $i => $p) {
            $boxKm = $p->box_kemenkeu ?? Pegawai::calculateBoxKemenkeu(
                $p->kategori_potensial, 
                $p->kategori_kinerja_nkp ?? $p->kategori_kinerja
            );
            
            $nkpVal = $p->nkp !== null ? number_format((float)$p->nkp, 2) : null;
            $nkpKat = $p->kategori_kinerja_nkp ?? null;
            $nkpColor = match($nkpKat) {
                'Di atas ekspektasi' => '#0F6E56',
                'Sesuai ekspektasi' => '#185FA5',
                default => '#A32D2D'
            };
            
            $rowNum = $pegawai->firstItem() + $i;
            $nilaiTotal = $p->nilai_potensi_total !== null ? (float)$p->nilai_potensi_total : null;
            $nilaiColor = $nilaiTotal !== null ? ($nilaiTotal >= 90 ? '#0F6E56' : ($nilaiTotal >= 78 ? '#854F0B' : '#A32D2D')) : '#9aaac2';
            
            $html .= '<tr>';
            $html .= '<td><span class="row-n">' . $rowNum . '</span></td>';
            $html .= '<td>';
            $html .= '<div class="pname">' . e($p->nama ?? '-') . '</div>';
            $html .= '<div class="pnip">' . e($p->nip) . '</div>';
            if ($p->jabatan) {
                $html .= '<div class="pjab"><i class="bi bi-briefcase" style="font-size:10px"></i> ' . e($p->jabatan);
                if ($p->jenjang_jabatan) $html .= ' · ' . e($p->jenjang_jabatan);
                $html .= '</div>';
            }
            $html .= '</td>';
            
            if ($canAll) {
                $html .= '<td><span class="bdg b-gray" title="' . e($p->ue1_nama ?? '') . '">' . e($p->ue1_short ?? '-') . '</span></td>';
            }
            
            $html .= '<td>';
            if ($p->jenjang_jabatan) {
                $html .= '<span class="bdg b-gray" style="font-size:10px">' . e($p->jenjang_jabatan) . '</span>';
            } else {
                $html .= '<span style="color:var(--border-md);font-size:12px">—</span>';
            }
            $html .= '</td>';
            
            // NKP
            $html .= '<td>';
            if ($nkpVal) {
                $html .= '<span class="nkp-val" style="color:' . $nkpColor . '">' . $nkpVal . '</span>';
                if ($nkpKat) {
                    $label = match($nkpKat) {
                        'Di atas ekspektasi' => '↑ Atas',
                        'Sesuai ekspektasi' => '= Sesuai',
                        default => '↓ Bawah'
                    };
                    $html .= '<span class="nkp-kat" style="color:' . $nkpColor . '">' . $label . '</span>';
                }
            } else {
                $html .= '<span style="color:var(--border-md);font-size:12px">—</span>';
            }
            $html .= '</td>';
            
            // Predikat Kinerja
            $html .= '<td>';
            if ($p->predikat_kinerja) {
                $cls = match($p->kategori_kinerja) {
                    'Di atas ekspektasi' => 'b-teal',
                    'Sesuai ekspektasi' => 'b-blue',
                    default => 'b-red'
                };
                $html .= '<span class="bdg ' . $cls . '">' . e($p->predikat_kinerja) . '</span>';
            } else {
                $cls = match($p->kategori_kinerja) {
                    'Di atas ekspektasi' => 'b-teal',
                    'Sesuai ekspektasi' => 'b-blue',
                    default => 'b-red'
                };
                $label = match($p->kategori_kinerja) {
                    'Di atas ekspektasi' => 'Di Atas',
                    'Sesuai ekspektasi' => 'Sesuai',
                    default => 'Di Bawah'
                };
                $html .= '<span class="bdg ' . $cls . '">' . $label . '</span>';
            }
            $html .= '</td>';
            
            // Potensial
            $html .= '<td class="td-potensial">';
            $potensialLabel = match($p->kategori_potensial) {
                'potensial tinggi' => 'Tinggi',
                'potensial menengah' => 'Menengah',
                default => 'Rendah'
            };
            $potensialClass = match($p->kategori_potensial) {
                'potensial tinggi' => 'b-teal',
                'potensial menengah' => 'b-amber',
                default => 'b-red'
            };
            $html .= '<span class="bdg ' . $potensialClass . '">' . $potensialLabel . '</span>';
            $html .= '</td>';
            
            // Nilai Potensi Total dengan tombol breakdown
            $html .= '<td class="td-potensial">';
            if ($nilaiTotal !== null) {
                $html .= '<span class="nilai-total" style="color:' . $nilaiColor . '">' . number_format($nilaiTotal, 2) . '</span>';
                $subLabel = match(true) {
                    $nilaiTotal >= 90 => '★ Tinggi',
                    $nilaiTotal >= 78 => '◆ Menengah',
                    default => '▼ Rendah'
                };
                $html .= '<span class="nilai-total-sub" style="color:' . $nilaiColor . '">' . $subLabel . '</span>';
                
                // Prepare data for breakdown
                $kompetensiTeknis = $p->kompetensi_teknis !== null ? (float)$p->kompetensi_teknis : 'null';
                $kompetensiMansoskul = $p->kompetensi_mansoskul !== null ? (float)$p->kompetensi_mansoskul : 'null';
                
                // Potensi 9 kompetensi
                $potensiValues = [];
                for ($k = 1; $k <= 9; $k++) {
                    $val = $p->{'potensi_' . $k};
                    $potensiValues[] = $val !== null ? (float)$val : 'null';
                }
                $potensiJson = '[' . implode(',', $potensiValues) . ']';
                
                $rjPendidikan = $p->rj_pendidikan_formal !== null ? (float)$p->rj_pendidikan_formal : 'null';
                $rjPelatihan = $p->rj_pelatihan !== null ? (float)$p->rj_pelatihan : 'null';
                $rjPengalaman = $p->rj_pengalaman !== null ? (float)$p->rj_pengalaman : 'null';
                $rjIntegritas = $p->rj_integritas !== null ? (float)$p->rj_integritas : 'null';
                $rjMoralitas = $p->rj_moralitas !== null ? (float)$p->rj_moralitas : 'null';
                
                $html .= '<button type="button" class="detail-btn"';
                $html .= ' onclick="showBreakdown(this, ' . $p->id . ', ' . $kompetensiTeknis . ', ' . $kompetensiMansoskul . ', ' . $potensiJson . ', ' . $rjPendidikan . ', ' . $rjPelatihan . ', ' . $rjPengalaman . ', ' . $rjIntegritas . ', ' . $rjMoralitas . ')"';
                $html .= ' title="Lihat breakdown nilai">';
                $html .= '<i class="bi bi-info"></i>';
                $html .= '</button>';
            } else {
                $html .= '<span style="color:var(--border-md);font-size:11px;display:block">—</span>';
                $html .= '<span style="color:var(--subtle);font-size:10px">Belum dihitung</span>';
            }
            $html .= '</td>';
            
            // Box
            $html .= '<td><span class="box-b bb' . $p->box . '">' . $p->box . '</span></td>';
            $html .= '<td><span class="km-b">' . $boxKm . '</span></td>';
            
            // Action delete
            $html .= '<td>';
            $html .= '<form action="' . route('pegawai.destroy', $p->id) . '" method="POST" onsubmit="return confirm(\'Hapus pegawai ini?\')">';
            $html .= csrf_field();
            $html .= method_field('DELETE');
            $html .= '<button type="submit" class="del-btn"><i class="bi bi-trash3"></i></button>';
            $html .= '</form>';
            $html .= '</td>';
            
            $html .= '</tr>';
        }
        
        return $html;
    }

    private function renderMobileCards($pegawai, $canAll)
    {
        $html = '';
        foreach ($pegawai as $p) {
            $bc = in_array($p->box, [7,8,9]) ? '#0F6E56' : (in_array($p->box, [4,5,6]) ? '#185FA5' : '#A32D2D');
            $boxKm = $p->box_kemenkeu ?? Pegawai::calculateBoxKemenkeu(
                $p->kategori_potensial, 
                $p->kategori_kinerja_nkp ?? $p->kategori_kinerja
            );
            
            $nkpColor = match($p->kategori_kinerja_nkp ?? '') {
                'Di atas ekspektasi' => '#0F6E56',
                'Sesuai ekspektasi' => '#185FA5',
                default => '#A32D2D'
            };
            
            $nilaiTotal = $p->nilai_potensi_total !== null ? (float)$p->nilai_potensi_total : null;
            $nilaiColor = $nilaiTotal !== null ? ($nilaiTotal >= 90 ? '#0F6E56' : ($nilaiTotal >= 78 ? '#854F0B' : '#A32D2D')) : null;
            
            $html .= '<div class="m-card" style="border-left-color:' . $bc . '">';
            $html .= '<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:8px">';
            $html .= '<div style="flex:1;min-width:0">';
            $html .= '<div class="pname">' . e($p->nama ?? '-') . '</div>';
            $html .= '<div class="pnip">' . e($p->nip) . '</div>';
            if ($p->jabatan) {
                $html .= '<div class="pjab">' . e($p->jabatan);
                if ($p->jenjang_jabatan) $html .= ' · ' . e($p->jenjang_jabatan);
                $html .= '</div>';
            }
            if ($p->nkp !== null) {
                $html .= '<div style="font-size:11px;font-weight:700;color:' . $nkpColor . ';margin-top:3px;font-family:\'JetBrains Mono\',monospace">NKP: ' . number_format((float)$p->nkp, 2) . '</div>';
            }
            if ($nilaiTotal !== null) {
                $html .= '<div style="font-size:11px;font-weight:800;color:' . $nilaiColor . ';margin-top:2px;font-family:\'JetBrains Mono\',monospace">';
                $html .= 'Nilai Potensi: ' . number_format($nilaiTotal, 2);
                $html .= '</div>';
            }
            if ($p->kompetensi_teknis !== null || $p->kompetensi_mansoskul !== null) {
                $html .= '<div style="font-size:10px;color:var(--subtle);margin-top:3px">';
                if ($p->kompetensi_teknis !== null) {
                    $html .= 'Teknis: <strong>' . number_format((float)$p->kompetensi_teknis, 1) . '</strong>';
                }
                if ($p->kompetensi_mansoskul !== null) {
                    if ($p->kompetensi_teknis !== null) $html .= ' · ';
                    $html .= 'Mansoskul: <strong>' . number_format((float)$p->kompetensi_mansoskul, 1) . '</strong>';
                }
                $html .= '</div>';
            }
            $html .= '</div>';
            
            $html .= '<div style="display:flex;align-items:center;gap:8px;flex-shrink:0">';
            $html .= '<div style="display:flex;flex-direction:column;align-items:center;gap:3px">';
            $html .= '<span class="box-b bb' . $p->box . '" style="width:30px;height:30px;font-size:12px">' . $p->box . '</span>';
            $html .= '<span class="km-b" style="font-size:10px;padding:1px 6px;min-width:30px;height:22px">' . $boxKm . '</span>';
            $html .= '</div>';
            $html .= '<form action="' . route('pegawai.destroy', $p->id) . '" method="POST" onsubmit="return confirm(\'Hapus?\')">';
            $html .= csrf_field();
            $html .= method_field('DELETE');
            $html .= '<button type="submit" class="del-btn"><i class="bi bi-trash3"></i></button>';
            $html .= '</form>';
            $html .= '</div></div>';
            
            $html .= '<div style="display:flex;flex-wrap:wrap;gap:4px">';
            if ($canAll) {
                $html .= '<span class="bdg b-gray">' . e($p->ue1_short ?? '-') . '</span>';
            }
            if ($p->predikat_kinerja) {
                $cls = match($p->kategori_kinerja) {
                    'Di atas ekspektasi' => 'b-teal',
                    'Sesuai ekspektasi' => 'b-blue',
                    default => 'b-red'
                };
                $html .= '<span class="bdg ' . $cls . '">' . e($p->predikat_kinerja) . '</span>';
            }
            
            $potensialLabel = match($p->kategori_potensial) {
                'potensial tinggi' => 'Potensi Tinggi',
                'potensial menengah' => 'Potensi Menengah',
                default => 'Potensi Rendah'
            };
            $potensialClass = match($p->kategori_potensial) {
                'potensial tinggi' => 'b-teal',
                'potensial menengah' => 'b-amber',
                default => 'b-red'
            };
            $html .= '<span class="bdg ' . $potensialClass . '">' . $potensialLabel . '</span>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        return $html;
    }
    
    /**
     * Get breakdown data for a single pegawai (AJAX endpoint)
     */
    public function getBreakdown($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        
        $kompetensiTeknis = $pegawai->kompetensi_teknis;
        $kompetensiMansoskul = $pegawai->kompetensi_mansoskul;
        
        $potensi = [];
        for ($i = 1; $i <= 9; $i++) {
            $potensi[] = $pegawai->{'potensi_' . $i};
        }
        
        $rekamJejak = [
            'pendidikan' => $pegawai->rj_pendidikan_formal,
            'pelatihan' => $pegawai->rj_pelatihan,
            'pengalaman' => $pegawai->rj_pengalaman,
            'integritas' => $pegawai->rj_integritas,
            'moralitas' => $pegawai->rj_moralitas,
        ];
        
        // Calculate nilai kompetensi (70%)
        $nilaiKompetensi = null;
        if ($kompetensiTeknis !== null && $kompetensiMansoskul !== null) {
            $nilaiKompetensi = (($kompetensiTeknis + $kompetensiMansoskul) / 2) * 0.7;
        }
        
        // Calculate nilai potensi (20%)
        $nilaiPotensi = null;
        $potensiValues = array_filter($potensi, function($v) { return $v !== null; });
        if (count($potensiValues) > 0) {
            $nilaiPotensi = (array_sum($potensiValues) / count($potensiValues)) * 0.2;
        }
        
        // Calculate nilai rekam jejak (10%)
        $nilaiRekamJejak = null;
        $rjValues = array_filter(array_values($rekamJejak), function($v) { return $v !== null; });
        if (count($rjValues) > 0) {
            $nilaiRekamJejak = (array_sum($rjValues) / count($rjValues)) * 0.1;
        }
        
        $total = ($nilaiKompetensi ?? 0) + ($nilaiPotensi ?? 0) + ($nilaiRekamJejak ?? 0);
        
        return response()->json([
            'kompetensi_teknis' => $kompetensiTeknis,
            'kompetensi_mansoskul' => $kompetensiMansoskul,
            'nilai_kompetensi' => $nilaiKompetensi,
            'potensi' => $potensi,
            'nilai_potensi' => $nilaiPotensi,
            'rekam_jejak' => $rekamJejak,
            'nilai_rekam_jejak' => $nilaiRekamJejak,
            'total' => $total,
        ]);
    }


    private function nilaiTotalColor(float $nilai): string
    {
        if ($nilai >= 90) return '#0F6E56';
        if ($nilai >= 78) return '#854F0B';
        return '#A32D2D';
    }

    // ─── CREATE / STORE ───────────────────────────────────────────────────────
    public function create()
    {
        return view('pegawai.create');
    }

    public function store(Request $request)
    {
        $user  = $this->currentUser();
        $rules = [
            'nip'               => 'required|string|unique:pegawai,nip',
            'nama'              => 'nullable|string|max:255',
            'jabatan'           => 'nullable|string|max:255',
            'jenjang_jabatan'   => 'nullable|string|max:100',
            'nkp'               => 'required|numeric|min:0|max:120',
            'predikat_kinerja'  => 'required|in:Sangat Baik,Baik,Butuh Perbaikan,Kurang,Sangat Kurang',

            // Kompetensi — opsional, tapi kalau diisi harus valid
            'kompetensi_teknis'    => 'nullable|numeric|min:0|max:100',
            'kompetensi_mansoskul' => 'nullable|numeric|min:0|max:100',

            // 9 Komponen Potensi — opsional
            'potensi_1' => 'nullable|numeric|min:0|max:100',
            'potensi_2' => 'nullable|numeric|min:0|max:100',
            'potensi_3' => 'nullable|numeric|min:0|max:100',
            'potensi_4' => 'nullable|numeric|min:0|max:100',
            'potensi_5' => 'nullable|numeric|min:0|max:100',
            'potensi_6' => 'nullable|numeric|min:0|max:100',
            'potensi_7' => 'nullable|numeric|min:0|max:100',
            'potensi_8' => 'nullable|numeric|min:0|max:100',
            'potensi_9' => 'nullable|numeric|min:0|max:100',

            // Rekam Jejak — opsional
            'rj_pendidikan_formal' => 'nullable|numeric|min:0|max:100',
            'rj_pelatihan'         => 'nullable|numeric|min:0|max:100',
            'rj_pengalaman'        => 'nullable|numeric|min:0|max:100',
            'rj_integritas'        => 'nullable|numeric|min:0|max:100',
            'rj_moralitas'         => 'nullable|numeric|min:0|max:100',
        ];
        $rules['ue1'] = $user->isOperator() ? 'nullable' : 'required|integer|min:1|max:14';
        $v = $request->validate($rules);

        if ($user->isOperator()) $v['ue1'] = $user->ue1;

        // Hitung kategori kinerja
        $v['kategori_kinerja']     = Pegawai::kategoriDariPredikat($v['predikat_kinerja']);
        $v['kategori_kinerja_nkp'] = Pegawai::kategoriDariNkp((float) $v['nkp']);

        // Hitung nilai potensi total
        $nilaiTotal = Pegawai::hitungTotal($v);
        $v['nilai_potensi_total'] = $nilaiTotal;

        // Kategori potensial ditentukan dari nilai total jika ada, fallback ke input manual
        if ($nilaiTotal !== null) {
            $v['kategori_potensial'] = Pegawai::kategoriDariNilaiTotal($nilaiTotal);
        } else {
            // Jika data kompetensi/potensi/rekam jejak belum lengkap, ambil dari input
            $v['kategori_potensial'] = $request->input('kategori_potensial', 'potensial rendah');
        }

        $v['box']          = Pegawai::calculateBox($v['kategori_potensial'], $v['kategori_kinerja']);
        $v['box_kemenkeu'] = Pegawai::calculateBoxKemenkeu($v['kategori_potensial'], $v['kategori_kinerja_nkp']);

        Pegawai::create($v);
        return redirect()->route('pegawai.daftar')
            ->with('success', "Data berhasil ditambahkan. Box PAN-RB: {$v['box']} | Box Kemenkeu: {$v['box_kemenkeu']}" .
                ($nilaiTotal !== null ? " | Nilai Potensi: " . number_format($nilaiTotal, 2) : ''));
    }

    // ─── IMPORT ───────────────────────────────────────────────────────────────
    public function importForm()
    {
        return view('pegawai.import');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:10240']);
        $user = $this->currentUser();

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $rows        = $spreadsheet->getActiveSheet()->toArray();
            $header      = array_shift($rows);
            $colMap      = $this->detectColumns($header);

            if (!isset($colMap['nip'], $colMap['nkp'], $colMap['predikat']))
                return back()->with('error', 'Kolom wajib tidak ditemukan: NIP, NKP, Predikat Kinerja.');
            if (!$user->isOperator() && !isset($colMap['ue1']))
                return back()->with('error', 'Kolom UE1 tidak ditemukan.');

            $imported = 0; $skipped = 0; $errors = [];
            DB::beginTransaction();

            foreach ($rows as $i => $row) {
                $rowNum   = $i + 2;
                $nip      = trim($row[$colMap['nip']] ?? '');
                if (empty($nip)) continue;

                $nkpRaw   = trim($row[$colMap['nkp']] ?? '');
                $predikat = $this->normalizePredikat(trim($row[$colMap['predikat']] ?? ''));
                $nama     = isset($colMap['nama'])    ? trim($row[$colMap['nama']] ?? '')    : null;
                $jabatan  = isset($colMap['jabatan']) ? trim($row[$colMap['jabatan']] ?? '') : null;
                $jenjang  = isset($colMap['jenjang']) ? trim($row[$colMap['jenjang']] ?? '') : null;

                if (!is_numeric($nkpRaw)) { $skipped++; $errors[] = "Baris {$rowNum}: NKP tidak valid"; continue; }
                if (!$predikat)           { $skipped++; $errors[] = "Baris {$rowNum}: Predikat tidak valid"; continue; }

                $nkp = (float) $nkpRaw;

                if ($user->isOperator()) {
                    $ue1 = $user->ue1;
                } else {
                    $ue1 = $this->normalizeUe1(trim($row[$colMap['ue1']] ?? ''));
                    if (!$ue1) { $skipped++; $errors[] = "Baris {$rowNum}: UE1 tidak valid"; continue; }
                }

                // Kompetensi
                $teknis    = $this->nullableFloat($row, $colMap, 'kompetensi_teknis');
                $mansoskul = $this->nullableFloat($row, $colMap, 'kompetensi_mansoskul');

                // 9 Komponen Potensi
                $komponen = [];
                for ($k = 1; $k <= 9; $k++) {
                    $komponen["potensi_{$k}"] = $this->nullableFloat($row, $colMap, "potensi_{$k}");
                }

                // Rekam Jejak
                $rjPendFormal = $this->nullableFloat($row, $colMap, 'rj_pendidikan_formal');
                $rjPelatihan  = $this->nullableFloat($row, $colMap, 'rj_pelatihan');
                $rjPengalaman = $this->nullableFloat($row, $colMap, 'rj_pengalaman');
                $rjIntegritas = $this->nullableFloat($row, $colMap, 'rj_integritas');
                $rjMoralitas  = $this->nullableFloat($row, $colMap, 'rj_moralitas');

                $katKinerja    = Pegawai::kategoriDariPredikat($predikat);
                $katKinerjaNkp = Pegawai::kategoriDariNkp($nkp);

                // Hitung nilai total
                $dataForCalc = array_merge([
                    'kompetensi_teknis'    => $teknis,
                    'kompetensi_mansoskul' => $mansoskul,
                    'rj_pendidikan_formal' => $rjPendFormal,
                    'rj_pelatihan'         => $rjPelatihan,
                    'rj_pengalaman'        => $rjPengalaman,
                    'rj_integritas'        => $rjIntegritas,
                    'rj_moralitas'         => $rjMoralitas,
                ], $komponen);
                $nilaiTotal = Pegawai::hitungTotal($dataForCalc);

                // Kategori potensial
                if ($nilaiTotal !== null) {
                    $katPotensial = Pegawai::kategoriDariNilaiTotal($nilaiTotal);
                } else {
                    // Fallback: coba baca dari kolom potensial di Excel
                    $potensialRaw = isset($colMap['potensial']) ? trim($row[$colMap['potensial']] ?? '') : '';
                    $katPotensial = $potensialRaw ? ($this->normalizePotensial($potensialRaw) ?? 'potensial rendah') : 'potensial rendah';
                }

                Pegawai::updateOrCreate(['nip' => $nip], array_merge([
                    'nama'                 => $nama ?: null,
                    'jabatan'              => $jabatan ?: null,
                    'jenjang_jabatan'      => $jenjang ?: null,
                    'nkp'                  => $nkp,
                    'predikat_kinerja'     => $predikat,
                    'ue1'                  => $ue1,
                    'kategori_kinerja'     => $katKinerja,
                    'kategori_kinerja_nkp' => $katKinerjaNkp,
                    'kategori_potensial'   => $katPotensial,
                    'box'                  => Pegawai::calculateBox($katPotensial, $katKinerja),
                    'box_kemenkeu'         => Pegawai::calculateBoxKemenkeu($katPotensial, $katKinerjaNkp),
                    'kompetensi_teknis'    => $teknis,
                    'kompetensi_mansoskul' => $mansoskul,
                    'rj_pendidikan_formal' => $rjPendFormal,
                    'rj_pelatihan'         => $rjPelatihan,
                    'rj_pengalaman'        => $rjPengalaman,
                    'rj_integritas'        => $rjIntegritas,
                    'rj_moralitas'         => $rjMoralitas,
                    'nilai_potensi_total'  => $nilaiTotal,
                ], $komponen));
                $imported++;
            }
            DB::commit();

            $msg = "Berhasil import {$imported} data.";
            if ($skipped) $msg .= " {$skipped} dilewati.";
            return redirect()->route('pegawai.daftar')->with('success', $msg)->with('import_errors', $errors);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // ─── DESTROY ──────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $p = Pegawai::findOrFail($id);
        if ($this->currentUser()->isOperator() && $p->ue1 !== $this->currentUser()->ue1) abort(403);
        $p->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }

    public function destroyAll()
    {
        $user = $this->currentUser();
        
        // Only Admin or Super Admin can delete ALL data
        if (!$user->isSuperAdmin()) {
            abort(403, 'Hanya Admin atau Super Admin yang dapat menghapus semua data.');
        }
        
        try {
            // Use truncate for efficiency (resets auto-increment)
            Pegawai::truncate();
            
            return redirect()->route('pegawai.daftar')
                ->with('success', 'Semua data pegawai berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('pegawai.daftar')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function destroyAllUnit()
    {
        $user = $this->currentUser();
        if (!$user->isOperator() || !$user->ue1) abort(403);
        $count = Pegawai::where('ue1', $user->ue1)->count();
        Pegawai::where('ue1', $user->ue1)->delete();
        return redirect()->route('pegawai.daftar')
            ->with('success', "Berhasil menghapus {$count} data pegawai " . (Pegawai::UE1_SHORT[$user->ue1] ?? 'Unit') . '.');
    }

    // ─── DOWNLOAD TEMPLATE ────────────────────────────────────────────────────
    public function downloadTemplate()
    {
        $user = $this->currentUser();
        $ss   = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sh   = $ss->getActiveSheet()->setTitle('Data Pegawai');
        $hs   = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F4E79']],
        ];
        $noteStyle = ['font' => ['italic' => true, 'color' => ['argb' => 'FF666666']]];

        // Header kolom berdasarkan role
        $baseHeaders = $user->isOperator()
            ? ['NIP', 'Nama', 'Jabatan', 'Jenjang Jabatan', 'NKP', 'Predikat Kinerja']
            : ['NIP', 'Nama', 'UE1', 'Jabatan', 'Jenjang Jabatan', 'NKP', 'Predikat Kinerja'];

        $extraHeaders = [
            'Kompetensi Teknis', 'Kompetensi Manajerial & Sosio-Kultural',
            'Potensi Komponen 1', 'Potensi Komponen 2', 'Potensi Komponen 3',
            'Potensi Komponen 4', 'Potensi Komponen 5', 'Potensi Komponen 6',
            'Potensi Komponen 7', 'Potensi Komponen 8', 'Potensi Komponen 9',
            'RJ Pendidikan Formal', 'RJ Pelatihan', 'RJ Pengalaman',
            'RJ Integritas', 'RJ Moralitas',
        ];

        $headers = array_merge($baseHeaders, $extraHeaders);
        $sh->fromArray([$headers]);

        // Contoh data
        $baseExample = $user->isOperator()
            ? ['196505041********', 'Contoh Pegawai', 'Analis Kebijakan', 'JPT Madya', '95.50', 'Baik']
            : ['196505041********', 'Contoh Pegawai', '4', 'Analis Kebijakan', 'JPT Madya', '95.50', 'Baik'];

        $extraExample = [85, 80, 75, 80, 70, 85, 90, 75, 80, 85, 90, 70, 75, 80, 85, 90];
        $sh->fromArray([array_merge($baseExample, $extraExample)], null, 'A2');

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sh->getStyle("A1:{$lastCol}1")->applyFromArray($hs);
        foreach (range('A', $lastCol) as $c) $sh->getColumnDimension($c)->setAutoSize(true);

        // Catatan
        $noteRow = 4;
        $notes = [
            'CATATAN:',
            'NKP: nilai desimal, contoh 95.50 (>100=Di atas, 90-100=Sesuai, <90=Di bawah ekspektasi)',
            'Predikat Kinerja: Sangat Baik | Baik | Butuh Perbaikan | Kurang | Sangat Kurang',
            'Kompetensi Teknis & Mansoskul: nilai 0-100',
            'Potensi 1-9: nilai 0-100 masing-masing',
            'Rekam Jejak (Pendidikan Formal, Pelatihan, Pengalaman, Integritas, Moralitas): nilai 0-100',
            'Jika semua kolom kompetensi/potensi/rekam jejak diisi, kategori potensial otomatis dihitung (>=90=Tinggi, >=78=Menengah, <78=Rendah)',
            'Jika kolom tersebut kosong, sistem menggunakan kolom Kategori Potensial jika ada, atau default ke potensial rendah',
        ];
        if (!$user->isOperator()) {
            array_splice($notes, 1, 0, ['UE1: kode angka 1-14 (lihat sheet Referensi UE1)']);
        }
        foreach ($notes as $j => $note) {
            $sh->setCellValue("A" . ($noteRow + $j), $note);
        }
        $sh->getStyle("A{$noteRow}:A" . ($noteRow + count($notes) - 1))->applyFromArray($noteStyle);
        $sh->getStyle("A{$noteRow}")->getFont()->setBold(true)->setItalic(false);

        if (!$user->isOperator()) {
            // Sheet referensi UE1
            $ref = $ss->createSheet()->setTitle('Referensi UE1');
            $ref->setCellValue('A1', 'Kode')->setCellValue('B1', 'Nama Unit');
            $ref->getStyle('A1:B1')->applyFromArray($hs);
            foreach (Pegawai::UE1_LIST as $code => $name) {
                $r = $code + 1;
                $ref->setCellValue("A{$r}", $code)->setCellValue("B{$r}", $name);
            }
            $ref->getColumnDimension('A')->setAutoSize(true);
            $ref->getColumnDimension('B')->setAutoSize(true);
        }

        // Sheet referensi bobot
        $refB = $ss->createSheet()->setTitle('Bobot Penilaian');
        $refB->fromArray([['Dimensi', 'Sub-dimensi', 'Bobot Sub', 'Bobot Total']]);
        $refB->getStyle('A1:D1')->applyFromArray($hs);
        $bobotData = [
            ['Kompetensi', 'Teknis', '50%', '35%'],
            ['Kompetensi (70%)', 'Manajerial & Sosio-Kultural', '50%', '35%'],
            ['Potensi (20%)', 'Komponen 1–9 (rata-rata)', '100%', '20%'],
            ['Rekam Jejak (10%)', 'Pendidikan Formal', '20%', '2%'],
            ['Rekam Jejak (10%)', 'Pelatihan', '20%', '2%'],
            ['Rekam Jejak (10%)', 'Pengalaman', '20%', '2%'],
            ['Rekam Jejak (10%)', 'Integritas', '20%', '2%'],
            ['Rekam Jejak (10%)', 'Moralitas', '20%', '2%'],
            ['', '', 'TOTAL', '100%'],
            ['', 'Kategori Potensial:', '≥ 90', 'Tinggi'],
            ['', 'Kategori Potensial:', '≥ 78 s.d < 90', 'Menengah'],
            ['', 'Kategori Potensial:', '< 78', 'Rendah'],
        ];
        $refB->fromArray($bobotData, null, 'A2');
        foreach (['A', 'B', 'C', 'D'] as $c) $refB->getColumnDimension($c)->setAutoSize(true);

        $ss->setActiveSheetIndex(0);
        $path = storage_path('app/template_talent_mapping.xlsx');
        IOFactory::createWriter($ss, 'Xlsx')->save($path);
        return response()->download($path, 'template_manajemen_talenta.xlsx')->deleteFileAfterSend(true);
    }

    // ─── EXPORT ───────────────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $q = $this->scopedQuery();
        if ($request->filled('ue1') && $this->currentUser()->canAccessAllUnits()) $q->where('ue1', $request->ue1);
        if ($request->filled('box')) $q->where('box', $request->box);

        $data = $q->orderBy('ue1')->orderBy('box')->orderBy('nip')->get();
        $ss   = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sh   = $ss->getActiveSheet()->setTitle('Data Pegawai');
        $hs   = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F4E79']],
        ];
        $sh->fromArray([[
            'NIP', 'Nama', 'UE1', 'Nama Unit', 'Jabatan', 'Jenjang Jabatan',
            'NKP', 'Predikat Kinerja',
            'Kategori Kinerja (Predikat→PAN-RB)', 'Kategori Kinerja (NKP→Kemenkeu)',
            'Kompetensi Teknis', 'Kompetensi Mansoskul',
            'Potensi K1', 'Potensi K2', 'Potensi K3', 'Potensi K4', 'Potensi K5',
            'Potensi K6', 'Potensi K7', 'Potensi K8', 'Potensi K9',
            'RJ Pend. Formal', 'RJ Pelatihan', 'RJ Pengalaman', 'RJ Integritas', 'RJ Moralitas',
            'Nilai Potensi Total',
            'Kategori Potensial',
            'Box PAN-RB', 'Keterangan Box PAN-RB',
            'Box Kemenkeu', 'Keterangan Box Kemenkeu',
        ]]);

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(32);
        $sh->getStyle("A1:{$lastCol}1")->applyFromArray($hs);

        foreach ($data as $i => $p) {
            $r = $i + 2;
            $sh->fromArray([[
                $p->nip, $p->nama, $p->ue1, $p->ue1_nama,
                $p->jabatan, $p->jenjang_jabatan,
                $p->nkp, $p->predikat_kinerja,
                $p->kategori_kinerja, $p->kategori_kinerja_nkp,
                $p->kompetensi_teknis, $p->kompetensi_mansoskul,
                $p->potensi_1, $p->potensi_2, $p->potensi_3,
                $p->potensi_4, $p->potensi_5, $p->potensi_6,
                $p->potensi_7, $p->potensi_8, $p->potensi_9,
                $p->rj_pendidikan_formal, $p->rj_pelatihan, $p->rj_pengalaman,
                $p->rj_integritas, $p->rj_moralitas,
                $p->nilai_potensi_total,
                $p->kategori_potensial,
                $p->box, Pegawai::getBoxLabel($p->box),
                $p->box_kemenkeu, Pegawai::getBoxKemenkeuLabel($p->box_kemenkeu ?? '-'),
            ]], null, "A{$r}");
        }
        foreach (range('A', $lastCol) as $c) $sh->getColumnDimension($c)->setAutoSize(true);

        $path = storage_path('app/manajemen_talenta_' . date('Y-m-d') . '.xlsx');
        IOFactory::createWriter($ss, 'Xlsx')->save($path);
        return response()->download($path)->deleteFileAfterSend(true);
    }

    // ─── HELPERS ──────────────────────────────────────────────────────────────
    private function nullableFloat(array $row, array $colMap, string $key): ?float
    {
        if (!isset($colMap[$key])) return null;
        $val = trim($row[$colMap[$key]] ?? '');
        return (is_numeric($val) && $val !== '') ? (float)$val : null;
    }

    private function detectColumns(array $h): array
    {
        $m = [];
        foreach ($h as $i => $c) {
            $c = strtolower(trim($c ?? ''));
            if (str_contains($c, 'nip'))                                                             $m['nip']               = $i;
            elseif (str_contains($c, 'nama') || str_contains($c, 'name'))                           $m['nama']              = $i;
            elseif ($c === 'ue1' || str_contains($c, 'unit eselon') || str_contains($c, 'eselon 1'))$m['ue1']               = $i;
            elseif (str_contains($c, 'jabatan') && !str_contains($c, 'jenjang'))                    $m['jabatan']           = $i;
            elseif (str_contains($c, 'jenjang'))                                                     $m['jenjang']           = $i;
            elseif (str_contains($c, 'nkp') || str_contains($c, 'nilai kinerja'))                   $m['nkp']               = $i;
            elseif (str_contains($c, 'predikat'))                                                    $m['predikat']          = $i;
            elseif (str_contains($c, 'potensi') && !preg_match('/\d/', $c) && !str_contains($c, 'komponen')) $m['potensial'] = $i;
            // Kompetensi
            elseif (str_contains($c, 'teknis'))                                                      $m['kompetensi_teknis']    = $i;
            elseif (str_contains($c, 'mansoskul') || str_contains($c, 'manajerial') || str_contains($c, 'sosio')) $m['kompetensi_mansoskul'] = $i;
            // 9 Komponen Potensi
            elseif (preg_match('/potensi.*(komponen\s*)?(\d)/', $c, $matches))                       $m['potensi_' . $matches[2]] = $i;
            // Rekam Jejak
            elseif (str_contains($c, 'pendidikan') || str_contains($c, 'pend'))                     $m['rj_pendidikan_formal'] = $i;
            elseif (str_contains($c, 'pelatihan'))                                                   $m['rj_pelatihan']         = $i;
            elseif (str_contains($c, 'pengalaman'))                                                  $m['rj_pengalaman']        = $i;
            elseif (str_contains($c, 'integritas'))                                                  $m['rj_integritas']        = $i;
            elseif (str_contains($c, 'moralitas'))                                                   $m['rj_moralitas']         = $i;
        }
        return $m;
    }

    private function normalizePredikat(string $v): ?string
    {
        return [
            'sangat baik'     => 'Sangat Baik',
            'baik'            => 'Baik',
            'butuh perbaikan' => 'Butuh Perbaikan',
            'kurang'          => 'Kurang',
            'sangat kurang'   => 'Sangat Kurang',
        ][strtolower(trim($v))] ?? null;
    }

    private function normalizePotensial(string $v): ?string
    {
        return [
            'potensial rendah'   => 'potensial rendah',   'rendah'   => 'potensial rendah',   'low'    => 'potensial rendah',
            'potensial menengah' => 'potensial menengah', 'menengah' => 'potensial menengah', 'medium' => 'potensial menengah', 'sedang' => 'potensial menengah',
            'potensial tinggi'   => 'potensial tinggi',   'tinggi'   => 'potensial tinggi',   'high'   => 'potensial tinggi',
        ][strtolower(trim($v))] ?? null;
    }

    private function normalizeUe1(string $v): ?int
    {
        if (is_numeric($v)) { $n = (int)$v; return ($n >= 1 && $n <= 14) ? $n : null; }
        $l = strtolower($v);
        foreach (Pegawai::UE1_LIST as $c => $nm) if (strtolower($nm) === $l || str_contains(strtolower($nm), $l)) return $c;
        foreach (Pegawai::UE1_SHORT as $c => $s) if (strtolower($s) === $l) return $c;
        return null;
    }
}