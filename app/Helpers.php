<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use GuzzleHttp\Client;
use App\Models\{Customer,User,Service,Program,CompanyDetail,QuestionnaireType,Category, Company};
use App\Notifications\CommonNotification;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
// use SendinBlue;

if (!function_exists('changeDateFormat')) {
    function changeDateFormat($date, string $format = 'j F, Y')
    {
        if( empty($date) )
            return "";
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (!function_exists('SendSMS')) {
    function SendSMS($number = null, $message = null){
        if($number && $message){
            $route = 'route=q';
            if(config('app.smsRoute') == 'v3'){
                $route = 'route=v3&sender_id=FTWSMS';
            }
            $url = 'https://www.fast2sms.com/dev/bulkV2?authorization=Y6ZozSm3cOQp50ABKbFlkVyxDwWIL7M4vhGjsP9grREUnt1efJPwL2uB0Il7xno1dZcATmiYSWqOXgRr&'.$route.'&message='.rawurlencode($message).'&language=english&flash=0&numbers='.$number;
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $responseData = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return $responseData;
        }
        return false;
    }
}
if (!function_exists('change_date_format'))
{
    function change_date_format(String $date, string $format = 'd-m-Y h:i A', $timezone = 'Asia/Kolkata', $timezoneCode = 'IST', $forBook = null)
    {
        $newDate = Carbon::createFromFormat('Y-m-d H:i:s', changeDateFormat($date, 'Y-m-d H:i:s'), 'UTC');
        $newDate->setTimezone($timezone);
        if($forBook){
            return $newDate->format('h:i A');
        }
        return $newDate->format($format);
    }
}
if (!function_exists('get_timestamp'))
{
    function get_timestamp()
    {
        return Carbon::now()->timestamp;

    }
}
if (!function_exists('get_user_name'))
{
    function get_user_name($user_id) {
        $user = User::find($user_id);
        return $user ? $user->first_name : '';
    }
}

if (!function_exists('get_permission_by_user_group'))
{
    function get_permission_by_user_group($groupName)
    {
        return Permission::where('group_name', $groupName)->get();
    }
}

if (!function_exists('getDifferenceInSecond'))
{
    function getDifferenceInSecond($startTime, $finishTime)
    {
        $startTime = date("Y-m-d h:i:s", $startTime);
        $finishTime = date("Y-m-d h:i:s", $finishTime);
        $startTime = Carbon::parse($startTime);
        $finishTime = Carbon::parse($finishTime);
        return $totalDuration = $finishTime->diffInSeconds($startTime);
    }
}
if (!function_exists('get_1_month_old_date'))
{
    function get_1_month_old_date()
    {
        $date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
        return $date;

    }
}
if (!function_exists('add_days_to_date'))
{
    function add_days_to_date(string $date, string $days)
    {
        if ($days == 'Monthly')
        {
            $no_of_days = 30;
        }
        elseif ($days == 'Quaterly')
        {
            $no_of_days = 91;
        }
        elseif ($days == 'HalfYearly')
        {
            $no_of_days = 182;
        }
        elseif ($days == 'Annually')
        {
            $no_of_days = 365;
        }
        $expiry_date = \Carbon\Carbon::createFromFormat('Y-m-d', $date);
        $expiry_date = $expiry_date->addDays($no_of_days);
        $expiry_date = \Carbon\Carbon::parse($expiry_date)->format('Y-m-d');
        return $expiry_date;
    }
}
if (!function_exists('encrypt_userdata'))
{
    function encrypt_userdata(string $data)
    {
        try
        {
            $encryptData = Crypt::encryptString($data);
            return $encryptData;
        }
        catch(\Exception $e)
        {
            abort('403');
        }
    }
}
if (!function_exists('decrypt_userdata'))
{
    function decrypt_userdata(string $data)
    {
        try
        {
            $decryptData = Crypt::decryptString($data);
            return $decryptData;
        }
        catch(\Exception $e)
        {
            abort('403');
        }
    }
}




if (!function_exists('jsencode_userdata')) {
    function jsencode_userdata($data, string $encryptionMethod = null, string $secret = null)
    {
        //return $data."";
        if( empty($data) )
            return "";
        $encryptionMethod = config('app.encryptionMethod');
        $secret = config('app.secrect');
        try {
            $iv = substr($secret, 0, 16);
            $jsencodeUserdata = str_replace('/', '!', openssl_encrypt($data, $encryptionMethod, $secret, 0, $iv));
            $jsencodeUserdata = str_replace('+', '~', $jsencodeUserdata);
            return $jsencodeUserdata;
        } catch (\Exception $e) {
            return null;
        }
    }
}
if (!function_exists('jsdecode_userdata')) {
    function jsdecode_userdata($data, string $encryptionMethod = null, string $secret = null)
    {
        //return $data."";
        if( empty($data) )
            return null;
        $encryptionMethod = config('app.encryptionMethod');
        $secret = config('app.secrect');
        try {
            $iv = substr($secret, 0, 16);
            $data = str_replace('!', '/', $data);
            $data = str_replace('~', '+', $data);
            $jsencodeUserdata = openssl_decrypt($data, $encryptionMethod, $secret, 0, $iv);
            return $jsencodeUserdata;
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('jsdecode_array_data')) {
    function jsdecode_array_data($data)
    {
        if( empty($data) )
            return [];
        return array_map( function($value){
            return jsdecode_userdata($value);
        } , $data );
    }
}

if (!function_exists('pluralUserType')) {
    /**
    * Get plural form of the user type.
    * @param string $path
    * @param bool $secure
    */
    function pluralUserType(string $userType)
    {
        switch($userType){
            case "user":
                return "Users";
            case "company":
                return "Companies";
        }
        return "";
    }
}

if (!function_exists('getParentCompany')) {
    /**
    * Get parent company of current login user
    * @return user id
    */
    function getParentCompany(){
        if( Auth::user()->hasRole('Company') )
            return Auth::id();
        if( !Auth::user()->hasRole(['HR','Employee']) )
            return null;
        return Auth::user()->parent_id;
    }
}

if (!function_exists('getCustomerQueryBuilder')) {
    /*
    * Get customer Query Builder
    */
    function getCustomerQueryBuilder(){
        return new Customer;
    }
}

if (!function_exists('getUserQueryBuilder')) {
    /*
    * Get user Query Builder
    */
    function getUserQueryBuilder(){
        return new User;
    }
}

if (!function_exists('getServiceQueryBuilder')) {
    /*
    * Get user Query Builder
    */
    function getServiceQueryBuilder(){
        $services = new Service;
        if( Auth::user()->hasRole(['HR','Employee','Company']) )
        $services = $services->where('company_id',getParentCompany());
        return $services;
    }
}
if (!function_exists('encryptDatabaseIds')) {
    /*
    * Encrypt Ids in record
    */
    function encryptDatabaseIds( $dbRecords ){
        foreach( $dbRecords as &$signleRecord ){
            if( isset($signleRecord['id']) )
                $signleRecord['id'] = jsencode_userdata($signleRecord['id']);
        }
        return collect($dbRecords);
    }
}
if (!function_exists('isUserStatusActive')) {
    /*
    * Check status of current login user
    */
    function isUserStatusActive(  ){
        if( auth()->check() && auth()->user()->status )
            return true;
        return false;
    }
}
/*
    Method Name:    removeEmptyElements
    Created Date:   2022-08-15 (yyyy-mm-dd)
    Purpose:        empty inputs from request which are empty
    Params:         [userid]
*/
if( !function_exists('removeEmptyElements') ){
    function removeEmptyElements( array $array ){
        return array_filter( $array , function( $value ){
            return !empty($value);
        } );
    }
}
/* End Method removeEmptyElements */
/*
    Method Name:    getProgramExercises
    Created Date:   2022-08-15 (yyyy-mm-dd)
    Purpose:        Get program exercises for each day
    Params:         [program_id]
*/
if( !function_exists('getProgramExercises') ){
    function getProgramExercises( $program_id ){
        $program = Program::find( jsdecode_userdata($program_id) );
        if( empty($program) )
            return [];
        $workouts = $program->workouts()->withTrashed()->get();
        $workoutExercises = [];
        foreach( $workouts as $singleWorkout ){
            $key = "{$singleWorkout->week}-{$singleWorkout->day_of_week}";
            $workoutExercises[$key] = [];
            foreach( $singleWorkout->exercises as $singleExercise ){
                $workoutExercises[$key][] = jsencode_userdata($singleExercise->id);
            }
        }
        return $workoutExercises;
    }
}

function limitListingContent( $string ){
    $string = strip_tags( $string );
    $maxString = 50;
    if( strlen($string) > $maxString )
        $string = substr($string,0,$maxString) . " ...";
    return $string;
}

function getQuestionTypes(){
    return QuestionnaireType::active()->pluck('name')->toArray();
}


function removeQueryParameter($parameter){
    $query_string = request()->query();
    unset( $query_string[$parameter] );
    return "?". http_build_query($query_string);
}

function printRawSql( $builder ){
    $query = str_replace(array('?'), array('\'%s\''), $builder->toSql());
    $query = vsprintf($query, $builder->getBindings());
    return $query;
}
function displayWorkoutType( $key ){
    return ucwords( str_replace('_',' ',$key) );
}

function getCategoriesOfType( $parent=null , $ignoreCategory = null ){
    return Category::active()->parent($parent)->when($ignoreCategory,function($query,$id){
        $query->where('id','<>',$id);
    })->get();
}

function get_company_name($company_id){
    return CompanyDetail::where('user_id', $company_id)->pluck('company_name')->first();
}

function getMinutes($interval)
{
    $hours = floor($interval / 3600);
    $minutes = floor(($interval / 60) % 60);
    $seconds = $interval % 60;
    return $minutes;
}

function convertLbToKg($lb)
{
    return $lb * 0.453592;
}

function convertKgToLb($kg)
{
    return $kg / 0.453592;
}

function convertInchToCm($inch)
{
    $result = $inch * 2.54;
    return $result;
}

/* Get weight unit as saved by user */
function getWeightFromDatabase( $value , $addUnit = true){
    if( strcasecmp( Auth::user()->user_detail->metric_values , "lb" ) === 0 )
        $value = convertKgToLb( $value );
    if( $addUnit )
        return $value . " " . strtoupper(Auth::user()->user_detail->metric_values);
    return $value;
}

/* Always save KG in database */
function saveWeightIntoDatabase( $value ){
    if( strcasecmp( Auth::user()->user_detail->metric_values , "lb" ) === 0 )
        $value = convertLbToKg( $value );
    return $value;
}

function set_default_value( $pairs, $atts ) {
	$atts = (array) $atts;
	$out  = array();
	foreach ( $pairs as $name => $default ) {
		if ( array_key_exists( $name, $atts ) ) {
			$out[ $name ] = $atts[ $name ];
		} else {
			$out[ $name ] = $default;
		}
	}
	return $out;
}

if (!function_exists('checkDeviceTokenExists')) {
    function checkDeviceTokenExists() {
        return DB::table('devices')->whereNotNull('device_token')->exists();
    }
}

if (!function_exists('hasGroupPermission')) {
    function hasGroupPermission($groupName)
    {
        $user = Auth::user();
        $groupPermissions = Permission::where('group_name', $groupName)->pluck('name')->toArray();
        return $user->hasAnyPermission($groupPermissions);
    }
}


