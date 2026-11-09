<h2 class="page-title">Data Penulis</h2>

<section class="card">
  <h3><?= $editItem ? 'Edit Penulis' : 'Tambah Penulis' ?></h3>
  <form method="post" action="index.php?page=authors" class="form-grid">
    <input type="hidden" name="op" value="<?= $editItem ? 'update' : 'create' ?>"> <!-- penentu create/update -->
    <input type="hidden" name="id" value="<?= e($editItem['id'] ?? 0) ?>">

    <label>Nama   <input type="text" name="name" value="<?= e($editItem['name'] ?? '') ?>" required></label>
    <label>Negara <input type="text" name="country" value="<?= e($editItem['country'] ?? '') ?>" required></label>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <?php if ($editItem): ?><a href="index.php?page=authors" class="btn btn-ghost">Batal</a><?php endif; ?>
    </div>
  </form>
</section>

<section class="card">
  <h3>Daftar Penulis</h3>
  <div class="table-wrap">
    <table>
      <tr><th>ID</th><th>Nama</th><th>Negara</th><th>Aksi</th></tr>
      <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= (int) $r['id'] ?></td>
        <td><?= e($r['name']) ?></td>
        <td><?= e($r['country']) ?></td>
        <td class="actions">
          <a href="index.php?page=authors&edit=<?= (int) $r['id'] ?>" class="btn btn-small">Edit</a>
          <form method="post" action="index.php?page=authors" onsubmit="return confirm('Hapus data ini?')">
            <input type="hidden" name="op" value="delete">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <button type="submit" class="btn btn-small btn-danger">Hapus</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="4" class="empty">Belum ada data.</td></tr><?php endif; ?>
    </table>
  </div>
</section>