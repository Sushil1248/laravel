<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatiRole;
class Role extends SpatiRole
{
    use HasFactory;

    protected $fillable = [
        'created_by',
    ];
    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

}
