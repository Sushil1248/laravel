<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Vehicle extends Model
{
    use HasFactory , Sortable, SoftDeletes;

    protected $fillable = ['name', 'vehicle_num', 'extra_notes', 'user_id', 'status'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

