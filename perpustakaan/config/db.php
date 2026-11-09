<?php
class Database
{
    // static = milik class, bukan milik objek. Menyimpan satu-satunya objek Database.
    private static ?Database $instance = null;
    private PDO $conn;                      // objek koneksi PDO

    // Data koneksi (private = tidak bisa diubah dari luar)
    private string $host = 'localhost';
    private string $dbname = 'db_perpustakaan';
    private string $user = 'root';
    private string $pass = '';

    // Constructor private = tidak bisa new Database() dari luar (pola Singleton)
    private function __construct()
    {
        // DSN = alamat koneksi: jenis DB, host, nama DB, charset
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
        $this->conn = new PDO($dsn, $this->user, $this->pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // error dilempar sebagai exception
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // hasil berupa array asosiatif
            PDO::ATTR_EMULATE_PREPARES   => false,                    // pakai prepared statement asli MySQL
        ]);
    }

    // Satu-satunya cara mendapatkan objek Database
    public static function getInstance(): Database
    {
        // ??= artinya: kalau $instance masih null, buat baru; kalau sudah ada, pakai yang lama
        return self::$instance ??= new Database();
    }

    // Mengembalikan objek PDO untuk dipakai Model
    public function getConnection(): PDO
    {
        return $this->conn;
    }
}
