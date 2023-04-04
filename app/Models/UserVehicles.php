<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVehicles extends Model
{
    use HasFactory;
    protected $table= "user_vehicle";
    protected $fillable = ['user_id', 'vehicle_id', 'ride_status'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

}

