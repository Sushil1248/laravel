<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpInformation extends Model
{
	
    public $timestamps = true;
    protected $fillable = [
       	'host',
       	'port',
       	'from_email',
       	'from_name',
        'username',
       	'password',
       	'encryption',
       	'status',
	    'created_at',
	    'updated_at',
    ];
	public $sortable = [ 
		'id',
        'host',
        'port',
        'from_email',
        'username',
        'from_name',
        'password',
        'encryption',
        'status',
        'created_at',
        'updated_at',
 ];
}
