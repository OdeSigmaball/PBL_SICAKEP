<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_laporan';
    public function kegiatan()
{
    return $this->belongsTo(Kegiatan::class, 'id_kegiatan');
}
}
