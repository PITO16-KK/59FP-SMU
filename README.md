# 🌫️ Sistem Monitoring Udara

Sistem Monitoring Udara adalah aplikasi berbasis website yang digunakan untuk
menampilkan informasi kualitas udara (AQI) dan data lokasi.
Project ini dibuat sebagai sarana pembelajaran pada mata kuliah:

- Pemrograman Web  
- Pemrograman Berorientasi Objek (PBO)  
- Basis Data  
- Analisis dan Desain Berorientasi Objek (ADBO)

---

## 🛠️ Teknologi yang Digunakan
- PHP (Native, MVC sederhana)
- MySQL / MariaDB
- PHPMailer
- HTML, CSS, JavaScript
- Apache (XAMPP / Laragon)

---

## 📂 Struktur Folder
app/ -> Controller, Model, View
core/ -> Router, Database, BaseController
public/ -> Entry point aplikasi
PHPMailer/ -> Library email
start.php -> Bootstrap aplikasi

yaml
Salin kode

---

## 🚀 Cara Menjalankan Project Secara Lokal

---

### STEP 1 — Clone Repository

Clone repository ke folder web server sesuai environment yang digunakan.

**Laragon:**
laragon/www

makefile
Salin kode

**XAMPP:**
xampp/htdocs

bash
Salin kode

**Command:**
```bash
git clone https://github.com/USERNAME/NAMA_REPOSITORY.git
cd NAMA_REPOSITORY
STEP 2 — Konfigurasi BASE_URL
Edit file berikut:

arduino
Salin kode
app/Config/config.php
Set nilai BASE_URL sesuai lokasi project di komputer masing-masing
(bukan CLI / terminal):

php
Salin kode
define('BASE_URL', 'https://localhost/NAMA_REPOSITORY/public/');
⚠️ WAJIB:

Menggunakan folder public/ sebagai entry point

Menggunakan HTTPS (dibutuhkan untuk fitur tertentu)

STEP 3 — Buat File .env
Buat file .env di root project (sejajar dengan folder app, core, public).

Contoh struktur:

pgsql
Salin kode
NAMA_REPOSITORY/
├── app/
├── core/
├── public/
├── .env
├── start.php
└── README.md
Isi file .env sebagai berikut:

env
Salin kode
# DATABASE
DB_HOST=localhost
DB_NAME=nama_database
DB_USER=root
DB_PASS=

# APPLICATION
BASE_URL=https://localhost/NAMA_REPOSITORY/public

# MAIL (PHPMailer)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=emailkamu@gmail.com
MAIL_PASSWORD=password_aplikasi
MAIL_FROM_NAME=Sistem Monitoring Udara
🔒 Catatan:
File .env bersifat rahasia dan tidak di-upload ke GitHub (sudah diatur pada .gitignore).

STEP 4 — Import Database
Buat database sesuai dengan DB_NAME

Import file .sql (jika tersedia)

Pastikan koneksi database berhasil

STEP 5 — Jalankan Aplikasi
Buka browser dan akses:

bash
Salin kode
https://localhost/NAMA_REPOSITORY/public
Jika halaman utama tampil, maka aplikasi berhasil dijalankan 🎉

👩‍💻 Tim Pengembang
Kaisha — Backend Developer

Tassa — Frontend Developer

Miftah — Data Analyst
