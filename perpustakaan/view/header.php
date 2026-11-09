<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sistem Perpustakaan</title>
  <link rel="stylesheet" href="style.css"> <!-- hubungkan ke file CSS -->
</head>
<body>

<header class="topbar">
  <h1>📚 Perpustakaan</h1>
  <nav>
    <!-- $page berasal dari index.php; menu yang sedang dibuka diberi class "active" -->
    <a href="index.php?page=books"      class="<?= $page === 'books' ? 'active' : '' ?>">Buku</a>
    <a href="index.php?page=authors"    class="<?= $page === 'authors' ? 'active' : '' ?>">Penulis</a>
    <a href="index.php?page=categories" class="<?= $page === 'categories' ? 'active' : '' ?>">Kategori</a>
  </nav>
</header>

<main class="container">
<?php if (!empty($_SESSION['flash'])): ?>
  <!-- Pesan sekali tampil dari index.php (berhasil/gagal) -->
  <div class="flash"><?= e($_SESSION['flash']) ?></div>
  <?php unset($_SESSION['flash']); ?> <!-- hapus agar tidak muncul lagi saat refresh -->
<?php endif; ?>
