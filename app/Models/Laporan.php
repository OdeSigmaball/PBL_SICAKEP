<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    // Gunakan primary key custom jika tidak menggunakan 'id'
    protected $primaryKey = 'id_laporan';

    // Mass assignment: kolom yang diizinkan untuk diisi secara massal
    protected $fillable = [
        'nama_laporan',
        'dokumen',
        'id_kegiatan',
        'id_user',
    ];

    // Relasi ke model Kegiatan
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan');
    }
}
