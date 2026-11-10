# 💳 SubTrack &bull; Tracker Langganan &amp; Free Trial

Aplikasi web **CRUD Tracker Langganan &amp; Free Trial** modern, responsif, full‑stack yang dibangun dengan **Laravel 12** dan **Bootstrap 5.3**.

SubTrack menyelesaikan masalah keuangan nyata: mengelola langganan berulang (Netflix, Spotify, penyimpanan cloud, keanggotaan gym, alat pengembang) dan mengawasi free trial sebelum otomatis memperbarui dan menagih kartu Anda. Dirancang dengan palet fintech bersih dan modern (**Deep Indigo, Slate Navy, Emerald, dan Amber** — tanpa tema pink), dukungan mode gelap/terang, serta visualisasi data interaktif menggunakan **Chart.js**.

---

## 🌟 Fitur Utama

### 1. 🔐 Otentikasi & Fungsi Pengguna
- **Registrasi & Masuk**: Alur otentikasi lengkap dengan validasi input, hashing password bcrypt, dan manajemen sesi *remember‑me*.
- **Login Demo 1‑Klik**: Akun demo yang sudah di‑seed (`demo@example.com` / `password123`) berisi 13 layanan realistis.
- **Dukungan Multi‑Currency**: Beralih antara `$`, `Rp`, `€`, `£`, dan `¥` di pengaturan pengguna; semua total, perhitungan bulanan, dan diagram menyesuaikan secara otomatis.
- **Pelacakan Anggaran**: Tetapkan batas anggaran bulanan dan pantau penggunaan anggaran secara real‑time.
- **Privasi Data Ketat**: Isolasi pengguna penuh — setiap pengguna hanya dapat mengakses langganannya sendiri.

### 2. 📊 Dashboard & Intelijen Keuangan
- **Total Pengeluaran Bulanan**: Menghitung pengeluaran bulanan ternormalkan dari siklus penagihan campuran (Mingguan `× 52 ÷ 12`, Bulanan, Tahunan `÷ 12`).
- **Proyeksi Pengeluaran Tahunan**: Proyeksi otomatis selama 12 bulan.
- **Banner Peringatan Pembayaran Mendesak**: Muncul secara dinamis ketika ada langganan yang jatuh tempo dalam **≤ 3 hari** atau terlambat.
- **Tampilan Khusus: "Pembayaran Jatuh Tempo 7 Hari Berikutnya"**: Timeline kronologis tenggat pembaruan yang akan datang.
- **Widget Free Trial Watchdog**: Pelacak hitung mundur dengan bar progres visual.
- **Meter Batas Anggaran**: Indikator visual persentase penggunaan anggaran bulanan (`✓ On track`, `⚡ Warning`, atau `⚠ Over budget`).

### 3. 🔁 CRUD & Manajemen Siklus Hidup Lengkap
- **Create**: Tambahkan langganan baru dengan nama layanan, harga, siklus, tanggal penagihan berikutnya, kategori, status, ikon/emoji, dan catatan. Menyertakan kalkulator pratinjau biaya bulanan/tahunan dan preset 1‑klik (Spotify, Netflix, Copilot, Gym, dll.).
- **Read**:
  - **Tampilan Card Grid**: Kartu modern berbentuk squircle dengan ikon, harga, jadwal pembaruan, dan dropdown aksi cepat.
  - **Tampilan Table**: Tabel data lengkap dengan label status, penyortiran, dan indikator filter.
- **Update**: Ubah tarif ketika layanan mengumumkan kenaikan harga, ubah siklus penagihan, atau ubah status.
- **1‑Click Renewal ("Mark as Paid")**: Memajukan tanggal penagihan +1 siklus (Mingguan `+1 week`, Bulanan `+1 month`, Tahunan `+1 year`) dengan satu klik.
- **Delete & Soft Deletes**:
  - **Status "Cancelled"**: Menghentikan penambahan ke total pengeluaran bulanan sambil mempertahankan riwayat lengkap.
  - **Soft Delete (Trash)**: Pindahkan catatan ke Trash dengan kemampuan **Restore** dan **Permanent Delete**.

### 4. 🚀 Tingkatkan Portofolio
- **Filter & Penyortiran**:
  - Filter berdasarkan **Kategori** (Entertainment, Utilities, Health, Work, Education, Food, Shopping, Others).
  - Filter berdasarkan **Status** (All, Active, Free Trial, Cancelled).
  - Filter berdasarkan **Siklus Penagihan** (Monthly, Yearly, Weekly).
  - Pencarian berdasarkan nama layanan dan catatan.
  - Penyortiran: *Renewal Date (Soonest)*, *Highest Cost*, *Lowest Cost*, *Name (A‑Z)*, *Recently Added*.
- **Visualisasi Interaktif (Chart.js)**:
  - **Spending by Category**: Diagram donat interaktif dengan legenda, jumlah, dan persentase anggaran.
  - **Billing Cycle Distribution**: Diagram batang frekuensi membandingkan komitmen Monthly vs Yearly vs Weekly.
  - **Top 5 Expenses Ranking**: Mengidentifikasi pengeluaran terbesar bulanan.
  - **Cancellation Savings Impact**: Menghitung estimasi tabungan bulanan dan tahunan dari layanan yang dibatalkan.
- **Logika Tanggal & Penyorotan Urgensi**:
  - **🔴 Merah / Danger (`card-critical`)**: Langganan yang jatuh tempo dalam **≤ 3 hari** atau terlambat.
  - **🟡 Kuning / Warning (`card-warning`)**: Langganan yang jatuh tempo dalam **4‑7 hari**.
  - **Normal**: Pembaruan yang jatuh tempo lebih dari 7 hari.
- **Ekspor Data**: 1‑klik **Export to CSV** untuk spreadsheet (Excel / Google Sheets).
- **Mode Light & Dark**: Pengalih tema persisten yang disimpan di `localStorage`.
- **Shortcut Keyboard**: Tekan `N` untuk *Quick Add*, `/` untuk fokus pencarian, dan `D` untuk beralih ke mode gelap.
- **Desain Responsif**: Sidebar lengket di desktop + navigasi bawah seluler (`.mobile-bottom-nav`).

---

## 🛠️ Teknologi & Persyaratan

- **PHP**: `^8.2` atau lebih tinggi (diuji pada PHP 8.2 – 8.5)
- **Framework**: [Laravel 12.x](https://laravel.com)
- **Database**: MySQL / MariaDB (mis. XAMPP)
- **Frontend**: Bootstrap 5.3.3, Bootstrap Icons 1.11.3, Google Fonts (*Plus Jakarta Sans*)
- **Chart**: Chart.js 4.4.2
- **Composer**: `^2.0`

---

## 📥 Instalasi & Panduan Setup

Ikuti langkah‑langkah berikut untuk meng‑clone, mengkonfigurasi, dan menjalankan proyek secara lokal.

### 1. Clone Repository
```bash
git clone https://github.com/your-username/crud-langganan.git
cd crud-langganan
```

### 2. Instal Dependensi PHP
```bash
composer install
```

### 3. Konfigurasi File Lingkungan
Salin file contoh `.env`:
```bash
# Pada Windows (PowerShell)
copy .env.example .env
```

Generate kunci enkripsi aplikasi:
```bash
php artisan key:generate
```

### 4. Penyiapan Database
#### Pilihan A: MySQL (mis. XAMPP)
1. Jalankan Apache & MySQL di XAMPP.
2. Buat basis data kosong di phpMyAdmin atau terminal:
```sql
CREATE DATABASE `crud-langganan`;
```
3. Perbarui `.env` dengan detail MySQL Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud-langganan
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Migrasi & Seed Data Demo
```bash
php artisan migrate --seed
```

### 6. Mulai Server Pengembangan
```bash
php artisan serve
```
Buka aplikasi di peramban Anda pada:
👉 **`http://127.0.0.1:8000`**

---

## 🔑 Kredensial Akun Demo
Gunakan kredensial berikut atau klik tombol **"1‑Click Demo Login"** pada halaman masuk:

| Peran | Email | Kata Sandi | Data Pramuat |
|---|---|---|---|
| **Demo User** | `demo@example.com` | `password123` | 13 Langganan (Active, Free Trials, Cancelled, Weekly, Yearly) |
| **Test User** | `test@example.com` | `password` | Akun bersih untuk pengujian kustom |

---

## 🧪 Menjalankan Tes Otomatis
Repository menyertakan rangkaian tes verifikasi lengkap yang mencakup otentikasi, CRUD, logika tanggal, perhitungan, dan keamanan multi‑tenant:

```bash
# 1. Tes otentikasi HTTP & alur sesi lengkap
php tests/test_http_auth.php

# 2. Tes fungsi pengguna, hashing password & isolasi data
php tests/test_auth_and_user.php

# 3. Suite verifikasi sistem 8‑point lengkap
php tests/verify.php
```
Semua suite harus lulus dengan `0 failures`.

---

## 📂 Arsitektur Proyek
```
crud-langganan/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AnalyticsController.php     # Metrix Chart.js & perhitungan penghematan
│   │       ├── AuthController.php          # Login, Register, Demo 1‑Klik, Logout
│   │       ├── DashboardController.php      # Metrix, pembayaran mendatang, pelacakan anggaran
│   │       ├── ProfileController.php        # Nama, email, mata uang, anggaran, password
│   │       ├── SubscriptionController.php   # CRUD lengkap, ekspor CSV, renewal 1‑klik
│   │       └── TrialController.php          # Pengelola free trial & aksi pembatalan
│   └── Models/
│       ├── Subscription.php                 # Scopes, normalisasi bulanan, logika urgensi tanggal
│       └── User.php                         # Auth, formatter mata uang, casts anggaran, relasi
├── database/
│   ├── migrations/                          # Users, currency, subscriptions, monthly budget
│   └── seeders/DatabaseSeeder.php           # Pre‑seed akun Demo dengan 13 layanan realistis
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php                # Layout master dengan sidebar, toggle tema, modal quick add
│       │   └── auth.blade.php               # Layout bersih untuk otentikasi
│       ├── auth/                            # Tampilan Login & Register
│       ├── dashboard/index.blade.php        # Ringkasan dashboard & diagram
│       ├── subscriptions/                   # Index (Cards/Table), Create, Edit, Show, Trash
│       ├── trials/index.blade.php           # Manajer free trial watchdog
│       ├── analytics/index.blade.php        # Analitik visual mendalam & distribusi pengeluaran
│       └── profile/edit.blade.php           # Pengaturan, pilihan mata uang & pembaruan password
├── routes/
│   └── web.php                              # Rute web dengan middleware auth & sumber daya RESTful
└── tests/                                   # Runner tes otomatis standalone
```

---

## 🧭 Referensi Rute Utama
| Metode | URI | Aksi / Controller | Deskripsi |
|---|---|---|---|
| `GET` | `/login` | `AuthController@showLogin` | Formulir masuk |
| `POST` | `/login` | `AuthController@login` | Otentikasi kredensial pengguna |
| `GET` | `/demo-login` | `AuthController@quickDemoLogin` | Login demo 1‑klik |
| `GET` | `/register` | `AuthController@showRegister` | Formulir registrasi |
| `POST` | `/logout` | `AuthController@logout` | Keluar dari pengguna saat ini |
| `GET` | `/dashboard` | `DashboardController@index` | Dashboard utama & metrik |
| `GET` | `/subscriptions` | `SubscriptionController@index` | Telusuri langganan (tabel/kartu) |
| `POST` | `/subscriptions` | `SubscriptionController@store` | Buat langganan baru |
| `GET` | `/subscriptions/{id}` | `SubscriptionController@show` | Lihat detail langganan |
| `PUT` | `/subscriptions/{id}` | `SubscriptionController@update` | Update harga/tanggal/status |
| `DELETE` | `/subscriptions/{id}` | `SubscriptionController@destroy` | Soft‑delete ke Trash |
| `POST` | `/subscriptions/{id}/renew` | `SubscriptionController@renew` | Tandai sebagai dibayar & maju 1 siklus |
| `GET` | `/subscriptions/export/csv` | `SubscriptionController@exportCsv` | Unduh file CSV |
| `GET` | `/subscriptions/trash` | `SubscriptionController@trash` | Tampilan catatan ter‑trash |
| `POST` | `/subscriptions/{id}/restore` | `SubscriptionController@restore` | Pulihkan dari Trash |
| `GET` | `/trials` | `TrialController@index` | Pengawas Free Trial khusus |
| `PATCH` | `/trials/{id}/cancel` | `TrialController@cancel` | Batalkan trial segera |
| `GET` | `/analytics` | `AnalyticsController@index` | Analitik Chart.js interaktif |
| `GET` | `/profile` | `ProfileController@edit` | Pengaturan akun & mata uang |
| `PUT` | `/profile/budget` | `ProfileController@updateBudget` | Perbarui batas anggaran bulanan |

---

## 📄 Lisensi

Proyek sumber terbuka ini dilisensikan di bawah [Lisensi MIT](LICENSE). Anda bebas menggunakannya untuk belajar, showcase portofolio, atau pelacakan keuangan pribadi!