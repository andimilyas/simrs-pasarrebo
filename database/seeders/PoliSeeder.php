<?php

namespace Database\Seeders;

use App\Models\Poli;
use Illuminate\Database\Seeder;

class PoliSeeder extends Seeder
{
    public function run(): void
    {
        $polis = [
            [
                'nama_poli' => 'Poli Umum',
                'kode_poli' => 'UMU',
                'deskripsi' => 'Poli untuk pemeriksaan umum dan konsultasi kesehatan',
                'biaya' => 75000,
                'kapasitas_harian' => 50,
                'jam_buka' => '08:00:00',
                'jam_tutup' => '16:00:00',
                'status' => 'Aktif',
            ],
            [
                'nama_poli' => 'Poli Gigi',
                'kode_poli' => 'GIG',
                'deskripsi' => 'Poli untuk perawatan gigi dan mulut',
                'biaya' => 100000,
                'kapasitas_harian' => 30,
                'jam_buka' => '08:00:00',
                'jam_tutup' => '15:00:00',
                'status' => 'Aktif',
            ],
            [
                'nama_poli' => 'Poli Anak',
                'kode_poli' => 'ANK',
                'deskripsi' => 'Poli khusus untuk pemeriksaan anak-anak',
                'biaya' => 85000,
                'kapasitas_harian' => 40,
                'jam_buka' => '08:00:00',
                'jam_tutup' => '16:00:00',
                'status' => 'Aktif',
            ],
            [
                'nama_poli' => 'Poli Kebidanan',
                'kode_poli' => 'KBD',
                'deskripsi' => 'Poli untuk pemeriksaan kehamilan dan persalinan',
                'biaya' => 120000,
                'kapasitas_harian' => 25,
                'jam_buka' => '08:00:00',
                'jam_tutup' => '15:00:00',
                'status' => 'Aktif',
            ],
            [
                'nama_poli' => 'Poli Bedah',
                'kode_poli' => 'BDH',
                'deskripsi' => 'Poli untuk konsultasi bedah dan operasi',
                'biaya' => 150000,
                'kapasitas_harian' => 20,
                'jam_buka' => '09:00:00',
                'jam_tutup' => '14:00:00',
                'status' => 'Aktif',
            ],
            [
                'nama_poli' => 'Poli Jantung',
                'kode_poli' => 'JNT',
                'deskripsi' => 'Poli untuk pemeriksaan jantung dan pembuluh darah',
                'biaya' => 200000,
                'kapasitas_harian' => 15,
                'jam_buka' => '08:00:00',
                'jam_tutup' => '15:00:00',
                'status' => 'Aktif',
            ],
            [
                'nama_poli' => 'Poli Saraf',
                'kode_poli' => 'SRF',
                'deskripsi' => 'Poli untuk pemeriksaan saraf dan otak',
                'biaya' => 180000,
                'kapasitas_harian' => 20,
                'jam_buka' => '09:00:00',
                'jam_tutup' => '16:00:00',
                'status' => 'Aktif',
            ],
            [
                'nama_poli' => 'Poli Mata',
                'kode_poli' => 'MTA',
                'deskripsi' => 'Poli untuk pemeriksaan mata dan penglihatan',
                'biaya' => 120000,
                'kapasitas_harian' => 25,
                'jam_buka' => '08:00:00',
                'jam_tutup' => '15:00:00',
                'status' => 'Aktif',
            ],
        ];

        foreach ($polis as $poli) {
            Poli::create($poli);
        }

        echo "Data poli berhasil ditambahkan!\n";
    }
}
