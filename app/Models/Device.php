<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Device extends Model
{
    use HasFactory, SoftDeletes, Sortable;

    protected $fillable = [
        'device_name',
        'device_activation_code',
        'status',
        'user_id',
        'device_token',
        'device_id',
        'device_platform',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

     /* Scopes */
     public function scopeActive($query)
     {
         $query->where('status', 1);
     }
}
