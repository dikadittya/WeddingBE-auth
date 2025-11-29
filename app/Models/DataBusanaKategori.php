<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataBusanaKategori extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'data_busana_kategori';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_kategori',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all data busana for the kategori.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dataBusana()
    {
        return $this->hasMany(DataBusana::class, 'id_kategori', 'id')->select('id', 'nama_busana', 'id_kategori');
    }
}
