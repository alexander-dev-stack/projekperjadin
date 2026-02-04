<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perjalanan extends Model
{
    protected $table = 'perjalanan';
    
    protected $fillable = [
        'perjadin_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'kota',
        'notadinas',
        'durasi',
        'jenis'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // Relationship: belongs to Perjadin
    public function perjadin()
    {
        return $this->belongsTo(Perjadin::class);
    }
}
