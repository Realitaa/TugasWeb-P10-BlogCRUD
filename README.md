# Blog CRUD Laravel — Tugas Pertemuan 10: Resource Controller, Blade Components & Media Management

[![Pest Tests](https://github.com/Realitaa/TugasWeb-P10-BlogCRUD/actions/workflows/tests.yml/badge.svg)](https://github.com/Realitaa/TugasWeb-P10-BlogCRUD/actions/workflows/tests.yml)

Repositori ini berisi implementasi lengkap **Tugas Rutin 10 — Blog CRUD** pada mata kuliah Pemrograman Web. Aplikasi dibangun menggunakan framework **Laravel 13**, **PHP 8.5**, **Tailwind CSS v4** via **Vite 8**, paket manajer **pnpm**, pustaka antarmuka **BladewindUI**, penampil gambar **Viewer.js**, serta pengujian otomatis berstandar industri dengan **Pest PHP**. Seluruh kriteria wajib (**8/8 Requirements**) dan fitur bonus (**3/3 Bonus**) telah terpenuhi dan diverifikasi secara komprehensif.

- **Repository**: [https://github.com/Realitaa/TugasWeb-P10-BlogCRUD](https://github.com/Realitaa/TugasWeb-P10-BlogCRUD)

---

## 📌 Pemenuhan Kriteria Tugas (Tugas Rutin 10 — Blog CRUD Laravel)

Berikut matriks pemenuhan lengkap terhadap seluruh kriteria wajib (**8/8 Requirements**) dan seluruh fitur bonus (**3/3 Bonus**):

### 📊 Matriks Kesesuaian Kriteria Wajib (8/8)

| No | Kriteria Wajib | Status | Bukti & Lokasi Implementasi dalam Proyek |
|:--:|:---|:---:|:---|
| 1 | **`Route::resource('posts')` + named routes** | ✅ **Terpenuhi** | Didefinisikan pada [`routes/web.php`](routes/web.php) menggunakan `Route::resource('posts', PostController::class)->withTrashed(['show'])`. Menghasilkan 7 named routes standar (`posts.index`, `posts.create`, `posts.store`, `posts.show`, `posts.edit`, `posts.update`, `posts.destroy`). Teruji otomatis pada [`tests/Feature/RequirementTest.php`](tests/Feature/RequirementTest.php). |
| 2 | **`PostController` resource (7 methods)** | ✅ **Terpenuhi** | Berkas [`app/Http/Controllers/PostController.php`](app/Http/Controllers/PostController.php) mengimplementasikan 7 method resource secara lengkap: `index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, dan `destroy()`. Setiap method teruji memproses data dan merespons dengan HTTP status yang tepat di [`tests/Feature/RequirementTest.php`](tests/Feature/RequirementTest.php). |
| 3 | **Blade layout master + `@extends` / `@yield`** | ✅ **Terpenuhi** | Layout master berada di [`resources/views/layouts/app.blade.php`](resources/views/layouts/app.blade.php) yang memuat header, navigasi, avatar, tombol pencarian Command Palette, theme switcher, container responsif, dan slot utama `@yield('content')`. Diwarisi oleh seluruh halaman post melalui direktif `@extends('layouts.app')` dan `@section('content')`. |
| 4 | **Minimal 2 components (Alert, Card)** | ✅ **Terpenuhi** | Tersedia berbagai komponen terstandarisasi di [`resources/views/components/`](resources/views/components/) (BladewindUI). Komponen utama yang aktif digunakan meliputi `<x-bladewind.alert>` (flash notification sukses/error) dan `<x-bladewind.card>` (kontainer postingan & formulir), serta diperkaya dengan `<x-bladewind.avatar>`, `<x-bladewind.carousel>`, `<x-bladewind.modal>`, `<x-bladewind.dropmenu>`, `<x-bladewind.command-palette>`, dan `<x-bladewind.theme-switcher>`. |
| 5 | **Validasi + error per field + old input** | ✅ **Terpenuhi** | Validasi backend pada `PostController::store` & `update` membatasi `title` (wajib, maksimal 64 kata), `body` (wajib, maksimal 1000 kata), serta `media` (maksimal 3 file visual, maksimal 512KB/file). Error ditampilkan spesifik per field menggunakan `@error('title')` dan `@error('body')` di view formulir. Nilai input sebelumnya dipertahankan menggunakan helper `old('title')` dan `old('body')`. Teruji di [`tests/Feature/RequirementTest.php`](tests/Feature/RequirementTest.php). |
| 6 | **Flash message sukses/gagal** | ✅ **Terpenuhi** | Disimpan via session flash di `PostController` (`session()->flash('success', ...)` & `session()->flash('error', ...)`) saat aksi tambah, ubah, dan hapus berhasil dijalankan. Di-render secara dinamis di [`resources/views/layouts/app.blade.php`](resources/views/layouts/app.blade.php) menggunakan `<x-bladewind.alert>`. Teruji di [`tests/Feature/RequirementTest.php`](tests/Feature/RequirementTest.php). |
| 7 | **`@csrf` semua form + `@method` PUT/DELETE** | ✅ **Terpenuhi** | Seluruh formulir menyertakan token proteksi CSRF (`@csrf`). Formulir edit menggunakan `@method('PUT')` dan penghapusan data menggunakan formulir tersembunyi dengan `@method('DELETE')`. Teruji di [`tests/Feature/RequirementTest.php`](tests/Feature/RequirementTest.php) bahwa request POST langsung ke rute PUT/DELETE akan ditolak (405 Method Not Allowed), sementara spoofed method diterima. |
| 8 | **Route Model Binding + pagination** | ✅ **Terpenuhi** | Menggunakan Route Model Binding berbasis UUID pada model [`app/Models/Post.php`](app/Models/Post.php) ke rute `{post}` (`show`, `edit`, `update`, `destroy`). Postingan dipaginasi sebanyak 5 item per halaman (`paginate(5)`) dengan pagination links responsif Tailwind CSS dan pengujian parameter halaman (`?page=2`) di [`tests/Feature/RequirementTest.php`](tests/Feature/RequirementTest.php). |

---

### ⭐ Matriks Kesesuaian Kriteria Bonus (3/3)

| No | Fitur Bonus | Status | Bukti & Lokasi Implementasi dalam Proyek |
|:--:|:---|:---:|:---|
| 1 | **Pencarian (Search)** | ⭐ **Terpenuhi** | Menyediakan fitur pencarian cepat interaktif berbasis **BladewindUI Command Palette** (`Ctrl+K` atau klik ikon pencarian di navbar). Query dikirimkan ke endpoint asinkron `GET /posts/search?q=...` yang mencari kecocokan pada kolom `title` dan `body`, mengembalikan title lengkap, cuplikan body terpotong (`line-clamp-2`), dan URL show. Teruji di [`tests/Feature/RequirementTest.php`](tests/Feature/RequirementTest.php). |
| 2 | **Soft Delete** | ⭐ **Terpenuhi** | Model `Post` menggunakan trait `SoftDeletes` dengan kolom `deleted_at`. Postingan yang dihapus tetap tampil di feed utama (`GET /posts`), namun judul, badan konten, dan gambar disembunyikan serta diganti pesan miring redup *"Postingan ini telah dihapus."* tanpa menu aksi. Halaman detail tetap dapat diakses (`->withTrashed(['show'])`) dengan notifikasi serupa. Teruji di [`tests/Feature/RequirementTest.php`](tests/Feature/RequirementTest.php). |
| 3 | **Upload Gambar** | ⭐ **Terpenuhi** | Model `Post` memiliki relasi `hasMany` ke `Media`. Setiap postingan dapat mengunggah hingga 3 gambar visual dengan batas ukuran maksimal 512KB per berkas. Berkas disimpan di `storage/app/public/media` dengan nama acak berbasis UUID untuk mencegah tabrakan/penimpaan berkas. Ditampilkan dalam bentuk **Carousel Slider** interaktif dengan panah navigasi, swipe gesture, indikator, dan integrasi penampil **Viewer.js** (zoom, rotasi, fullscreen, thumbnail). Teruji di [`tests/Feature/RequirementTest.php`](tests/Feature/RequirementTest.php). |

---

## 🔍 Penjelasan Rinci Implementasi Fitur & Arsitektur

### 1. Resource Routing & Controller Standard
Rute didefinisikan secara deklaratif di [`routes/web.php`](routes/web.php):
```php
Route::redirect('/', '/posts');
Route::get('posts/search', [PostController::class, 'search'])->name('posts.search');
Route::resource('posts', PostController::class)->withTrashed(['show']);
```
Rute resource mencakup 7 endpoint standar:
1. `GET /posts` (`posts.index`) — Menampilkan daftar postingan terpaginasi.
2. `GET /posts/create` (`posts.create`) — Menampilkan formulir pembuatan postingan baru.
3. `POST /posts` (`posts.store`) — Memvalidasi dan menyimpan postingan serta berkas media.
4. `GET /posts/{post}` (`posts.show`) — Menampilkan detail postingan (termasuk postingan soft-deleted).
5. `GET /posts/{post}/edit` (`posts.edit`) — Menampilkan formulir ubah postingan.
6. `PUT /posts/{post}` (`posts.update`) — Memperbarui data postingan dan menambah media.
7. `DELETE /posts/{post}` (`posts.destroy`) — Melakukan soft delete terhadap postingan.

### 2. Blade Layout Master & Pewarisan View
Layout utama didefinisikan pada [`resources/views/layouts/app.blade.php`](resources/views/layouts/app.blade.php):
- **Navbar Header**: Sticky bar blur berisi logo BlogPost, tombol pemicu pencarian Command Palette (`openCommandPalette('post-search-palette')`), tombol *Theme Switcher* (Terang, Gelap, Sistem) dengan penyimpanan status di `localStorage`, serta avatar profil GitHub Realitaa.
- **Wadah Konten Responsif**: Lebar penuh pada layar seluler (`< md`) dan dibatasi maksimal `640px` (`max-w-160`) pada layar desktop ke atas.
- **Flash Alert**: Membaca sesi `session('success')` atau `session('error')` dan merendernya dalam komponen `<x-bladewind.alert>`.
- **Command Palette**: Dialog modal pencarian global terintegrasi keyboard shortcut `Ctrl+K` / `Cmd+K`.
- **Inheritansi Halaman**:
  - [`resources/views/posts/index.blade.php`](resources/views/posts/index.blade.php) merender feed timeline media sosial.
  - [`resources/views/posts/create.blade.php`](resources/views/posts/create.blade.php) merender form postingan baru dengan upload multi-file.
  - [`resources/views/posts/edit.blade.php`](resources/views/posts/edit.blade.php) merender form edit postingan beserta manajemen thumbnail gambar yang sudah ada.
  - [`resources/views/posts/show.blade.php`](resources/views/posts/show.blade.php) merender tampilan mendalam postingan tunggal.

### 3. Komponen BladewindUI & Desain Modern
Antarmuka dibangun dengan memanfaatkan komponen BladewindUI:
- `<x-bladewind.card>`: Kontainer berdesain bersih dengan sudut membulat dan bayangan halus untuk setiap kartu postingan.
- `<x-bladewind.alert>`: Kotak peringatan feedback status aksi.
- `<x-bladewind.modal>`: Modal dialog konfirmasi penghapusan data sebelum submit form DELETE dieksekusi.
- `<x-bladewind.dropmenu>`: Menu popup opsi tiga titik (`...`) untuk akses cepat edit dan hapus.
- `<x-bladewind.carousel>`: Galeri multi-gambar dengan transisi halus, navigasi panah, dan indikator titik.
- `<x-bladewind.theme-switcher>`: Pengganti tema gelap/terang otomatis yang sinkron dengan class `.dark` pada tag `<html>`.

### 4. Validasi Komprehensif & Old Input
Aturan validasi didefinisikan secara ketat pada [`app/Http/Controllers/PostController.php`](app/Http/Controllers/PostController.php):
- `title`: Wajib diisi, string, maksimal 64 kata (`Str::of($value)->wordCount() > 64`).
- `body`: Wajib diisi, string, maksimal 1000 kata (`Str::of($value)->wordCount() > 1000`).
- `media`: Maksimal 3 berkas visual (`max:3`).
- `media.*`: Berkas gambar (`image`), ukuran maksimal 512KB (`max:512`).

Ketika validasi gagal, Laravel otomatis mengalihkan pengguna kembali ke formulir sebelumnya (`back()`), menyertakan pesan error per field (`$errors->get('title')`, `$errors->get('body')`), dan mengembalikan nilai isian formulir sebelumnya melalui fungsi `old()`.

### 5. Keamanan: Proteksi CSRF & Method Spoofing
- Setiap form mutasi data menyertakan direktif `@csrf` untuk memvalidasi token sesi pengguna terhadap serangan Cross-Site Request Forgery.
- Operasi `PUT` (edit) dan `DELETE` (hapus) dijalankan melalui HTML Method Spoofing (`@method('PUT')` dan `@method('DELETE')`) yang menyisipkan input tersembunyi `_method`.
- Request POST murni ke rute update atau delete tanpa spoofing method ditolak oleh router Laravel dengan status `405 Method Not Allowed`.

### 6. Route Model Binding Berbasis UUID & Paginasi
- Model `Post` menggunakan trait `Illuminate\Database\Eloquent\Concerns\HasUuids` dengan tipe kolom `uuid` sebagai primary key.
- Router menginjeksi instance model secara otomatis saat parameter URL `{post}` berupa UUID yang valid. Jika UUID tidak ditemukan di database, aplikasi secara otomatis merespons dengan HTTP status `404 Not Found`.
- Data postingan pada feed utama dipecah per halaman dengan `Post::latest()->paginate(5)->withQueryString()`, meminimalkan konsumsi memori browser saat dataset bertambah besar.

### 7. Fitur Bonus: Pencarian Cepat (Command Palette)
Pencarian postingan dirancang menggunakan model *Command Palette* modern (`Ctrl+K`):
- **Backend API**: Method `search(Request $request): JsonResponse` di `PostController` mencari teks pada kolom `title` dan `body`. Hanya mengambil kolom yang dibutuhkan (`id`, `title`, `body`) dan membatasi cuplikan body (`Str::limit(..., 140)`) untuk menghemat bandwidth.
- **Frontend Debouncing**: Pengetikan di-debounce selama 250ms dengan pembatalan request (`AbortController`) saat query berubah.
- **UI Hasil Pencarian**: Hasil ditampilkan dengan judul lengkap dan isi yang terpotong rapi (`line-clamp-2`). Mendukung navigasi keyboard (`ArrowUp`, `ArrowDown`, `Enter`) dan auto-redirect ke halaman detail postingan.

### 8. Fitur Bonus: Soft Delete dengan Tampilan Timeline
- Fitur *soft delete* diaktifkan menggunakan trait `SoftDeletes` dan kolom `deleted_at`.
- Pada feed utama ([`resources/views/posts/index.blade.php`](resources/views/posts/index.blade.php)), postingan yang berstatus `trashed()` tetap dipertahankan urutan kronologinya di timeline untuk menjaga konteks percakapan. Judul, badan konten, dan gambar diganti dengan teks miring redup *"Postingan ini telah dihapus."* dan opsi menu titik tiga (`...`) dihilangkan.
- Rute `posts.show` diizinkan membaca postingan terhapus melalui `withTrashed(['show'])`.

### 9. Fitur Bonus: Upload Gambar, Carousel, & Viewer.js
- Unggahan media dikelola dalam tabel terpisah `media` yang terhubung secara relasional ke tabel `posts`.
- Berkas diunggah ke direktori `storage/app/public/media` dengan nama file unik berbasis UUID (`Str::uuid()->toString() . '.' . $extension`) untuk mencegah penimpaan file saat nama berkas sama diunggah ulang. Nama asli disimpan pada kolom `file_name`.
- Pada tampilan Blade, jika postingan memiliki gambar, sistem merender `<x-bladewind.carousel>` dengan swipe gesture handling.
- Mengklik gambar akan membuka modal preview **Viewer.js** dengan mode modal lengkap (backdrop blur, tombol navigasi, rotasi gesture/touch, zoom on wheel/pinch, thumbnail strip navbar, dan judul gambar).

---

## 📁 Struktur Direktori Proyek

```plaintext
blogcrud/
├── app/
│   ├── Http/Controllers/
│   │   ├── Controller.php
│   │   └── PostController.php           # 7 Resource methods + search API
│   └── Models/
│       ├── Media.php                    # Relasi Media (UUID, belongsTo Post)
│       ├── Post.php                     # Model Post (UUID, HasMany Media, SoftDeletes)
│       └── User.php
├── database/
│   ├── factories/
│   │   ├── MediaFactory.php             # Media dummy generator (Picsum Photos)
│   │   └── PostFactory.php              # Post dummy generator (Bahasa Indonesia)
│   ├── migrations/
│   │   ├── ..._create_posts_table.php   # Skema posts (UUID PK, title, body, softDeletes)
│   │   └── ..._create_media_table.php   # Skema media (UUID PK, post_id FK cascade, path)
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── PostSeeder.php               # Seeder 20 postingan (0-3 media per post)
├── resources/
│   ├── css/
│   │   └── app.css                      # Tailwind CSS v4 + BladewindUI Command Palette styles
│   ├── js/
│   │   └── app.js                       # Viewer.js config, Command Palette handler, pointer tracker
│   └── views/
│       ├── components/bladewind/        # Komponen UI Bladewind (Card, Alert, Carousel, Modal, dll)
│       ├── layouts/
│       │   └── app.blade.php            # Master layout responsif + Command Palette & Theme Switcher
│       └── posts/
│           ├── create.blade.php         # Form tambah postingan & upload file
│           ├── edit.blade.php           # Form edit postingan & kelola media
│           ├── index.blade.php          # Timeline feed postingan (dengan soft-deleted notice)
│           └── show.blade.php           # Detail postingan lengkap
├── routes/
│   └── web.php                          # Route resource 'posts' + route 'posts.search'
├── tests/
│   └── Feature/
│       ├── PostTest.php                 # Unit & feature tests fungsionalitas post
│       └── RequirementTest.php          # Uji kesesuaian 8 requirements wajib + 3 fitur bonus
├── .github/
│   └── workflows/
│       └── tests.yml                    # GitHub Actions CI automated testing
├── composer.json
├── package.json                         # Dependensi frontend (pnpm & Vite)
├── pnpm-lock.yaml
└── vite.config.js
```

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek di lingkungan pengembangan lokal:

### 1. Kebutuhan Sistem
- **PHP**: Versi 8.2 atau lebih baru (direkomendasikan PHP 8.4+ / PHP 8.5)
- **Composer**: Versi 2.8+
- **Node.js**: Versi 20+
- **pnpm**: Versi 9+
- Ekstensi PHP: `pdo_sqlite` (atau `pdo_mysql`), `mbstring`, `fileinfo`, `dom`

### 2. Kloning Repositori
```bash
git clone https://github.com/Realitaa/TugasWeb-P10-BlogCRUD.git
cd TugasWeb-P10-BlogCRUD
```

### 3. Instalasi Dependensi Backend (Composer)
```bash
composer install
```

### 4. Instalasi Dependensi Frontend (pnpm)
```bash
pnpm install
```

### 5. Konfigurasi Environment (`.env`)
Salin berkas konfigurasi `.env.example`:
```bash
cp .env.example .env
```

Buat application key:
```bash
php artisan key:generate
```

Secara default, aplikasi telah dikonfigurasi menggunakan database SQLite (`database/database.sqlite`). Jika berkas belum ada, buat berkas tersebut:
```bash
touch database/database.sqlite
```

### 6. Menjalankan Migrasi & Database Seeder
Jalankan migrasi skema tabel beserta data contoh postingan bahasa Indonesia dan gambar acak unik:
```bash
php artisan migrate --seed
```

Buat symbolic link untuk direktori penyimpanan publik:
```bash
php artisan storage:link
```

### 7. Kompilasi Aset Frontend (Vite)
Build aset untuk mode produksi:
```bash
pnpm run build
```
Atau jalankan server pengembang Vite:
```bash
pnpm run dev
```

### 8. Menjalankan Server Lokal
Jalankan server aplikasi Laravel:
```bash
php artisan serve
```
Buka peramban pada alamat [http://localhost:8000](http://localhost:8000).

---

## 🧪 Pengujian Otomatis (Automated Testing with Pest)

Proyek ini dilengkapi dengan rangkaian pengujian fitur otomatis menyeluruh berbasis **Pest PHP** yang memverifikasi setiap aspek kriteria penugasan:

Jalankan seluruh suite pengujian:
```bash
php artisan test --compact
```
Atau jalankan pengujian khusus pemenuhan kriteria penugasan:
```bash
php artisan test --filter=RequirementTest --compact
```

### Hasil Pengujian Otomatis:
```plaintext
   PASS  Tests\Feature\RequirementTest
  ✓ requirement 1 & 2: PostController implements all 7 resource methods and named routes are registered
  ✓ requirement 1 & 2: all 7 resource routes invoke corresponding methods and return expected responses
  ✓ requirement 5: validation fails when required fields are missing and returns error per field
  ✓ requirement 5: validation enforces maximum word constraints on title and body
  ✓ requirement 5: old input is preserved on validation failure
  ✓ requirement 6: store, update, and delete actions trigger success flash messages
  ✓ requirement 7: update and destroy endpoints reject plain POST requests without spoofed method
  ✓ requirement 7: method spoofing via POST with _method PUT and DELETE is supported
  ✓ requirement 8: Route Model Binding binds model or throws 404 for invalid identifier
  ✓ requirement 8: posts list supports pagination via query parameter
  ✓ bonus 1: search endpoint filters posts by title and body and returns truncated body with show url
  ✓ bonus 2: soft deleted posts have deleted_at timestamp and are displayed with deletion notice
  ✓ bonus 3: post can store up to 3 visual media images and persists files to public storage
  ✓ bonus 3: upload validation rejects non-visual files, more than 3 files, or files > 512KB

  Tests:    31 passed (164 assertions)
  Duration: 1.05s
```
