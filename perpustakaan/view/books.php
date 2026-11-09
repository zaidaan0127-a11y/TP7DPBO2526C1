<h2 class="page-title">Data Buku</h2>

<!-- Card form: dipakai untuk tambah sekaligus edit -->
<section class="card">
  <h3><?= $editItem ? 'Edit Buku' : 'Tambah Buku' ?></h3> <!-- judul berubah sesuai mode -->

  <form method="post" action="index.php?page=books" class="form-grid">
    <!-- op & id dibaca index.php untuk menentukan create atau update -->
    <input type="hidden" name="op" value="<?= $editItem ? 'update' : 'create' ?>">
    <input type="hidden" name="id" value="<?= e($editItem['id'] ?? 0) ?>">

    <!-- value diisi data lama saat edit; kalau tambah, kosong -->
    <label>Judul <input type="text" name="title" value="<?= e($editItem['title'] ?? '') ?>" required></label>
    <label>Tahun <input type="number" name="year" value="<?= e($editItem['year'] ?? '') ?>" required></label>
    <label>Stok  <input type="number" name="stock" value="<?= e($editItem['stock'] ?? '') ?>" required></label>

    <label>Penulis
      <select name="author_id" required>
        <option value="">-- Pilih --</option>
        <?php foreach ($authors as $a): ?> <!-- dropdown dari tabel authors (FK) -->
          <!-- selected = pilihan otomatis sesuai data yang sedang diedit -->
          <option value="<?= (int) $a['id'] ?>" <?= ($editItem['author_id'] ?? 0) == $a['id'] ? 'selected' : '' ?>>
            <?= e($a['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>Kategori
      <select name="category_id" required>
        <option value="">-- Pilih --</option>
        <?php foreach ($categories as $c): ?> <!-- dropdown dari tabel categories (FK) -->
          <option value="<?= (int) $c['id'] ?>" <?= ($editItem['category_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>>
            <?= e($c['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <?php if ($editItem): ?><a href="index.php?page=books" class="btn btn-ghost">Batal</a><?php endif; ?>
    </div>
  </form>
</section>

<!-- Card tabel data -->
<section class="card">
  <h3>Daftar Buku</h3>
  <div class="table-wrap">
    <table>
      <tr><th>ID</th><th>Judul</th><th>Penulis</th><th>Kategori</th><th>Tahun</th><th>Stok</th><th>Aksi</th></tr>

      <?php foreach ($rows as $r): ?> <!-- satu baris tabel untuk tiap buku -->
      <tr>
        <td><?= (int) $r['id'] ?></td>
        <td><?= e($r['title']) ?></td>
        <td><?= e($r['author_name']) ?></td>    <!-- hasil JOIN di Book::getAll() -->
        <td><span class="badge"><?= e($r['category_name']) ?></span></td>
        <td><?= e($r['year']) ?></td>
        <td><?= e($r['stock']) ?></td>
        <td class="actions">
          <!-- Edit: kirim id lewat URL, index.php mengisinya ke form -->
          <a href="index.php?page=books&edit=<?= (int) $r['id'] ?>" class="btn btn-small">Edit</a>
          <!-- Hapus memakai POST (bukan link) agar aman -->
          <form method="post" action="index.php?page=books" onsubmit="return confirm('Hapus data ini?')">
            <input type="hidden" name="op" value="delete">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <button type="submit" class="btn btn-small btn-danger">Hapus</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>

      <?php if (!$rows): ?><tr><td colspan="7" class="empty">Belum ada data.</td></tr><?php endif; ?>
    </table>
  </div>
</section>