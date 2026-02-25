<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    /**
     * Get current authenticated user (type-hinted).
     */
    private function currentUser(): User
    {
        /** @var User $user */
        $user = auth()->user();
        return $user;
    }

    // Operator: scoped to their UE1. Admin/Super: all data.
    private function scopedQuery()
    {
        $q = Pegawai::query();
        if ($this->currentUser()->isOperator()) $q->where('ue1', $this->currentUser()->ue1);
        return $q;
    }

    public function index(Request $request)
    {
        $user = $this->currentUser();

        $query = $this->scopedQuery();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nip', 'like', "%{$s}%")->orWhere('nama', 'like', "%{$s}%"));
        }
        if ($request->filled('box')) $query->where('box', $request->box);
        if ($request->filled('ue1') && $user->canAccessAllUnits()) $query->where('ue1', $request->ue1);

        $pegawai = $query->orderBy('box')->orderBy('nip')->paginate(25)->withQueryString();

        // Grid filter: operator locked to own unit
        $gridUe1 = $user->isOperator() ? $user->ue1 : $request->input('grid_ue1');

        $boxQ = $this->scopedQuery()->select('box', DB::raw('count(*) as jumlah'));
        if ($gridUe1 && $user->canAccessAllUnits()) $boxQ->where('ue1', $gridUe1);
        $boxCounts = $boxQ->groupBy('box')->pluck('jumlah', 'box')->toArray();

        $gridTotal = ($user->isOperator())
            ? $this->scopedQuery()->count()
            : ($gridUe1 ? Pegawai::where('ue1', $gridUe1)->count() : Pegawai::count());

        $ue1Counts = $user->isOperator()
            ? [$user->ue1 => $this->scopedQuery()->count()]
            : Pegawai::select('ue1', DB::raw('count(*) as jumlah'))->groupBy('ue1')->orderBy('ue1')->pluck('jumlah', 'ue1')->toArray();

        $totalPegawai = $this->scopedQuery()->count();

        return view('pegawai.index', compact('pegawai', 'boxCounts', 'ue1Counts', 'totalPegawai', 'gridTotal', 'gridUe1'));
    }

    public function create()
    {
        return view('pegawai.create');
    }

    public function store(Request $request)
    {
        $user = $this->currentUser();
        $rules = [
            'nip' => 'required|string|unique:pegawai,nip',
            'nama' => 'nullable|string|max:255',
            'kategori_kinerja' => 'required|in:Di atas ekspektasi,Sesuai ekspektasi,Di bawah ekspektasi',
            'kategori_potensial' => 'required|in:potensial rendah,potensial menengah,potensial tinggi',
        ];
        $rules['ue1'] = $user->isOperator() ? 'nullable' : 'required|integer|min:1|max:14';
        $v = $request->validate($rules);

        if ($user->isOperator()) $v['ue1'] = $user->ue1;
        $v['box'] = Pegawai::calculateBox($v['kategori_potensial'], $v['kategori_kinerja']);

        Pegawai::create($v);
        return redirect()->route('pegawai.index')->with('success', 'Data berhasil ditambahkan. Box: ' . $v['box']);
    }

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
            $rows = $spreadsheet->getActiveSheet()->toArray();
            $header = array_shift($rows);
            $colMap = $this->detectColumns($header);

            if (!isset($colMap['nip'], $colMap['kinerja'], $colMap['potensial']))
                return back()->with('error', 'Kolom NIP/Kinerja/Potensial tidak ditemukan.');
            if (!$user->isOperator() && !isset($colMap['ue1']))
                return back()->with('error', 'Kolom UE1 tidak ditemukan.');

            $imported = 0;
            $skipped = 0;
            $errors = [];
            DB::beginTransaction();

            foreach ($rows as $i => $row) {
                $rowNum = $i + 2;
                $nip = trim($row[$colMap['nip']] ?? '');
                if (empty($nip)) continue;

                $kinerja = $this->normalizeKinerja(trim($row[$colMap['kinerja']] ?? ''));
                $potensial = $this->normalizePotensial(trim($row[$colMap['potensial']] ?? ''));
                $nama = isset($colMap['nama']) ? trim($row[$colMap['nama']] ?? '') : null;

                if (!$kinerja || !$potensial) {
                    $skipped++;
                    $errors[] = "Baris {$rowNum}: Kinerja/potensial tidak valid";
                    continue;
                }

                if ($user->isOperator()) {
                    $ue1 = $user->ue1;
                } else {
                    $ue1 = $this->normalizeUe1(trim($row[$colMap['ue1']] ?? ''));
                    if (!$ue1) {
                        $skipped++;
                        $errors[] = "Baris {$rowNum}: UE1 tidak valid";
                        continue;
                    }
                }

                Pegawai::updateOrCreate(['nip' => $nip], [
                    'nama' => $nama,
                    'ue1' => $ue1,
                    'kategori_kinerja' => $kinerja,
                    'kategori_potensial' => $potensial,
                    'box' => Pegawai::calculateBox($potensial, $kinerja),
                ]);
                $imported++;
            }
            DB::commit();

            $msg = "Berhasil import {$imported} data.";
            if ($skipped) $msg .= " {$skipped} dilewati.";
            return redirect()->route('pegawai.index')->with('success', $msg)->with('import_errors', $errors);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $p = Pegawai::findOrFail($id);
        if ($this->currentUser()->isOperator() && $p->ue1 !== $this->currentUser()->ue1) abort(403);
        $p->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }

    public function destroyAll()
    {
        Pegawai::truncate();
        return redirect()->route('pegawai.index')->with('success', 'Semua data berhasil dihapus.');
    }

    /**
     * Delete all records for operator's own UE1
     */
    public function destroyAllUnit()
    {
        $user = $this->currentUser();

        if (!$user->isOperator() || !$user->ue1) {
            abort(403);
        }

        $count = Pegawai::where('ue1', $user->ue1)->count();
        Pegawai::where('ue1', $user->ue1)->delete();

        $unitName = Pegawai::UE1_SHORT[$user->ue1] ?? 'Unit';
        return redirect()->route('pegawai.index')
            ->with('success', "Berhasil menghapus {$count} data pegawai {$unitName}.");
    }

    public function downloadTemplate()
    {
        $user = $this->currentUser();
        $ss = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sh = $ss->getActiveSheet()->setTitle('Data Pegawai');
        $hs = ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F4E79']]];

        if ($user->isOperator()) {
            $sh->fromArray([['NIP', 'Nama', 'Kategori Kinerja', 'Kategori Potensial'], ['196505041985031001', 'Contoh', 'Di atas ekspektasi', 'potensial menengah']]);
            $sh->getStyle('A1:D1')->applyFromArray($hs);
            foreach (range('A', 'D') as $c) $sh->getColumnDimension($c)->setAutoSize(true);
        } else {
            $sh->fromArray([['NIP', 'Nama', 'UE1', 'Kategori Kinerja', 'Kategori Potensial'], ['196505041985031001', 'Contoh', '4', 'Di atas ekspektasi', 'potensial menengah']]);
            $sh->getStyle('A1:E1')->applyFromArray($hs);
            foreach (range('A', 'E') as $c) $sh->getColumnDimension($c)->setAutoSize(true);
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
        $ss->setActiveSheetIndex(0);
        $path = storage_path('app/template_talent_mapping.xlsx');
        IOFactory::createWriter($ss, 'Xlsx')->save($path);
        return response()->download($path, 'template_talent_mapping.xlsx')->deleteFileAfterSend(true);
    }

    public function export(Request $request)
    {
        $q = $this->scopedQuery();
        if ($request->filled('ue1') && $this->currentUser()->canAccessAllUnits()) $q->where('ue1', $request->ue1);
        if ($request->filled('box')) $q->where('box', $request->box);

        $data = $q->orderBy('ue1')->orderBy('box')->orderBy('nip')->get();
        $ss = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sh = $ss->getActiveSheet()->setTitle('Data Pegawai');
        $hs = ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F4E79']]];
        $sh->fromArray([['NIP', 'Nama', 'UE1', 'Nama Unit', 'Kategori Kinerja', 'Kategori Potensial', 'Box', 'Keterangan']]);
        $sh->getStyle('A1:H1')->applyFromArray($hs);

        foreach ($data as $i => $p) {
            $r = $i + 2;
            $sh->fromArray([$p->nip, $p->nama, $p->ue1, $p->ue1_nama, $p->kategori_kinerja, $p->kategori_potensial, $p->box, Pegawai::getBoxLabel($p->box)], null, "A{$r}");
        }
        foreach (range('A', 'H') as $c) $sh->getColumnDimension($c)->setAutoSize(true);

        $path = storage_path('app/talent_mapping_' . date('Y-m-d') . '.xlsx');
        IOFactory::createWriter($ss, 'Xlsx')->save($path);
        return response()->download($path)->deleteFileAfterSend(true);
    }

    // ─── Helpers ───
    private function detectColumns(array $h): array
    {
        $m = [];
        foreach ($h as $i => $c) {
            $c = strtolower(trim($c ?? ''));
            if (str_contains($c, 'nip')) $m['nip'] = $i;
            elseif (str_contains($c, 'nama') || str_contains($c, 'name')) $m['nama'] = $i;
            elseif ($c === 'ue1' || str_contains($c, 'unit eselon') || str_contains($c, 'eselon 1')) $m['ue1'] = $i;
            elseif (str_contains($c, 'kinerja')) $m['kinerja'] = $i;
            elseif (str_contains($c, 'potensi')) $m['potensial'] = $i;
        }
        return $m;
    }
    private function normalizeKinerja(string $v): ?string
    {
        $map = ['di atas ekspektasi' => 'Di atas ekspektasi', 'diatas ekspektasi' => 'Di atas ekspektasi', 'sangat baik' => 'Di atas ekspektasi', 'sesuai ekspektasi' => 'Sesuai ekspektasi', 'baik' => 'Sesuai ekspektasi', 'di bawah ekspektasi' => 'Di bawah ekspektasi', 'dibawah ekspektasi' => 'Di bawah ekspektasi', 'kurang' => 'Di bawah ekspektasi', 'sangat kurang' => 'Di bawah ekspektasi', 'butuh perbaikan' => 'Di bawah ekspektasi'];
        return $map[strtolower(trim($v))] ?? null;
    }
    private function normalizePotensial(string $v): ?string
    {
        $map = ['potensial rendah' => 'potensial rendah', 'rendah' => 'potensial rendah', 'low' => 'potensial rendah', 'potensial menengah' => 'potensial menengah', 'menengah' => 'potensial menengah', 'medium' => 'potensial menengah', 'sedang' => 'potensial menengah', 'potensial tinggi' => 'potensial tinggi', 'tinggi' => 'potensial tinggi', 'high' => 'potensial tinggi'];
        return $map[strtolower(trim($v))] ?? null;
    }
    private function normalizeUe1(string $v): ?int
    {
        if (is_numeric($v)) {
            $n = (int)$v;
            return ($n >= 1 && $n <= 14) ? $n : null;
        }
        $l = strtolower($v);
        foreach (Pegawai::UE1_LIST as $c => $nm) if (strtolower($nm) === $l || str_contains(strtolower($nm), $l)) return $c;
        foreach (Pegawai::UE1_SHORT as $c => $s) if (strtolower($s) === $l) return $c;
        return null;
    }
}
