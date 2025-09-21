<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

class Address extends Model
{
    protected $fillable = [
        'member_id','address_type_id','line1','line2','city','state','postal_code','country'
    ];

    public function member(): Relations\BelongsTo {
        return $this->belongsTo(Member::class);
    }

    public function type(): Relations\BelongsTo {
        return $this->belongsTo(AddressType::class, 'address_type_id');
    }

    public function documents(): Relations\MorphMany {
        return $this->morphMany(Document::class, 'documentable');
    }
}
