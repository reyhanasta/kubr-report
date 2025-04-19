# Proyek Ekspor Laporan Pasien ke Excel (Laravel)

## Deskripsi

Aplikasi web sederhana yang dibangun dengan Laravel untuk memungkinkan staf administrasi (admin) mengekspor data kunjungan pasien rawat jalan (Rajal) dalam rentang tanggal tertentu ke dalam format file Excel (.xlsx). Aplikasi ini memiliki antarmuka pengguna yang dibangun menggunakan Tailwind CSS dan menghasilkan file Excel dengan format header khusus yang mencakup penggabungan sel (merged cells).

**Penting:** Aplikasi ini dirancang khusus untuk terintegrasi dengan struktur database dari Sistem Informasi Manajemen Rumah Sakit (SIMRS) Khanza.

## Fitur Utama

-   Antarmuka web untuk memilih rentang tanggal (Tanggal Mulai & Tanggal Selesai).
-   Ekspor data pasien (berdasarkan model `Register` dari SIMRS Khanza) ke format Excel (.xlsx).
-   Format header Excel kustom dengan 2 baris dan penggabungan sel (merged cells) untuk kategori seperti Gender, Cara Bayar, Kunjungan, dan Asal Pasien.
-   Validasi input tanggal di backend.
-   Antarmuka pengguna (UI) yang responsif menggunakan Tailwind CSS.
-   (Opsional) Auto-refresh halaman setiap 5 menit.

## Teknologi yang Digunakan

-   **Framework Backend:** Laravel (Misal: 10.x atau 11.x)
-   **Bahasa Pemrograman:** PHP (Misal: 8.1+)
-   **Database:** MySQL / PostgreSQL (atau database lain yang didukung Laravel) - **Dirancang untuk skema database SIMRS Khanza**
-   **Frontend Styling:** Tailwind CSS
-   **Paket Ekspor Excel:** Maatwebsite/Laravel-Excel
-   **Manajemen Dependensi PHP:** Composer
-   **Manajemen Dependensi Frontend:** Node.js & NPM

## Setup / Instalasi Lokal

Berikut langkah-langkah untuk menjalankan proyek ini di lingkungan lokal Anda:

1.  **Clone Repository:**

    ```bash
    git clone <url-repository-anda> nama-folder-proyek
    cd nama-folder-proyek
    ```

2.  **Instal Dependensi PHP:**

    ```bash
    composer install
    ```

3.  **Instal Dependensi Node.js:**

    ```bash
    npm install
    ```

4.  **Salin File Environment:**

    ```bash
    cp .env.example .env
    ```

5.  **Generate Kunci Aplikasi:**

    ```bash
    php artisan key:generate
    ```

6.  **Konfigurasi File `.env`:**

    -   Buka file `.env` dengan editor teks.
    -   Atur koneksi database Anda ( `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`). Pastikan database sudah dibuat.
    -   **Penting:** Aplikasi ini memerlukan struktur database yang kompatibel dengan SIMRS Khanza. Database dummy untuk pengujian/pengembangan mungkin tersedia di repository GitHub proyek ini (jika Anda menyertakannya).
    -   Atur `APP_URL` jika perlu (misal: `APP_URL=http://localhost:8000`).

7.  **Jalankan Migrasi Database:**

    -   Karena aplikasi ini terhubung ke database SIMRS Khanza yang sudah ada, Anda **tidak perlu** menjalankan `php artisan migrate` kecuali ada tabel tambahan khusus untuk aplikasi ini yang tidak ada di Khanza. Jika hanya membaca data Khanza, lewati langkah ini.
    -   (Opsional) Jika Anda memiliki seeder khusus untuk aplikasi ini: `php artisan db:seed`

8.  **Kompilasi Aset Frontend:**

    -   Untuk pengembangan (dengan hot-reloading jika menggunakan Vite):
        ```bash
        npm run dev
        ```
    -   Untuk build produksi (minified):
        ```bash
        npm run build
        ```

9.  **Buat Symbolic Link Storage:** (Hanya jika aplikasi Anda menyimpan file publik sendiri, di luar data Khanza)

    ```bash
    php artisan storage:link
    ```

10. **Jalankan Server Pengembangan:**
    ```bash
    php artisan serve
    ```
    Aplikasi sekarang seharusnya bisa diakses di `http://localhost:8000`.

## Penggunaan

1.  Buka aplikasi di browser Anda (misalnya `http://localhost:8000` atau domain production Anda).
2.  Pastikan aplikasi terhubung ke database SIMRS Khanza yang benar.
3.  Pilih rentang tanggal yang diinginkan pada form "Tanggal Mulai" dan "Tanggal Selesai".
4.  Klik tombol "Download Excel".
5.  File Excel (`.xlsx`) dengan data pasien sesuai rentang tanggal dan format header khusus akan terunduh.

## Detail Ekspor Excel

Logika utama untuk menghasilkan file Excel berada di class `App\Exports\ReportExport`. Class ini menggunakan pendekatan manual dengan event `AfterSheet` dari Maatwebsite/Laravel-Excel untuk:

-   Membuat struktur header 2 baris.
-   Melakukan merge cell pada header.
-   Menerapkan styling pada header.
-   Mengambil data dari database (tabel-tabel SIMRS Khanza).
-   Melakukan mapping data secara manual.
-   Menulis data ke sheet mulai dari baris ke-3.

## Catatan Deployment ke Produksi

Saat mendeploy ke server produksi:

-   Pastikan `APP_ENV=production` dan `APP_DEBUG=false` di file `.env` server.
-   Pastikan koneksi database di `.env` mengarah ke database SIMRS Khanza produksi yang benar.
-   Jalankan `composer install --optimize-autoloader --no-dev`.
-   Jalankan `npm install --production` dan `npm run build`.
-   Jalankan perintah optimasi Laravel:
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    # php artisan event:cache (jika pakai event discovery)
    ```
-   Pastikan konfigurasi web server (Nginx/Apache) mengarah ke direktori `public`.
-   Atur hak akses (permissions) yang benar untuk direktori `storage` dan `bootstrap/cache` jika aplikasi menulis log atau cache.
-   Gunakan HTTPS untuk keamanan.
-   Lihat dokumentasi resmi Laravel untuk panduan deployment yang lebih lengkap.

## Lisensi

This project is licensed under the [MIT license](https://opensource.org/licenses/MIT). See the LICENSE file for more information.
