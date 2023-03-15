<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{City,State,Country};
use Illuminate\Support\Facades\Storage;

class CitiesTableSeeder extends Seeder
{
    public function run(){

        if( !Country::count() ){
            $jsonCities = Storage::get('database/countries+states+cities.json');
            $allCountries = json_decode( $jsonCities );
            foreach( $allCountries as $singleCountry ){
                $country = Country::updateOrCreate(['name'    =>  $singleCountry->name],[
                    'short_code'    =>  $singleCountry->iso2,
                    'phonecode'    =>  $singleCountry->phone_code,
                    'status'        =>  1
                ]);
                foreach( $singleCountry->states as $singleState ){
                    $state = State::updateOrCreate([
                        'name'          =>  $singleState->name,
                        'country_id'    =>  $country->id,
                        'state_code'    =>  $singleState->state_code
                        ],[
                        'status'        =>  1
                    ]);
                    foreach( $singleState->cities as $singleCity ){
                        City::updateOrCreate(['city_name'          =>  $singleCity->name,'state_id'    =>  $state->id]);
                    }
                }
            }
        }
        
    }
}