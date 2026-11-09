<?php
class Book extends Model
{
    protected string $table = 'books';
    // author_id dan category_id adalah foreign key ke tabel lain
    protected array $fillable = ['title', 'year', 'stock', 'author_id', 'category_id'];

    // Override = menimpa getAll() milik Model dengan versi sendiri.
    // Tujuannya: supaya yang tampil nama penulis & kategori, bukan angka id-nya.
    public function getAll(): array
    {
        
        $sql = "SELECT b.*, a.name AS author_name, c.name AS category_name
                FROM books b
                JOIN authors a ON b.author_id = a.id
                JOIN categories c ON b.category_id = c.id
                ORDER BY b.id DESC";

        $stmt = $this->db->prepare($sql);   // siapkan query
        $stmt->execute();                   // jalankan
        return $stmt->fetchAll();           // ambil semua hasil sebagai array
    }
}