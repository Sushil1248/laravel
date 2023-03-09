<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Media extends Model
{
    use HasFactory , SoftDeletes , Sortable;
    protected $fillable = ['name','path','type','status'];
    protected $appends = ['image_url'];
    protected function imageUrl(): Attribute{
        return Attribute::make(
            get: function( $value , $attributes ){ 
                if( $attributes['path'] )
                    return Storage::url( $attributes['path'] );
                return '';
            }
        );
    }
    public function scopeActive($query)
    {
        $query->where('status', 1);
    }
}
