<?php
// extends = Author mewarisi semua method Model (getAll, find, create, update, delete)
class Author extends Model
{
    protected string $table = 'authors';              // tabel yang dikelola class ini
    protected array $fillable = ['name', 'country'];  // kolom yang boleh diisi
}