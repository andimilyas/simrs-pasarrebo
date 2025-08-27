<?php

namespace Database\Seeders;

use App\Models\Obat;
use Illuminate\Database\Seeder;

class ObatSeeder extends Seeder
{
    public function run(): void
    {
        $obat = [
            [
                'nama_obat' => 'Paracetamol 500mg',
                'kategori' => 'Analgesik',
                'satuan' => 'Tablet',
                'dosis' => '1-2 tablet 3x sehari',
                'indikasi' => 'Mengatasi demam dan nyeri ringan',
                'kontraindikasi' => 'Hipersensitivitas terhadap paracetamol',
                'efek_samping' => 'Mual, muntah, reaksi alergi',
                'harga' => 5000,
                'stok' => 1000,
                'minimal_stok' => 100,
                'status' => 'Aktif',
            ],
            [
                'nama_obat' => 'Amoxicillin 500mg',
                'kategori' => 'Antibiotik',
                'satuan' => 'Kapsul',
                'dosis' => '1 kapsul 3x sehari',
                'indikasi' => 'Infeksi bakteri ringan hingga sedang',
                'kontraindikasi' => 'Alergi penisilin',
                'efek_samping' => 'Mual, diare, ruam kulit',
                'harga' => 15000,
                'stok' => 500,
                'minimal_stok' => 50,
                'status' => 'Aktif',
            ],
            [
                'nama_obat' => 'Ibuprofen 400mg',
                'kategori' => 'Analgesik',
                'satuan' => 'Tablet',
                'dosis' => '1 tablet 3-4x sehari',
                'indikasi' => 'Mengatasi nyeri dan peradangan',
                'kontraindikasi' => 'Ulkus lambung, gangguan ginjal',
                'efek_samping' => 'Nyeri lambung, pusing',
                'harga' => 8000,
                'stok' => 800,
                'minimal_stok' => 80,
                'status' => 'Aktif',
            ],
            [
                'nama_obat' => 'Vitamin C 1000mg',
                'kategori' => 'Vitamin',
                'satuan' => 'Tablet',
                'dosis' => '1 tablet 1x sehari',
                'indikasi' => 'Suplemen vitamin C',
                'kontraindikasi' => 'Hipersensitivitas',
                'efek_samping' => 'Diare, mual',
                'harga' => 3000,
                'stok' => 1200,
                'minimal_stok' => 120,
                'status' => 'Aktif',
            ],
            [
                'nama_obat' => 'Omeprazole 20mg',
                'kategori' => 'Antasida',
                'satuan' => 'Kapsul',
                'dosis' => '1 kapsul 1x sehari',
                'indikasi' => 'Mengatasi asam lambung berlebih',
                'kontraindikasi' => 'Hipersensitivitas',
                'efek_samping' => 'Sakit kepala, mual',
                'harga' => 25000,
                'stok' => 300,
                'minimal_stok' => 30,
                'status' => 'Aktif',
            ],
            [
                'nama_obat' => 'Cetirizine 10mg',
                'kategori' => 'Antihistamin',
                'satuan' => 'Tablet',
                'dosis' => '1 tablet 1x sehari',
                'indikasi' => 'Mengatasi alergi dan rinitis',
                'kontraindikasi' => 'Hipersensitivitas',
                'efek_samping' => 'Mengantuk, mulut kering',
                'harga' => 12000,
                'stok' => 400,
                'minimal_stok' => 40,
                'status' => 'Aktif',
            ],
            [
                'nama_obat' => 'Betadine 10%',
                'kategori' => 'Antiseptik',
                'satuan' => 'Botol',
                'dosis' => 'Oleskan pada luka 2-3x sehari',
                'indikasi' => 'Antiseptik untuk luka',
                'kontraindikasi' => 'Hipersensitivitas terhadap iodine',
                'efek_samping' => 'Iritasi kulit',
                'harga' => 18000,
                'stok' => 200,
                'minimal_stok' => 20,
                'status' => 'Aktif',
            ],
            [
                'nama_obat' => 'Salbutamol 2mg',
                'kategori' => 'Bronkodilator',
                'satuan' => 'Tablet',
                'dosis' => '1 tablet 3-4x sehari',
                'indikasi' => 'Mengatasi sesak napas',
                'kontraindikasi' => 'Hipersensitivitas',
                'efek_samping' => 'Jantung berdebar, tremor',
                'harga' => 20000,
                'stok' => 250,
                'minimal_stok' => 25,
                'status' => 'Aktif',
            ],
            [
                'nama_obat' => 'Metformin 500mg',
                'kategori' => 'Antidiabetik',
                'satuan' => 'Tablet',
                'dosis' => '1 tablet 2-3x sehari',
                'indikasi' => 'Mengontrol gula darah',
                'kontraindikasi' => 'Gagal ginjal, asidosis',
                'efek_samping' => 'Mual, diare, nyeri perut',
                'harga' => 35000,
                'stok' => 150,
                'minimal_stok' => 15,
                'status' => 'Aktif',
            ],
            [
                'nama_obat' => 'Amlodipine 5mg',
                'kategori' => 'Antihipertensi',
                'satuan' => 'Tablet',
                'dosis' => '1 tablet 1x sehari',
                'indikasi' => 'Mengontrol tekanan darah tinggi',
                'kontraindikasi' => 'Hipersensitivitas',
                'efek_samping' => 'Pusing, bengkak kaki',
                'harga' => 28000,
                'stok' => 180,
                'minimal_stok' => 18,
                'status' => 'Aktif',
            ],
        ];

        foreach ($obat as $item) {
            $obatModel = Obat::create($item);
            $obatModel->kode_obat = $obatModel->generateKodeObat();
            $obatModel->save();
        }

        echo "Data obat berhasil ditambahkan!\n";
    }
}
