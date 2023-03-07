<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes, Sortable;
    protected $fillable = [
        'name',
        'price',
        'billing_interval',
        'billing_interval_count',
        'product_id',
        'price_id',
        'description',
        'features_offered',
        'raw_response',
        'status'
    ];
    public function scopeActive($query)
    {
        $query->where('status', 1);
    }
    protected function rawResponse(): Attribute{
        return Attribute::make(
            get: fn ($value, $attributes) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }
}
