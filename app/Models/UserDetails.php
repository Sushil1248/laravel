<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UserDetails extends Model
{
	use HasFactory;
	public $timestamps = true;
	protected $fillable = [
	'user_id',
	 'address',
	 'mobile',
	 'dob',
	 'city_id',
	 'state_id',
	 'country_id',
	 'zipcode',
	 'profile_picture',
	 'cover_image',
	 'current_photos',
	 'bio',
	 'gender',
	 'about_us',
	 'status',
	 'notification',
	 'created_at',
	 'updated_at',
	];

	protected function profilePicture()
	{
		return Attribute::make(
			get: function($value){
				if( $value )
					return asset("storage/$value");
				return "";
			}
		);
	}

    // gets automatically invoked :Attribute / hasOne
	protected function coverImage()
	{
		return Attribute::make(
			get: function($value){
				if( $value )
					return asset("storage/$value");
				return asset("assets/images/cover.png");
			}
		);
	}

	public function user(){
		return $this->belongsTo(User::class);
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

	protected function currentPhotos()
	{
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
