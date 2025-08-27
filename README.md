# SIMRS Sederhana - Laravel

Aplikasi **Sistem Informasi Manajemen Rumah Sakit (SIMRS)** sederhana berbasis Laravel.

## Fitur

- **Manajemen User**: Admin, Dokter, Petugas
- **Manajemen Obat**: CRUD, pencarian, filter kategori & status
- **Manajemen Pasien**: (akan dikembangkan)
- **Pendaftaran**: (akan dikembangkan)
- **Manajemen Resep**: (akan dikembangkan)
- **Manajemen Poli**: Seeder sudah tersedia

## Instalasi

1. **Clone repository**
   ```
   git clone <repo-url>
   cd <nama-folder>
   ```

2. **Install dependency**
   ```
   composer install
   npm install && npm run dev
   ```

3. **Copy file environment**
   ```
   cp .env.example .env
   ```

4. **Atur konfigurasi database** di file `.env`

5. **Generate key**
   ```
   php artisan key:generate
   ```

6. **Migrasi dan seeder**
   ```
   php artisan migrate --seed
   ```

   Seeder akan otomatis membuat:
   - 1 user admin:  
     **Email:** simrspr@gmail.com  
     **Password:** testprogramer1
   - 1 user dokter:  
     **Email:** dokter@simrs.com  
     **Password:** password
   - 1 user petugas:  
     **Email:** petugas@simrs.com  
     **Password:** password
   - Data master poli & obat

7. **Jalankan aplikasi**
   ```
   php artisan serve
   ```

## Login

- **Admin:** simrspr@gmail.com / testprogramer1
- **Dokter:** dokter@simrs.com / password
- **Petugas:** petugas@simrs.com / password

## Struktur Menu

- Dashboard
- Manajemen Pasien
- Pendaftaran
- Manajemen Obat
- Resep
- Manajemen User
- Pengaturan

## Catatan

- Fitur manajemen pasien, pendaftaran, dan resep masih dalam pengembangan.
- Untuk pengujian, gunakan user yang sudah disediakan oleh seeder.

## Kontribusi

Pull request dan issue sangat terbuka untuk pengembangan lebih lanjut.

## Lisensi

MIT License
