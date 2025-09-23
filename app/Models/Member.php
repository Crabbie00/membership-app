<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

class Member extends Model
{
    protected $fillable = ['name','email','phone','referral_code','referrer_id'];

    public function addresses(): Relations\HasMany {
        return $this->hasMany(Address::class);
    }

    public function documents(): Relations\MorphMany {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function referrer(): Relations\BelongsTo {
        return $this->belongsTo(Member::class, 'referrer_id');
    }

    public function referees(): Relations\HasMany {
        return $this->hasMany(Member::class, 'referrer_id');
    }

    public function profileImage()
    {
        return $this->morphOne(Document::class, 'documentable')
                    ->where('type', 'profile');
    }

    public function scopeFilter($query, $q = null, $ref = null)
    {
        return $query
            ->with('referrer')
            ->when($q, fn($qBuilder) => 
                $qBuilder->where(fn($w) => 
                    $w->where('name','like',"%{$q}%")
                      ->orWhere('email','like',"%{$q}%")
                )
            )
            ->when($ref, fn($qBuilder) =>
                $qBuilder->where('referral_code','like',"%{$ref}%")
                         ->orWhereHas('referrer', fn($r) =>
                             $r->where('name','like',"%{$ref}%")
                               ->orWhere('email','like',"%{$ref}%")
                               ->orWhere('referral_code','like',"%{$ref}%")
                         )
            )
            ->orderByDesc('id');
    }
}
