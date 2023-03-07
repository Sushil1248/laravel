<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDetail extends Model
{
	use HasFactory;
	public $timestamps = true;
	protected $fillable = [
	'company_id',
	 'address',
	 'mobile',
	 'estalish_date',
	 'city_id',
	 'state_id',
	 'country_id',
	 'zipcode',
	 'company_logo',
	 'cover_image',
	 'current_photos',
	 'details',
	 'gender',
	 'status',
	 'establish_date',
	 'created_at',
	 'updated_at',
	];

	protected function Company_logo(): Attribute
	{
		return Attribute::make(
			get: function($value){
				if( $value )
					return asset("storage/$value");
				return "";
			}
		);
	}

	protected function coverImage(): Attribute
	{
		return Attribute::make(
			get: function($value){
				if( $value )
					return asset("storage/$value");
				return asset("assets/images/cover.png");
			}
		);
	}

	public function Company(){
		return $this->belongsTo(Company::class);
	}

	public function country(){
		return $this->belongsTo(Country::class);
	}

	public function state(){
		return $this->belongsTo(State::class);
	}

	public function city(){
		return $this->belongsTo(City::class);
	}

	protected function currentPhotos(): Attribute
	{
		return [];
		return Attribute::make(
			get: function ($value, $attributes){
				$value = json_decode($value,1);
				//return $value;
				if( !is_array($value) )
					$value = [];
				$values = [
					'front'=>'',
					'side'=>'',
					'back'=>''
				];
				foreach( $values as $key => $singleValue )
				if( isset($value[$key]) )
				$values[$key] = asset("storage/{$value[$key]}");
				return $values;
			},
			set: function ($value, $attributes){
				if( !is_array($value) )
					$value = [];
				return json_encode($value);
			}
		);
	}

}

