<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Kyslik\ColumnSortable\Sortable;

class Payment extends Model
{
    use HasFactory,Sortable;
    protected $fillable = ['subscription_id','amount_paid','status','raw_response'];
    protected function rawResponse(): Attribute{
        return Attribute::make(
            get: fn ($value, $attributes) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
