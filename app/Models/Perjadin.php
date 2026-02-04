<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perjadin extends Model
{
    // Explicitly define the table name
    protected $table = 'perjadin';

    // Disable automatic timestamps
    public $timestamps = false;
    
    protected $guarded = [];

    // Relationship: has many Perjalanan (trips)
    public function perjalanan()
    {
        return $this->hasMany(Perjalanan::class, 'perjadin_id');
    }
}
