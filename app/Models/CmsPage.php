<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CmsPage extends Model
{
    use HasFactory,SoftDeletes,Sortable;
    protected $fillable = ['name','short_content','content','media_id','status'];
    protected $appends = ['image_url'];
    public function media()
    {
        return $this->belongsTo(Media::class)->active();
    }
    protected function imageUrl(): Attribute{
        return Attribute::make(
            get: function( $value , $attributes ){                
                return $this->media ? $this->media->image_url : '' ;
            }
        );
    }
    protected function content(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => html_entity_decode($value),
        );
    }
}
