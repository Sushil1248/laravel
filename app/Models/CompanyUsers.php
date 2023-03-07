<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyUsers extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'company_id',
    ];

    public function get_company_user() {
        // fetch company user
    }
}
