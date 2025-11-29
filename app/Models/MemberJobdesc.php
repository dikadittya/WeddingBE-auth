<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberJobdesc extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'member_jobdesc';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_member',
        'id_jobdesc',
        'nama_jobdesc',
        'tipe_jobdesc',
        'key_job',
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
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the formatted date.
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Get the member that owns this jobdesc.
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member');
    }

    /**
     * Get the master jobdesc.
     */
    public function masterJobdesc()
    {
        return $this->belongsTo(MasterJobdesc::class, 'id_jobdesc');
    }
}
