# SaveTube - Modern Multi-Platform Video Downloader

![SaveTube Banner](https://img.shields.io/badge/SaveTube-Catppuccin%20Theme-ffb86c?style=for-the-badge&logo=laravel)
![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-77C1D2?style=for-the-badge&logo=alpine.js&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

SaveTube adalah aplikasi web pengunduh video yang elegan dan modern. Dibangun dengan framework **Laravel**, reaktivitas menggunakan **Alpine.js**, dan antarmuka **Tailwind CSS** yang di-styling menggunakan skema warna **Catppuccin Mocha**. Aplikasi ini menggunakan mesin **yt-dlp** untuk mengunduh video dari berbagai platform seperti YouTube, TikTok, Facebook, dan Instagram.

## ✨ Fitur Utama

- 🎥 **Multi-Platform Support:** Auto-detect link dari YouTube, TikTok, Facebook, Instagram.
- ⚡ **Proxy Stream:** Meneruskan file media melalui server untuk mengatasi masalah *CORS* atau *Expired Link*.
- 🎨 **Catppuccin Theme:** Antarmuka yang mulus, solid, dan indah menggunakan tema Catppuccin.
- 🕒 **History Management:** (Untuk user login) Riwayat unduhan, pencarian, dan unduh ulang.
- 🛡️ **Admin Dashboard:** Pantau statistik pengguna, kelola role user, dan mode maintenance.
- 🗄️ **MySQL Database:** Setup standar menggunakan MySQL yang tangguh dan scalable.

---

## 🚀 Panduan Instalasi (Quick Setup)

Berikut adalah langkah-langkah untuk menyiapkan aplikasi di komputer Anda menggunakan database **MySQL**.

### Persyaratan Sistem
Pastikan perangkat Anda telah menginstal:
- **PHP** (>= 8.2)
- **Composer**
- **Node.js** & **NPM** (Untuk compile asset frontend)
- **MySQL** / XAMPP / Laragon (Untuk server database)
- **FFmpeg** *(Opsional, disarankan jika ingin menggabungkan audio+video resolusi tinggi)*

### Langkah-langkah Instalasi

**1. Clone Repository**
```bash
git clone https://github.com/username/savetube.git
cd savetube
```

**2. Instal Dependensi Backend & Frontend**
```bash
composer install
npm install
npm run build
```

**3. Buat Database MySQL**
Buka phpMyAdmin atau alat pengelolaan database Anda lainnya, lalu buat sebuah database baru dengan nama `savetube`.

**4. Konfigurasi Environment (.env)**
```bash
# Windows
copy .env.example .env

# Linux/Mac
cp .env.example .env
```
Buka file `.env` dan pastikan konfigurasi koneksi database Anda sudah sesuai dengan setup MySQL Anda, seperti ini:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=savetube
DB_USERNAME=root
DB_PASSWORD=
```

**5. Generate Application Key**
```bash
php artisan key:generate
```

**6. Migrate & Seed Database**
Perintah ini akan secara otomatis membuat tabel-tabel di database `savetube` yang telah Anda buat, dan menambahkan data user awal (Test User).
```bash
php artisan migrate --seed
```

**7. Download Engine yt-dlp**
SaveTube memerlukan binary `yt-dlp` untuk bekerja. Jalankan command kustom ini agar Laravel secara otomatis mendownload versi `yt-dlp` terbaru yang sesuai dengan OS Anda (Windows/Linux) ke dalam folder aplikasi:
```bash
php artisan yt-dlp:update
```

**8. Jalankan Aplikasi**
```bash
php artisan serve
```
Aplikasi kini dapat diakses melalui browser pada `http://localhost:8000`.

---

## 💻 Akun Default (Seeder)

Jika Anda menjalankan perintah `--seed` pada langkah instalasi di atas, Anda dapat login menggunakan akun percobaan:
- **Email:** `test@example.com`
- **Password:** `password` (Password default factory Laravel)

Untuk menjadikan akun tersebut sebagai Admin, Anda dapat menggunakan tinker:
```bash
php artisan tinker
> App\Models\User::first()->update(['role' => 'admin']);
> exit
```

---

## 🛠️ Tech Stack & Dependencies
- **Backend:** Laravel 11.x, yt-dlp (Core Engine)
- **Frontend:** Blade Templates, Alpine.js, Tailwind CSS
- **Database:** MySQL

## 📄 Lisensi
SaveTube bersifat open-source dengan lisensi yang dapat disesuaikan oleh pengembang. Engine `yt-dlp` memiliki lisensinya sendiri.
