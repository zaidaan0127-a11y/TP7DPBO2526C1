<?php
// abstract = class induk yang tidak boleh dibuat objeknya langsung (new Model() error).
// Hanya boleh diwarisi oleh class lain (Author, Category, Book).
abstract class Model
{
    // protected = bisa diakses class ini dan class turunannya, tapi tidak dari luar
    protected PDO $db;              // menyimpan objek koneksi database
    protected string $table;        // nama tabel (diisi oleh class turunan)
    protected array $fillable = []; // daftar kolom yang boleh diisi (diisi oleh class turunan)

    // Constructor: otomatis jalan saat objek dibuat, misal new Book()
    public function __construct()
    {
        // Ambil koneksi dari class Database (Singleton) dan simpan ke $this->db
        $this->db = Database::getInstance()->getConnection();
    }

    // READ: ambil semua data 
    public function getAll(): array
    {
        // prepare() = kirim template query ke MySQL dulu (belum dijalankan)
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY id DESC");
        $stmt->execute();           // jalankan query yang sudah disiapkan
        return $stmt->fetchAll();   // ambil semua baris hasil dalam bentuk array
    }

    // READ: ambil satu data berdasarkan id 
    public function find(int $id): ?array   // tanda ? = boleh mengembalikan null
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);     // isi placeholder :id dengan nilai $id
        return $stmt->fetch() ?: null;      // ambil 1 baris; kalau tidak ada (false) jadi null
    }

    // CREATE: tambah data baru 
    public function create(array $data): bool
    {
        // implode menggabungkan array jadi string: ['title','year'] => "title, year"
        $cols  = implode(', ', $this->fillable);
        $holds = implode(', ', array_map(fn($f) => ":$f", $this->fillable));
        $stmt  = $this->db->prepare("INSERT INTO {$this->table} ($cols) VALUES ($holds)");
        // bind() menyusun array nilai, lalu execute mengirimnya ke placeholder
        return $stmt->execute($this->bind($data));
    }

    // UPDATE: ubah data berdasarkan id 
    public function update(int $id, array $data): bool
    {
        // buat bagian SET: "title = :title, year = :year"
        $set  = implode(', ', array_map(fn($f) => "$f = :$f", $this->fillable));
        $stmt = $this->db->prepare("UPDATE {$this->table} SET $set WHERE id = :id");
        // gabungkan nilai kolom dengan nilai :id memakai operator +
        return $stmt->execute($this->bind($data) + [':id' => $id]);
    }

    //  DELETE: hapus data berdasarkan id 
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // private = hanya dipakai di dalam class Model ini
    // Fungsi: mengubah ['title'=>'A'] menjadi [':title'=>'A']
    private function bind(array $data): array
    {
        $params = [];
        foreach ($this->fillable as $f) {           // hanya kolom yang ada di $fillable
            $params[":$f"] = $data[$f] ?? null;     // ambil nilai; kalau tidak ada pakai null
        }
        return $params;
    }
}