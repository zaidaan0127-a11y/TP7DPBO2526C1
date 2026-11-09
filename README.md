# TP7DPBO2526C1
Janji

Saya Zaidaan Dhyaa Ulhaq Budiono dengan NIM 2500945 mengerjakan Tugas Praktikum 7 dalam mata kuliah Desain dan Pemrograman Berorientasi Objek untuk keberkahanNya maka saya tidak melakukan kecurangan seperti yang telah dispesifikasikan. Aamiin.

# 📚 Sistem Perpustakaan (PHP Native OOP)

Aplikasi web sederhana untuk mengelola data perpustakaan, dibuat dengan **PHP Native**, **MySQL/MariaDB**, dan konsep **OOP (Object-Oriented Programming)**. Seluruh query database memakai **Prepared Statement**.

---

## 1. Tema Website

**Tema: Manajemen Data Perpustakaan.**

Website ini dipakai petugas perpustakaan untuk mencatat dan mengelola:

- **Buku**: judul, tahun terbit, stok, penulis, dan kategorinya.
- **Penulis**: data pengarang buku beserta negara asalnya.
- **Kategori**: pengelompokan buku, misalnya Novel atau Pendidikan.

Setiap entitas memiliki fitur **CRUD** lengkap (Create, Read, Update, Delete).

---

## 2. Struktur Folder

<img width="182" height="235" alt="Screenshot 2026-11-09 122707" src="https://github.com/user-attachments/assets/9c1b3022-3c6f-48b2-bbbf-26d0e7d4a761" />


| Folder / File | Fungsi |
|---|---|
| `class/` | Berisi satu class untuk setiap tabel, agar kode rapi dan tanggung jawabnya jelas |
| `config/db.php` | Menyimpan konfigurasi dan koneksi database |
| `database/` | Tempat file SQL database |
| `view/` | Elemen HTML yang dipisah per halaman agar tidak berulang |
| `index.php` | Pintu masuk aplikasi: memuat file, memproses request, memanggil view |
| `style.css` | Tampilan halaman |

---

## 3. Penjelasan Database

**Nama database:** `db_perpustakaan`
Terdiri dari **3 tabel**, dan tabel `books` memiliki **2 foreign key**.

### 3.1 Tabel `authors` (penulis)

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT, AUTO_INCREMENT | Primary key |
| `name` | VARCHAR(100) | Nama penulis |
| `country` | VARCHAR(100) | Negara asal penulis |

### 3.2 Tabel `categories` (kategori)

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT, AUTO_INCREMENT | Primary key |
| `name` | VARCHAR(100) | Nama kategori |

### 3.3 Tabel `books` (buku)

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT, AUTO_INCREMENT | Primary key |
| `title` | VARCHAR(200) | Judul buku |
| `year` | INT | Tahun terbit |
| `stock` | INT | Jumlah stok (default 0) |
| `author_id` | INT | **Foreign key** → `authors.id` |
| `category_id` | INT | **Foreign key** → `categories.id` |

### 3.4 Relasi Antar Tabel

```
authors (1) ────────< books >──────── (1) categories
   id  ◄── author_id        category_id ──► id
```

- Satu penulis dapat memiliki **banyak buku** (one-to-many).
- Satu kategori dapat memiliki **banyak buku** (one-to-many).
- Foreign key memakai `ON DELETE RESTRICT`: penulis atau kategori **tidak bisa dihapus** selama masih dipakai oleh buku. Ini menjaga konsistensi data, dan aplikasi menampilkan pesan yang jelas saat hal itu terjadi.
- Foreign key memakai `ON UPDATE CASCADE`: jika `id` induk berubah, `id` di tabel `books` ikut berubah.

---

## 4. Penerapan OOP

### 4.1 Class `Database` (`config/db.php`)
- Mengelola koneksi database memakai **PDO**.
- Memakai pola **Singleton**: constructor `private`, objek hanya dibuat sekali lewat `Database::getInstance()`. Hasilnya, seluruh aplikasi memakai satu koneksi yang sama.

### 4.2 Class `Model` (`class/Model.php`)
- **Abstract class** yang menjadi induk semua model.
- Berisi method CRUD generik: `getAll()`, `find()`, `create()`, `update()`, `delete()`.
- Class turunan hanya perlu mendefinisikan `$table` (nama tabel) dan `$fillable` (kolom yang boleh diisi).

### 4.3 Class `Author`, `Category`, `Book`
- Mewarisi (**inheritance**) class `Model`, sehingga langsung memiliki seluruh fitur CRUD.
- `Book` melakukan **override** pada `getAll()` agar memakai `JOIN` ke tabel `authors` dan `categories`. Dengan begitu daftar buku menampilkan nama penulis dan kategori, bukan angka id.

### 4.4 Konsep OOP yang dipakai

| Konsep | Penerapan |
|---|---|
| Class & Object | `Database`, `Model`, `Author`, `Category`, `Book` |
| Inheritance | `Author`, `Category`, `Book` mewarisi `Model` |
| Abstraction | `Model` bersifat `abstract` |
| Encapsulation | Properti dan method memakai `private` / `protected` |
| Polymorphism (override) | `Book::getAll()` menimpa `Model::getAll()` |
| Singleton | `Database::getInstance()` |

---

## 5. Penjelasan Flow Program\

### 5.1 Penjelasan Langkah di `index.php`

1. **Memuat file.** `config/db.php` dan seluruh file di `class/` dimuat dengan `require_once`. `Model.php` dimuat lebih dulu karena diwarisi class lain.
2. **Peta halaman (`$pages`).** Setiap halaman terhubung ke satu objek model dan daftar field form-nya.
3. **Pemilihan halaman.** Nilai `?page=` dicek terhadap daftar `$pages` (whitelist). Jika tidak terdaftar, otomatis ke halaman `books`.
4. **Proses POST.** Form mengirim field tersembunyi `op` yang berisi `create`, `update`, atau `delete`.
   - `create` / `update`: input diambil, di-`trim`, divalidasi tidak kosong, lalu field angka diubah ke integer. Setelah itu `$model->create()` atau `$model->update()` dipanggil.
   - `delete`: memanggil `$model->delete($id)`.
   - Hasilnya disimpan di `$_SESSION['flash']` sebagai pesan notifikasi.
   - Error foreign key (kode `23000`) ditangkap dengan `try-catch` dan diubah menjadi pesan yang mudah dibaca.
   - Terakhir halaman di-**redirect** ke URL GET (pola *Post-Redirect-Get*), sehingga refresh browser tidak mengirim ulang form.
5. **Proses GET (Read).**
   - Jika ada `?edit=ID`, data dicari dengan `$model->find()` dan hasilnya mengisi form.
   - Seluruh data tabel diambil dengan `$model->getAll()`.
   - Khusus halaman buku, data `authors` dan `categories` juga diambil untuk mengisi dropdown.
6. **Menampilkan view.** `header.php`, view halaman, dan `footer.php` dimuat berurutan. Variabel dari `index.php` (`$rows`, `$editItem`, `$authors`, dst.) otomatis dapat dipakai di view.

### 5.2 Contoh Flow: Menambah Buku

1. Pengguna mengisi form di `view/books.php` lalu klik **Simpan** (POST, `op=create`).
2. `index.php` menerima data dan memvalidasinya.
3. `index.php` memanggil `$model->create($data)` pada objek `Book`.
4. `Model::create()` menyusun query `INSERT INTO books (...) VALUES (:title, :year, ...)`, lalu `prepare()` dan `execute()` mengisi nilainya.
5. Pesan "Data berhasil ditambahkan." disimpan di session.
6. Halaman di-redirect ke `index.php?page=books`.
7. `Book::getAll()` berjalan (dengan JOIN) dan tabel ditampilkan oleh `view/books.php`, beserta pesan notifikasi.

### 5.3 Contoh Flow: Mengubah Buku

1. Klik **Edit** → URL `index.php?page=books&edit=3`.
2. `index.php` memanggil `$model->find(3)`; hasilnya masuk ke `$editItem`.
3. Form terisi data lama (dropdown otomatis memilih penulis dan kategori yang sesuai), dan field tersembunyi `op` bernilai `update`.
4. Saat disimpan, `index.php` memanggil `$model->update($id, $data)` → query `UPDATE ... WHERE id = :id`.

### 5.4 Contoh Flow: Menghapus Penulis yang Masih Dipakai

1. Klik **Hapus** pada penulis (POST, `op=delete`).
2. `Model::delete()` menjalankan `DELETE`, tetapi database menolaknya karena foreign key `RESTRICT`.
3. `PDOException` dengan kode `23000` ditangkap di `index.php`.
4. Pengguna melihat pesan: *"Gagal: data masih dipakai/terhubung dengan data lain."*

### 6 Dokumentasi

### 6.1 Dokumentasi CRUD tabel Buku


https://github.com/user-attachments/assets/18227253-87db-4572-be8b-a5768f6f694e


### 6.2 Dokumentasi CRUD tabel Penulis 


https://github.com/user-attachments/assets/ec1afe18-b2ad-4757-8b79-66cac29602f4


### 6.3 Dokumentasi CRUD tabel Kategori


https://github.com/user-attachments/assets/f3c608a6-3e96-4767-aebf-7cbbc2016580





