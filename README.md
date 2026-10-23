# SIMPel (Sistem Informasi Manajemen Pelatihan)

<p align="center">
  <img src="public/media/images/logo-bpsdm.png" alt="BPSDM Kaltim Logo" height="60">
</p>

<p align="center">
  <strong>Badan Pengembangan Sumber Daya Manusia (BPSDM) Provinsi Kalimantan Timur</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-red.svg" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.4-blue.svg" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Database-MariaDB_10.11-teal.svg" alt="MariaDB">
  <img src="https://img.shields.io/badge/Branch-2026--lv13-brightgreen.svg" alt="Branch">
</p>

---

## 📖 Tentang SIMPel

**SIMPel (Sistem Informasi Manajemen Pelatihan)** adalah portal dan aplikasi operasional utama BPSDM Provinsi Kalimantan Timur yang digunakan untuk mengelola seluruh siklus hidup pengembangan kompetensi (Bangkom) ASN dan non-ASN di Kalimantan Timur. 

Aplikasi ini mencakup:
- Pendaftaran pelatihan mandiri, seleksi berkas, dan verifikasi peserta diklat.
- Penjadwalan mata pelatihan, penugasan Widyaiswara/Fasilitator, serta pencetakan Surat Tugas (SPWI).
- Presensi daring berbasis QR Code dinamis dan geolocation.
- Penilaian, seminar laporan aksi perubahan (PKA/PKP/Latsar), dan evaluasi penyelenggaraan.
- Penerbitan, penandatanganan elektronik, serta verifikasi keaslian e-Sertifikat via QR Code.

Branch **`2026-lv12`** ini merupakan hasil penulisan ulang arsitektur (*modern framework rewrite*) dari basis legacy Laravel 5.8 (PHP 7.3) ke **Laravel 12** yang berjalan di atas runtime **PHP 8.4**, dengan mempertahankan integritas fungsionalitas dan tampilan antarmuka (UI/UX) 100% identik.

---

## 🚀 Fitur & Peningkatan pada Versi Modern (Laravel 12 / PHP 8.4)

1. **Modern Runtime & Security**:
   - Berjalan pada PHP 8.4 dengan strict typing, efisiensi JIT, dan proteksi memory leak.
   - Peningkatan keamanan session handling, CSRF token, dan sanitasi header download PDF sesuai spesifikasi RFC.
2. **SSO / Single Sign-On Ready**:
   - Integrasi login berbasis Authentik & Keycloak (`OAuth2 Client`) untuk login ASN dan pengelola.
3. **Peningkatan Pustaka Modern**:
   - **DataTables**: `yajra/laravel-datatables-oracle` v12.
   - **Export/Import Excel**: `maatwebsite/excel` v4 & PhpSpreadsheet modern.
   - **PDF Generation**: `barryvdh/laravel-dompdf` v3.
   - **QR Code Engine**: `simplesoftwareio/simple-qrcode` v4 dengan backend ImageMagick (Imagick) untuk stempel sertifikat instan.
4. **Activity Logger Otomatis**:
   - Pencatatan log aktivitas pengguna (URL, IP, method, user agent) otomatis via middleware global.
5. **Sticky Layout & UI Responsif**:
   - Tampilan Dashmix UI dengan Flexbox sticky footer agar selalu rapi di berbagai ukuran layar.

---

## 🛠️ Persyaratan Sistem

- **PHP**: `^8.4`
- **Ekstensi PHP Wajib**:
  - `pdo_mysql`
  - `gd`
  - `imagick` (ImageMagick)
  - `zip`
  - `intl`
  - `bcmath`
  - `opcache`
- **Database**: MariaDB `^10.11` atau MySQL `^8.0`
- **Composer**: `^2.2`

---

## 📦 Panduan Instalasi Lokal

### 1. Clone Repositori
```bash
git clone -b 2026-lv12 https://github.com/wsnsmd/simpel-update.git simpel-v12
cd simpel-v12
```

### 2. Konfigurasi Lingkungan (.env)
Salin berkas konfigurasi lingkungan dan sesuaikan kredensial database Anda:
```bash
cp .env.example .env
```

Sesuaikan parameter utama di `.env`:
```dotenv
APP_NAME="SIMPel BPSDM Kaltim"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_ADMIN_PATH=backend

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=app_simpel
DB_USERNAME=root
DB_PASSWORD="your_password"

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
```

### 3. Instalasi Dependensi Composer
```bash
composer install
```

### 4. Generate Application Key & Optimasi
```bash
php artisan key:generate
php artisan config:clear
php artisan route:cache
php artisan view:clear
```

### 5. Menjalankan Aplikasi
Untuk pengembangan lokal:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
Buka browser di `http://localhost:8000` (Frontend) atau `http://localhost:8000/backend/login` (Admin Portal).

---

## 🐳 Panduan Menggunakan Runtime Docker (Opsional)

Jika server host tidak memiliki PHP 8.4 secara native, gunakan image custom yang telah disiapkan:

```bash
# Build image PHP 8.4 custom dengan Imagick & ekstensi lengkap:
docker build -t simpel-php:8.4 /root/docker-php84

# Menjalankan artisan serve:
php84 artisan serve --host=0.0.0.0 --port=8000
```

---

## 📄 Lisensi & Hak Cipta

Aplikasi ini dikembangkan dan dikelola oleh **Tim IT / Pranata Komputer BPSDM Provinsi Kalimantan Timur** untuk mendukung penyelenggaraan pelatihan dan pengembangan kompetensi aparatur. Hak Cipta &copy; BPSDM Provinsi Kalimantan Timur.
