# Portfolio & Resume Builder 🚀

Halo! Ini adalah web aplikasi portofolio pribadi dan manajemen resume yang dibuat menggunakan framework **Laravel**. 

Aplikasi ini dibuat untuk menampilkan profil profesional, pengalaman kerja, riwayat pendidikan, skill, sertifikat, serta berbagai showcase project yang pernah dikerjakan — lengkap dengan dashboard admin untuk mengelola semua datanya secara langsung dan fitur cetak/ekspor portofolio ke format PDF.

---

## ✨ Fitur Utama

- **Dashboard Admin:** Panel kontrol untuk update data diri, foto profil, bio, dan link sosial media (LinkedIn, Instagram, kontak).
- **Manajemen Riwayat & Portofolio (CRUD):**
  - **Pendidikan & Pengalaman:** Catat riwayat studi dan pengalaman kerja lengkap dengan lampiran foto/dokumentasi.
  - **Katalog Project:** Tampilkan project yang pernah dibuat beserta deskripsi, tautan demo/repo, thumbnail, dan galeri foto/video pendukung.
  - **Sertifikasi & Penghargaan:** Tampilkan lisensi, sertifikat keahlian, dan link verifikasi kredensial.
  - **Skill & Keahlian:** Kategorisasi skill teknis, soft skill, dan bahasa.
- **Urutan Fleksibel (Drag/Sort & Bulk Delete):** Atur urutan tampilan setiap section sesuai kebutuhan dan hapus data sekaligus.
- **Halaman Preview Portofolio:** Tampilan publik/preview yang rapi, modern, dan responsif di berbagai ukuran layar.
- **Ekspor ke PDF:** Cetak atau simpan rangkuman portofolio/CV dalam format PDF siap pakai.

---

## 🛠️ Teknologi yang Digunakan

- **Backend:** PHP & Laravel
- **Frontend:** Blade Templates, HTML5, CSS3 kustom, JavaScript
- **Database:** MySQL / SQLite
- **Dependency & Build Tools:** Composer, NPM, Vite

---

## 💻 Cara Menjalankan di Komputer Lokal

Kalau kamu mau coba jalankan project ini di lokal, ikuti langkah-langkah simpel berikut:

### 1. Clone repository
```bash
git clone https://github.com/abidsp15/portfolio.git
cd portfolio
```

### 2. Install dependensi (Composer & NPM)
```bash
composer install
npm install
```

### 3. Setup file konfigurasi `.env`
Salin file contoh `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan koneksi database kamu (misal: MySQL atau SQLite). Setelah itu, generate application key:
```bash
php artisan key:generate
```

### 4. Link Storage & Migrasi Database
Jalankan migrasi tabel database dan buat symlink storage untuk upload file:
```bash
php artisan storage:link
php artisan migrate --seed
```
*(Seeder sudah menyediakan data awal profil, pengalaman, dan project dummy)*

### 5. Jalankan server lokal
```bash
npm run build
php artisan serve
```

Buka browser dan akses aplikasi di: **`http://127.0.0.1:8000`**

---

## 👤 Akun Bawaan (Default Seeder)

Setelah menjalankan `php artisan migrate --seed`, kamu bisa langsung login ke dashboard admin dengan:
- **Email:** `admin@portfolio.com`
- **Password:** `password`

---

## 📬 Kontak & Profil

Dibuat oleh **Abidsyach Pramana**
- **LinkedIn:** [Abidsyach Pramana](http://www.linkedin.com/in/abidsyach-pramana)
- **Instagram:** [@abidsyachp](https://instagram.com/abidsyachp)
- **Email:** Abidsyach1501@gmail.com
