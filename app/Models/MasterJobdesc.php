<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJobdesc extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_jobdesc';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
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
     * Get the formatted date.
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Get the member jobdescs for this master jobdesc.
     */
    public function memberJobdescs()
    {
        return $this->hasMany(MemberJobdesc::class, 'id_jobdesc');
    }

    /**
     * Get the members that have this jobdesc.
     */
    public function members()
    {
        return $this->hasManyThrough(
            Member::class,
            MemberJobdesc::class,
            'id_jobdesc',
            'id',
            'id',
            'id_member'
        );
    }
}
