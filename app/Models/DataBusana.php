<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataBusana extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'data_busana';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_tipe_busana',
        'tipe_busana',
        'id_kategori',
        'nama_busana',
        'harga_beli',
        'tanggal_beli',
        'produk_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'harga_beli' => 'decimal:2',
        'tanggal_beli' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the master tipe busana that owns the data busana.
     */
    // public function masterTipeBusana()
    // {
    //     return $this->belongsTo(MasterTipeBusana::class, 'id_tipe_busana', 'id');
    // }

    /**
     * Get the data busana kategori that owns the data busana.
     */
    public function dataBusanaKategori()
    {
        return $this->belongsTo(DataBusanaKategori::class, 'id_kategori', 'id');
    }
}
