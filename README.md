# Dompet Digital

Aplikasi **manajemen keuangan pribadi** berbasis web yang dibangun dengan Laravel 13, menampilkan arsitektur **session-based SPA-like** dengan Blade + JavaScript dinamis.

---

## Fitur / Menu Aplikasi

### 1. Autentikasi
- **Login** — Masuk dengan username dan password (rate-limited: 10 percobaan/menit)
- **Register** — Daftar akun baru (otomatis membuat 2 dompet default: Dompet Utama & Tabungan)
- **Logout** — Hapus sesi
- **Session Check** — API untuk memverifikasi status sesi

### 2. Dashboard (`/dashboard`)
- **Kartu Dompet Ganda** — Saldo Dompet Utama & Tabungan dalam hero card
- **Ringkasan Statistik** — Total pemasukan, pengeluaran, saldo, jumlah transaksi (seumur hidup + bulan ini)
- **Rasio Pengeluaran** — Persentase pengeluaran terhadap pemasukan bulan ini
- **Rata-rata Bulanan** — Rata-rata pemasukan/pengeluaran per bulan
- **Grafik** — Pemasukan doughnut, Pengeluaran pie, Tren bulanan bar, Pertumbuhan tabungan line
- **Progress Tabungan** — Progress bar menuju target tabungan
- **Aksi Cepat** — Kelola dompet, kelola tabungan, refresh data
- **Transaksi Terbaru** — Tabel 10 transaksi terakhir

### 3. Dompet Utama (`/dompet`)
- **Hero Saldo** — Saldo utama dengan animasi
- **Ringkasan** — Pemasukan, pengeluaran, saldo, total transaksi
- **Filter Transaksi** — Semua, Bulan Ini, Bulan Lalu, pemilih bulan manual
- **Tabel Transaksi** — Search, pagination, edit inline, delete dengan konfirmasi
- **CRUD Transaksi** — Tambah, edit, hapus transaksi pemasukan/pengeluaran
- **Transfer Antar Dompet** — Pindahkan saldo antar dompet (membuat transaksi berpasangan)
- **Ekspor Laporan** — Export ke Excel (XLSX) dan PDF
- **Grafik** — Pie pengeluaran, Doughnut pemasukan

### 4. Tabungan (`/tabungan`)
- **Hero Tabungan** — Saldo tabungan hijau
- **Statistik Tabungan** — Total saved, bulan ini, rata-rata/bulan, total transaksi
- **Target Tabungan** — Progress bar dengan fitur edit target nominal
- **Grafik Pertumbuhan** — Line chart pertumbuhan tabungan
- **Topup** — Transfer dari Dompet Utama ke Tabungan via modal
- **Tabel Transaksi Tabungan** — Search & pagination

### 5. Profil (`/profile`)
- **Avatar** — Pilih dari 19 preset avatar (icon + background warna + label)
- **Quote Motivasi** — Edit quote dengan efek typewriter dan inline editing
- **Edit Profil** — Ubah nama lengkap dan username
- **Ganti Password** — Validasi password saat ini, hint kekuatan password
- **Dark Mode** — Toggle tema gelap/terang

### 6. Admin Panel (`/admin`)
- **Dashboard Admin** — Kartu statistik: total user, transaksi, pemasukan, pengeluaran, saldo utama, saldo tabungan
- **Grafik Admin** — Bar chart pemasukan, pengeluaran, saldo dompet per user
- **Manajemen User** — Tambah, edit, hapus user + buat dompet otomatis
- **Lihat Transaksi User** — Filter per hari/bulan/tahun dengan pagination
- **Ekspor Data User** — Excel & PDF

### 7. Fitur Umum
- **SPA Navigation** — Navigasi tanpa reload penuh menggunakan `js/spa.js`
- **Dark Mode** — Toggle tema dengan persistensi localStorage
- **Splash Screen** — Animasi loading saat masuk aplikasi
- **Animasi Glassmorphism** — UI dengan efek kaca pada halaman auth
- **Refresh Data** — Tombol refresh untuk update data real-time
- **Toast Notifikasi** — SweetAlert2 untuk feedback aksi

---

## Teknologi

| Stack | Detail |
|---|---|
| **Backend** | Laravel 13, PHP 8.3 |
| **Frontend** | Blade, JavaScript (vanilla), TailwindCSS 4 |
| **Database** | SQLite (default) / MySQL |
| **Chart** | Chart.js |
| **Icons** | Font Awesome 6.5.1 |
| **Export** | SheetJS (XLSX), jsPDF + autotable |
| **Notifikasi** | SweetAlert2 |
| **Build** | Vite 8 |

---

## Struktur Database

| Tabel | Deskripsi |
|---|---|
| `users` | id, username, full_name, password, role (user/admin), avatar, quote |
| `wallets` | id, user_id, name, type (main/savings), icon, color, savings_target |
| `transactions` | id, user_id, wallet_id, transaction_date, type (income/expense), category, amount, note |
| `categories` | id, name, type (income/expense/both), icon (7 kategori default) |
| `sessions` | id, user_id, ip_address, user_agent, payload, last_activity |

---

## Peran Pengguna

| Peran | Akses |
|---|---|
| **User** | Dashboard, Dompet, Tabungan, Profil — kelola transaksi & keuangan pribadi |
| **Admin** | Semua akses User + Admin Panel untuk manajemen user & statistik global |
