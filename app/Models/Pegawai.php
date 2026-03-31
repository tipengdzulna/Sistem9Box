<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'jenjang_jabatan',
        'nkp',
        'predikat_kinerja',
        'ue1',
        'kategori_kinerja',
        'kategori_kinerja_nkp',
        'kategori_potensial',
        'box',
        'box_kemenkeu',

        // ── Kompetensi (bobot 70%) ─────────────────────────
        'kompetensi_teknis',      // 0–100, bobot 50% dari kompetensi
        'kompetensi_mansoskul',   // 0–100, bobot 50% dari kompetensi

        // ── Potensi / 9 Komponen (bobot 20%) ──────────────
        'potensi_1',  // Komponen 1
        'potensi_2',  // Komponen 2
        'potensi_3',  // Komponen 3
        'potensi_4',  // Komponen 4
        'potensi_5',  // Komponen 5
        'potensi_6',  // Komponen 6
        'potensi_7',  // Komponen 7
        'potensi_8',  // Komponen 8
        'potensi_9',  // Komponen 9

        // ── Rekam Jejak (bobot 10%) ────────────────────────
        'rj_pendidikan_formal',  // 0–100, bobot 20% dari rekam jejak
        'rj_pelatihan',          // 0–100, bobot 20% dari rekam jejak
        'rj_pengalaman',         // 0–100, bobot 20% dari rekam jejak
        'rj_integritas',         // 0–100, bobot 20% dari rekam jejak
        'rj_moralitas',          // 0–100, bobot 20% dari rekam jejak

        // ── Nilai total hasil perhitungan ──────────────────
        'nilai_potensi_total',   // hasil akhir 0–100
    ];

    protected $casts = [
        'nkp'                  => 'decimal:2',
        'kompetensi_teknis'    => 'decimal:2',
        'kompetensi_mansoskul' => 'decimal:2',
        'potensi_1'            => 'decimal:2',
        'potensi_2'            => 'decimal:2',
        'potensi_3'            => 'decimal:2',
        'potensi_4'            => 'decimal:2',
        'potensi_5'            => 'decimal:2',
        'potensi_6'            => 'decimal:2',
        'potensi_7'            => 'decimal:2',
        'potensi_8'            => 'decimal:2',
        'potensi_9'            => 'decimal:2',
        'rj_pendidikan_formal' => 'decimal:2',
        'rj_pelatihan'         => 'decimal:2',
        'rj_pengalaman'        => 'decimal:2',
        'rj_integritas'        => 'decimal:2',
        'rj_moralitas'         => 'decimal:2',
        'nilai_potensi_total'  => 'decimal:2',
    ];

    // ── Label nama 9 komponen potensi ─────────────────────
    public const POTENSI_KOMPONEN = [
        1 => 'Komponen 1',
        2 => 'Komponen 2',
        3 => 'Komponen 3',
        4 => 'Komponen 4',
        5 => 'Komponen 5',
        6 => 'Komponen 6',
        7 => 'Komponen 7',
        8 => 'Komponen 8',
        9 => 'Komponen 9',
    ];

    public const PREDIKAT_LIST = [
        'Sangat Baik',
        'Baik',
        'Butuh Perbaikan',
        'Kurang',
        'Sangat Kurang',
    ];

    public const JENJANG_LIST = [
        'JPT Madya',
        'JPT Pratama',
        'JAD Administrator',
        'JAD Pengawas',
        'JAD Pelaksana',
        'JKH Pratama',
        'JKH Madya',
        'JKH Muda',
        'JKH Pertama',
        'JKT Penyelia',
        'JKT Mahir',
        'JKT Terampil',
    ];

    public const UE1_LIST = [
        1  => 'Sekretariat Jenderal',
        2  => 'Direktorat Jenderal Strategi Ekonomi dan Fiskal',
        3  => 'Direktorat Jenderal Anggaran',
        4  => 'Direktorat Jenderal Pajak',
        5  => 'Direktorat Jenderal Bea dan Cukai',
        6  => 'Direktorat Jenderal Perbendaharaan',
        7  => 'Direktorat Jenderal Kekayaan Negara',
        8  => 'Direktorat Jenderal Perimbangan Keuangan',
        9  => 'Direktorat Jenderal Pengelolaan Pembiayaan dan Risiko',
        10 => 'Direktorat Jenderal Stabilitas dan Pengembangan Sektor Keuangan',
        11 => 'Inspektorat Jenderal',
        12 => 'Badan Teknologi, Informasi, dan Intelijen Keuangan',
        13 => 'Badan Pendidikan dan Pelatihan Keuangan',
        14 => 'Lembaga National Single Window',
    ];

    public const UE1_SHORT = [
        1  => 'Setjen',   2  => 'DJSEF',  3  => 'DJA',
        4  => 'DJP',      5  => 'DJBC',   6  => 'DJPb',
        7  => 'DJKN',     8  => 'DJPK',   9  => 'DJPPR',
        10 => 'DJSPSK',   11 => 'Itjen',  12 => 'BTIIK',
        13 => 'BPPK',     14 => 'LNSW',
    ];

    public function getUe1NamaAttribute(): string  { return self::UE1_LIST[$this->ue1] ?? '-'; }
    public function getUe1ShortAttribute(): string { return self::UE1_SHORT[$this->ue1] ?? '-'; }

    // ══════════════════════════════════════════════════════════
    // PERHITUNGAN NILAI POTENSI TOTAL
    // ══════════════════════════════════════════════════════════

    /**
     * Hitung nilai kompetensi (bobot 70% dari total).
     * teknis (50%) + mansoskul (50%) → rata-rata × 70%
     */
    public static function hitungNilaiKompetensi(?float $teknis, ?float $mansoskul): ?float
    {
        if ($teknis === null || $mansoskul === null) return null;
        $rataKompetensi = ($teknis + $mansoskul) / 2;
        return round($rataKompetensi * 0.70, 4);
    }

    /**
     * Hitung nilai potensi (bobot 20% dari total).
     * 9 komponen, bobot sama rata (1/9 masing-masing) → rata-rata × 20%
     */
    public static function hitungNilaiPotensiKomponen(array $komponen): ?float
    {
        $valid = array_filter($komponen, fn($v) => $v !== null && $v !== '');
        if (count($valid) === 0) return null;
        $rata = array_sum($valid) / count($valid);
        return round($rata * 0.20, 4);
    }

    /**
     * Hitung nilai rekam jejak (bobot 10% dari total).
     * 5 aspek, bobot sama rata (20% masing-masing) → rata-rata × 10%
     */
    public static function hitungNilaiRekamJejak(
        ?float $pendFormal,
        ?float $pelatihan,
        ?float $pengalaman,
        ?float $integritas,
        ?float $moralitas
    ): ?float {
        $vals = array_filter(
            [$pendFormal, $pelatihan, $pengalaman, $integritas, $moralitas],
            fn($v) => $v !== null
        );
        if (count($vals) === 0) return null;
        $rata = array_sum($vals) / count($vals);
        return round($rata * 0.10, 4);
    }

    /**
     * Hitung total nilai potensi (0–100).
     * = nilai_kompetensi + nilai_potensi_komponen + nilai_rekam_jejak
     *
     * Jika salah satu komponen null, komponen tersebut dianggap 0
     * dan bobot tetap penuh (supaya bisa input bertahap).
     * Gunakan $strict=true agar return null jika ada komponen yang belum diisi.
     */
    public static function hitungNilaiPotesiTotal(
        ?float $nilaiKompetensi,
        ?float $nilaiPotensiKomponen,
        ?float $nilaiRekamJejak,
        bool $strict = false
    ): ?float {
        if ($strict && ($nilaiKompetensi === null || $nilaiPotensiKomponen === null || $nilaiRekamJejak === null)) {
            return null;
        }
        return round(
            ($nilaiKompetensi ?? 0) + ($nilaiPotensiKomponen ?? 0) + ($nilaiRekamJejak ?? 0),
            2
        );
    }

    /**
     * Helper lengkap: terima semua raw input, return nilai total.
     */
    public static function hitungTotal(array $data): ?float
    {
        $teknis    = isset($data['kompetensi_teknis'])    ? (float)$data['kompetensi_teknis']    : null;
        $mansoskul = isset($data['kompetensi_mansoskul']) ? (float)$data['kompetensi_mansoskul'] : null;

        $komponen = [];
        for ($i = 1; $i <= 9; $i++) {
            $key = 'potensi_' . $i;
            $komponen[] = isset($data[$key]) && $data[$key] !== '' ? (float)$data[$key] : null;
        }

        $pendFormal  = isset($data['rj_pendidikan_formal']) ? (float)$data['rj_pendidikan_formal'] : null;
        $pelatihan   = isset($data['rj_pelatihan'])         ? (float)$data['rj_pelatihan']         : null;
        $pengalaman  = isset($data['rj_pengalaman'])        ? (float)$data['rj_pengalaman']        : null;
        $integritas  = isset($data['rj_integritas'])        ? (float)$data['rj_integritas']        : null;
        $moralitas   = isset($data['rj_moralitas'])         ? (float)$data['rj_moralitas']         : null;

        $nK  = self::hitungNilaiKompetensi($teknis, $mansoskul);
        $nP  = self::hitungNilaiPotensiKomponen($komponen);
        $nRJ = self::hitungNilaiRekamJejak($pendFormal, $pelatihan, $pengalaman, $integritas, $moralitas);

        return self::hitungNilaiPotesiTotal($nK, $nP, $nRJ);
    }

    /**
     * Tentukan kategori potensial dari nilai total.
     * >=90 → tinggi | >=78 → menengah | <78 → rendah
     */
    public static function kategoriDariNilaiTotal(?float $nilaiTotal): string
    {
        if ($nilaiTotal === null) return 'potensial rendah'; // default jika belum lengkap
        if ($nilaiTotal >= 90)   return 'potensial tinggi';
        if ($nilaiTotal >= 78)   return 'potensial menengah';
        return 'potensial rendah';
    }

    // ══════════════════════════════════════════════════════════
    // STATIC HELPERS EXISTING
    // ══════════════════════════════════════════════════════════

    public static function kategoriDariPredikat(string $predikat): string
    {
        return match (strtolower(trim($predikat))) {
            'sangat baik'                              => 'Di atas ekspektasi',
            'baik'                                     => 'Sesuai ekspektasi',
            'butuh perbaikan', 'kurang', 'sangat kurang' => 'Di bawah ekspektasi',
            default                                    => 'Di bawah ekspektasi',
        };
    }

    public static function kategoriDariNkp(float $nkp): string
    {
        if ($nkp > 100) return 'Di atas ekspektasi';
        if ($nkp >= 90) return 'Sesuai ekspektasi';
        return 'Di bawah ekspektasi';
    }

    public static function calculateBox(string $potensial, string $kinerja): int
    {
        $mapping = [
            'potensial rendah'   => ['Di bawah ekspektasi' => 1, 'Sesuai ekspektasi' => 2, 'Di atas ekspektasi' => 4],
            'potensial menengah' => ['Di bawah ekspektasi' => 3, 'Sesuai ekspektasi' => 5, 'Di atas ekspektasi' => 7],
            'potensial tinggi'   => ['Di bawah ekspektasi' => 6, 'Sesuai ekspektasi' => 8, 'Di atas ekspektasi' => 9],
        ];
        return $mapping[$potensial][$kinerja] ?? 0;
    }

    public static function calculateBoxKemenkeu(string $potensial, string $kinerja): string
    {
        $mapping = [
            'potensial rendah'   => ['Di bawah ekspektasi' => '1', 'Sesuai ekspektasi' => '4', 'Di atas ekspektasi' => '5'],
            'potensial menengah' => ['Di bawah ekspektasi' => '2', 'Sesuai ekspektasi' => '6', 'Di atas ekspektasi' => '8'],
            'potensial tinggi'   => ['Di bawah ekspektasi' => '3', 'Sesuai ekspektasi' => '7', 'Di atas ekspektasi' => '9'],
        ];
        return $mapping[$potensial][$kinerja] ?? '-';
    }

    public static function getDasar(string $kinerja): string
    {
        return match ($kinerja) {
            'Di atas ekspektasi'  => 'Sangat Baik',
            'Sesuai ekspektasi'   => 'Baik',
            'Di bawah ekspektasi' => 'Butuh Perbaikan, Kurang, dan Sangat Kurang',
            default               => '-',
        };
    }

    public static function getBoxLabel(int $box): string
    {
        return [
            1 => 'Kinerja di bawah ekspektasi dan potensial rendah',
            2 => 'Kinerja sesuai ekspektasi dan potensial rendah',
            3 => 'Kinerja di bawah ekspektasi dan potensial menengah',
            4 => 'Kinerja di atas ekspektasi dan potensial rendah',
            5 => 'Kinerja sesuai ekspektasi dan potensial menengah',
            6 => 'Kinerja di bawah ekspektasi dan potensial tinggi',
            7 => 'Kinerja di atas ekspektasi dan potensial menengah',
            8 => 'Kinerja sesuai ekspektasi dan potensial tinggi',
            9 => 'Kinerja di atas ekspektasi dan potensial tinggi',
        ][$box] ?? '-';
    }

    public static function getBoxKemenkeuLabel(string $box): string
    {
        return [
            '1' => 'Kinerja di bawah ekspektasi dan kompetensi rendah',
            '2' => 'Kinerja di bawah ekspektasi dan kompetensi menengah',
            '3' => 'Kinerja di bawah ekspektasi dan kompetensi tinggi',
            '4' => 'Kinerja sesuai ekspektasi dan kompetensi rendah',
            '5' => 'Kinerja di atas ekspektasi dan kompetensi rendah',
            '6' => 'Kinerja sesuai ekspektasi dan kompetensi menengah',
            '7' => 'Kinerja sesuai ekspektasi dan kompetensi tinggi',
            '8' => 'Kinerja di atas ekspektasi dan kompetensi menengah',
            '9' => 'Kinerja di atas ekspektasi dan kompetensi tinggi',
        ][$box] ?? '-';
    }
}