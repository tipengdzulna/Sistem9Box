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
        'ue1',
        'kategori_kinerja',
        'kategori_potensial',
        'box',
    ];

    /**
     * Unit Eselon 1 Mapping
     */
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

    /**
     * Short names for UE1 (for compact display)
     */
    public const UE1_SHORT = [
        1  => 'Setjen',
        2  => 'DJSEF',
        3  => 'DJA',
        4  => 'DJP',
        5  => 'DJBC',
        6  => 'DJPb',
        7  => 'DJKN',
        8  => 'DJPK',
        9  => 'DJPPR',
        10 => 'DJSPSK',
        11 => 'Itjen',
        12 => 'BTIIK',
        13 => 'BPPK',
        14 => 'LNSW',
    ];

    /**
     * Get UE1 full name
     */
    public function getUe1NamaAttribute(): string
    {
        return self::UE1_LIST[$this->ue1] ?? '-';
    }

    /**
     * Get UE1 short name
     */
    public function getUe1ShortAttribute(): string
    {
        return self::UE1_SHORT[$this->ue1] ?? '-';
    }

    /**
     * 9-Box Grid Mapping
     */
    public static function calculateBox(string $potensial, string $kinerja): int
    {
        $mapping = [
            'potensial rendah' => [
                'Di bawah ekspektasi' => 1,
                'Sesuai ekspektasi'   => 2,
                'Di atas ekspektasi'  => 4,
            ],
            'potensial menengah' => [
                'Di bawah ekspektasi' => 3,
                'Sesuai ekspektasi'   => 5,
                'Di atas ekspektasi'  => 7,
            ],
            'potensial tinggi' => [
                'Di bawah ekspektasi' => 6,
                'Sesuai ekspektasi'   => 8,
                'Di atas ekspektasi'  => 9,
            ],
        ];

        return $mapping[$potensial][$kinerja] ?? 0;
    }

    /**
     * Get dasar (basis) text based on kinerja category
     */
    public static function getDasar(string $kinerja): string
    {
        return match ($kinerja) {
            'Di atas ekspektasi'  => 'Sangat Baik',
            'Sesuai ekspektasi'   => 'Baik',
            'Di bawah ekspektasi' => 'Butuh Perbaikan, Kurang, dan Sangat Kurang',
            default => '-',
        };
    }

    /**
     * Get box label description
     */
    public static function getBoxLabel(int $box): string
    {
        $labels = [
            1 => 'Kinerja di bawah ekspektasi dan potensial rendah',
            2 => 'Kinerja sesuai ekspektasi dan potensial rendah',
            3 => 'Kinerja di bawah ekspektasi dan potensial menengah',
            4 => 'Kinerja di atas ekspektasi dan potensial rendah',
            5 => 'Kinerja sesuai ekspektasi dan potensial menengah',
            6 => 'Kinerja di bawah ekspektasi dan potensial tinggi',
            7 => 'Kinerja di atas ekspektasi dan potensial menengah',
            8 => 'Kinerja sesuai ekspektasi dan potensial tinggi',
            9 => 'Kinerja di atas ekspektasi dan potensial tinggi',
        ];

        return $labels[$box] ?? '-';
    }
}
