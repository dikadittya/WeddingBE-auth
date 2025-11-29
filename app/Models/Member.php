<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'members';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'jabatan',
        'alamat',
        'nomor_hp',
        'is_core_team',
        'key_name',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_core_team' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    /**
     * Get the member's created_at and updated_at date formatted.
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Scope a query to only include core team members.
     */
    public function scopeCoreTeam($query)
    {
        return $query->where('is_core_team', 1);
    }

    /**
     * Scope a query to only include non-core team members.
     */
    public function scopeNonCoreTeam($query)
    {
        return $query->where('is_core_team', 0);
    }

    /**
     * Check if member is core team.
     */
    public function isCoreTeam(): bool
    {
        return $this->is_core_team === 1;
    }

    /**
     * Get the jobdescs for the member.
     */
    public function jobdescs()
    {
        return $this->hasMany(MemberJobdesc::class, 'id_member');
    }

    /**
     * Get the master jobdescs through member jobdescs.
     */
    public function masterJobdescs()
    {
        return $this->hasManyThrough(
            MasterJobdesc::class,
            MemberJobdesc::class,
            'id_member',
            'id',
            'id',
            'id_jobdesc'
        );
    }

    /**
     * The user that owns this member.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
