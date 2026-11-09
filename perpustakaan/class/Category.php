<?php
class Category extends Model
{
    protected string $table = 'categories';   // tabel kategori
    protected array $fillable = ['name'];     // hanya satu kolom yang diisi
}