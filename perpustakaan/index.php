<?php
session_start();    // aktifkan session, dipakai untuk pesan notifikasi (flash)

// Muat semua file yang dibutuhkan. __DIR__ = folder tempat file ini berada.
// Urutan penting: Model harus dimuat sebelum Author/Category/Book yang mewarisinya.
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/class/Model.php';
require_once __DIR__ . '/class/Author.php';
require_once __DIR__ . '/class/Category.php';
require_once __DIR__ . '/class/Book.php';

// Fungsi bantu: mengamankan teks sebelum ditampilkan (cegah XSS)
function e($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

// Peta halaman: tiap halaman punya objek model dan daftar field form
$pages = [
    'books' => [
        'model'  => new Book(),   // objek Book (konstruktor Model langsung koneksi ke DB)
        'fields' => ['title' => 'text', 'year' => 'number', 'stock' => 'number',
                     'author_id' => 'number', 'category_id' => 'number'],
    ],
    'authors' => [
        'model'  => new Author(),
        'fields' => ['name' => 'text', 'country' => 'text'],
    ],
    'categories' => [
        'model'  => new Category(),
        'fields' => ['name' => 'text'],
    ],
];

// Ambil halaman dari URL (?page=books). Kalau tidak ada, default 'books'.
$page = $_GET['page'] ?? 'books';
if (!isset($pages[$page])) {    // kalau nilai tidak terdaftar, paksa ke 'books' (whitelist)
    $page = 'books';
}
$model  = $pages[$page]['model'];    // model yang dipakai halaman ini
$fields = $pages[$page]['fields'];   // field form halaman ini

//PROSES POST: Create, Update, Delete 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {   // hanya jalan kalau form dikirim
    $op = $_POST['op'] ?? '';                  // jenis operasi: create/update/delete
    $id = (int) ($_POST['id'] ?? 0);           // (int) memastikan id berupa angka

    try {   // tangkap error database
        if ($op === 'create' || $op === 'update') {
            $data = [];
            $valid = true;
            foreach ($fields as $name => $type) {
                $val = trim($_POST[$name] ?? '');       // ambil input, buang spasi di tepi
                if ($val === '') {
                    $valid = false;                      // ada field kosong = tidak valid
                }
                // field number diubah ke integer, field text tetap string
                $data[$name] = $type === 'number' ? (int) $val : $val;
            }
            if (!$valid) {
                $_SESSION['flash'] = 'Semua field wajib diisi.';
            } elseif ($op === 'create') {
                $model->create($data);                   // panggil Model::create
                $_SESSION['flash'] = 'Data berhasil ditambahkan.';
            } else {
                $model->update($id, $data);              // panggil Model::update
                $_SESSION['flash'] = 'Data berhasil diubah.';
            }
        } elseif ($op === 'delete') {
            $model->delete($id);                         // panggil Model::delete
            $_SESSION['flash'] = 'Data berhasil dihapus.';
        }
    } catch (PDOException $ex) {
        // Kode 23000 = melanggar foreign key (misal hapus penulis yang masih punya buku)
        $_SESSION['flash'] = $ex->getCode() === '23000'
            ? 'Gagal: data masih dipakai/terhubung dengan data lain.'
            : 'Terjadi kesalahan database.';
    }

    // Post-Redirect-Get: pindah ke halaman GET agar refresh tidak mengirim ulang form
    header("Location: index.php?page=$page");
    exit;   // hentikan script setelah redirect
}

//PROSES GET: Read 
$editItem = null;                        // null = form dalam mode tambah
if (isset($_GET['edit'])) {              // kalau URL ada ?edit=3
    $editItem = $model->find((int) $_GET['edit']);   // ambil data untuk diisi ke form
}
$rows = $model->getAll();                // data untuk ditampilkan di tabel

// Halaman buku butuh data penulis & kategori untuk dropdown (relasi FK)
$authors = $categories = [];
if ($page === 'books') {
    $authors    = (new Author())->getAll();
    $categories = (new Category())->getAll();
}

// Rakit tampilan: header + isi halaman + footer
// Variabel di atas ($rows, $editItem, dst.) otomatis bisa dipakai di file view
require __DIR__ . '/view/header.php';
require __DIR__ . "/view/$page.php";
require __DIR__ . '/view/footer.php';