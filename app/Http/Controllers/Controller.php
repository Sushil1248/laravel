<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\UserProgress;
use Auth,DB;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /* Save user setting */
    function saveUpdateUserDetail( $key , $value , $multiple = false , $userId = null ){
        if( !$userId )
            $userId = Auth::id();
        
        if( $multiple )
            UserProgress::create([
                'user_id'   =>  $userId,
                'type'       =>  $key,
                'body_parts' =>   $value
            ]);
        else
            UserProgress::updateorCreate([
                'user_id'   =>  $userId,
                'type'       =>  $key
            ],[
                'body_parts' =>   $value
            ]);
    }

    /* Get user setting */
    function getUserDetail( $key , $multiple = false , $userId = null ){
        if( !$userId )
            $userId = Auth::id();
        $response = '';
        if( $multiple )
        {
            $userData = UserProgress::active()->where([
                'user_id'   =>  $userId,
                'type'       =>  $key
            ])->pluck('body_parts');
            
            if($userData)
                $response =  $userData->toArray();
        }
        else
        {
            $userData = UserProgress::active()->where([
                'user_id'   =>  $userId,
                'type'       =>  $key
            ])->first();

            if($userData)
                $response = $userData->body_parts;
        }
        return $response;
    }

    /* get program week of user */
    function getUserProgramWeek( $program , $programDate = null ){
        $weekNumber = 1;
        $todayDate = now();
        if( $programDate )
            $todayDate = Carbon::createFromFormat("Y-m-d", $programDate);
        if( $activeProgram = Auth::user()->activePrograms()->where('program_id',$program->id)->first() )
            $weekNumber = $todayDate->startOfWeek()->diffInWeeks( $activeProgram->pivot->start_date->startOfWeek() )  + 1;
        return $weekNumber;
    }

    /* Get program day of user */
    function getUserProgramDay( $program , $programDate = null ){
        $todayDate = now();
        if( $programDate )
            $todayDate = Carbon::createFromFormat("Y-m-d", $programDate);
        $r = $todayDate->dayOfWeekIso + 1;//Mysql Monday = 2
        if( $r == 8 )//Mysql Sunday = 1
            return 1;
        return $todayDate->dayOfWeekIso + 1;
    }

    protected function getNextWorkout( $singleExercise ){
        return $singleExercise->programDay->exerciseDetails()->where('id', '>', $singleExercise->id)->orderBy('id')->first();
    }

    /* Get week details */
    protected function getWeekDetails(){
        $program = Auth::user()->getActiveProgram();
        $userProgramWeek = $this->getUserProgramWeek( $program , request('program_date') );
        /* Week details */
        $week_details = [
            'week_1'    =>  [],
            'week_2'    =>  []
        ];
        for( $startOfWeek = now()->startOfWeek() ; $startOfWeek <= now()->endOfWeek() ; $startOfWeek->addDay() )
            $week_details['week_1'][] =   [
                'day'       =>  $startOfWeek->format("D")[0],
                'date'      =>  $startOfWeek->format("j"),
                'month'     =>  $startOfWeek->format("F"),
                'api_date'  =>  $startOfWeek->format("Y-m-d")
            ];
        if( $userProgramWeek < $program->number_of_weeks )
            for( $startOfWeek = now()->addWeek()->startOfWeek() ; $startOfWeek <= now()->addWeek()->endOfWeek() ; $startOfWeek->addDay() )
                $week_details['week_2'][] =   [
                    'day'       =>  $startOfWeek->format("D")[0],
                    'date'      =>  $startOfWeek->format("j"),
                    'month'     =>  $startOfWeek->format("F"),
                    'api_date'  =>  $startOfWeek->format("Y-m-d")
                ];
        return $week_details;
        /* End Week Details */
    }

    protected function allWorkoutSetsComplete( $singleExercise ){
        return $singleExercise->workoutSet->count() == $singleExercise->workoutSet()->whereHas('userWorkouts',function($query){
            $query->where('user_id',Auth::id());
        })->count();
    }

    /* Is week complete */
    protected function isWeekComplete( $date = null ){
        $program = Auth::user()->getActiveProgram();
        $week = $this->getUserProgramWeek( $program );
        $allProgramDays = $program->workouts()->frontendApi()->where('week', $week )->get();
        foreach( $allProgramDays as $singleDay )
            foreach( $singleDay->getExerciseDetail() as $singleWorkout )
                if( !$this->allWorkoutSetsComplete($singleWorkout) )
                    return false;
        return true;
    }

    /* Generate and save RM */
    public function generateSaveRm( $exercise ){
        $getSetList = DB::table('user_workout_sets')
        ->join('workout_sets', 'workout_sets.id', '=', 'user_workout_sets.workout_set_id')
        ->join('exercise_program_day', 'exercise_program_day.id', '=', 'workout_sets.workout_setable_id')
        ->select('user_workout_sets.*', 'exercise_program_day.exercise_id')
        ->where('user_workout_sets.user_id',Auth::id())
        ->where('exercise_program_day.exercise_id',$exercise->id)
        ->get();

        $singlerm = [];

        $max = 0;
        foreach ($getSetList as $key => $singleSet) {
            $amount =  $singleSet->weight; 
            if(isset($singlerm[$singleSet->time_or_reps])) {
                if($amount > $singlerm[$singleSet->time_or_reps] ) {
                    $singlerm[$singleSet->time_or_reps] = $amount;
                } 
            } else { 
                $singlerm[$singleSet->time_or_reps] = $amount;
            }
        }
         
    //     foreach($getSetList as $singleSet)
    //     {
           
    //         $singlerm[$singleSet->time_or_reps] = $singleSet->weight; 
    //     }

        $exercise->userRms()->updateOrCreate([
            'user_id'   =>  Auth::id()
        ],[
            'rm_details'    =>  $singlerm
        ]);
        
    }

    /* Return user workout sets */
    protected function getWorkoutSets( $singleWorkout ){
        $user = Auth::user();
        $rmData = $user->user_rm()
        ->where('exercise_id',$singleWorkout->exercise->primary_excercise_id)
        ->where('exercise_type','App\Models\PrimaryExcercise')->first();
        $parentWeight = 0;
        if($rmData)
            $parentWeight = $rmData->rm_details[1];
        $response = [];
        $userWeight = 0;
        foreach( $singleWorkout->workoutSet as $singleSet ){
            $set_mark = false;
            $getSetWeight = $singleSet->userWorkouts()->where('user_id',Auth::id())->first();
            if( $getSetWeight )
                $set_mark = true;
            if( $getSetWeight && empty($userWeight) )
                $userWeight = $getSetWeight->weight;
            
            if($singleSet->rm_percent) 
                $actualWeight = $getSetWeight ? $getSetWeight->weight : ($singleSet->rm_percent ? number_format((float)$singleSet->rm_percent/100 * $parentWeight, 2, '.', '') : '');
            else
                $actualWeight = $getSetWeight ? $getSetWeight->weight : $singleSet->recommended_wight/100 * $userWeight ;
            $response[] = [
                'id' => jsencode_userdata($singleSet->id),
                'set'   =>  $singleSet->sets,
                'time_or_reps'  =>  $singleSet->time_or_reps,
                'time_reps_type'    =>  strtoupper($singleSet->workoutSetable->set_type),
                'rest_period'  =>  $singleSet->rest_period,
                'rm_percent'  =>  $singleSet->rm_percent,
                'recommended_wight'  =>  $singleSet->recommended_wight,
                'actual_weight' =>  $actualWeight ? strval(getWeightFromDatabase( $actualWeight , 0 )) : '',
                'saved_weight_unit' =>  'KG -',
                'set_mark'  => $set_mark
            ];
        }
        return $response;
    }


    /* Admin create Sets of Workouts */
    function createWorkoutSets( $workout , $singleDetail , $editWorkout ){
        $singleExerciseDetail = [];
        $parent_id = null;
        foreach( $singleDetail->exercise as $requestSingleExercise  ){
            $requestSingleExercise = (object)$requestSingleExercise;
            $exerciseDetail = [
                'set_type'              =>  empty($requestSingleExercise->set_type) ? "reps" : $requestSingleExercise->set_type,
                'exercise_type'              =>  $requestSingleExercise->exercise_type,
                'exercise_id'   =>  jsdecode_userdata($requestSingleExercise->exercise_id)
            ];
            if( $parent_id ){
                $exerciseDetail['exercise_program_day_id'] = $parent_id;
                $parent_id = null;
            }
            
            if( isset($requestSingleExercise->exercise_detail) && ($singleExercise = $workout->exerciseDetails()->find( jsdecode_userdata($requestSingleExercise->exercise_detail) ) )  )
                $singleExercise->update( $exerciseDetail );
            else
                $singleExercise = $workout->exerciseDetails()->create( $exerciseDetail );
            
            if( $requestSingleExercise->exercise_type == "superset" )
                $parent_id = $singleExercise->id;
            elseif( $singleExercise->superSet )
                $singleExercise->superSet->delete();

            $singleExerciseDetail[] = $singleExercise->id;

            /* Synchronize Workouts Sets */
            $insertedSets = [];
            foreach( $requestSingleExercise->workout_set as $key  => $singleSet ){
                $singleSet = (object)$singleSet;
                $workoutSet = [
                    'sets'  =>  $key+1,
                    'time_or_reps'  =>  ( empty($requestSingleExercise->set_type) || $requestSingleExercise->set_type == "reps" ? $singleSet->reps : $singleSet->time ),
                    'rest_period'   =>  $singleSet->rest_period,
                    'rm_percent'    =>  $singleSet->rm_percent,
                    'recommended_wight'    =>  $singleSet->recommended_wight,
                ];
                if( empty($singleSet->workout_set_id) ){
                    $insertedSets[] = $singleExercise->workoutSet()->create( removeEmptyElements($workoutSet) )->id;
                }else{
                    $insertedSets[] = jsdecode_userdata($singleSet->workout_set_id);
                    $singleExercise->workoutSet()->where( 'id' , jsdecode_userdata($singleSet->workout_set_id) )->update( removeEmptyElements($workoutSet) );
                }
            }
            if( $editWorkout && !empty($insertedSets) ) 
                $singleExercise->workoutSet()->whereNotIn('id',$insertedSets)->delete();
            

            /* Synchronize Warmup Sets */
            $insertedSets = [];
            if( isset($requestSingleExercise->warmup_set) )
                foreach( $requestSingleExercise->warmup_set as $key  => $singleSet )
                {
                    $singleSet = (object)$singleSet;
                    $workoutSet = [
                        'sets'  =>  $key+1,
                        'time_or_reps'  =>  $singleSet->reps,
                        'recommended_wight'    =>  $singleSet->recommended_wight,
                        'is_warmup'     =>  1
                    ];
                    if( empty($singleSet->workout_set_id) ){
                        $insertedSets[] = $singleExercise->warmupWorkoutSet()->create( removeEmptyElements($workoutSet) )->id;
                    }else{
                        $insertedSets[] = jsdecode_userdata($singleSet->workout_set_id);
                        $singleExercise->warmupWorkoutSet()->where( 'id' , jsdecode_userdata($singleSet->workout_set_id) )->update( removeEmptyElements($workoutSet) );
                    }
                }
            
            if( $editWorkout && !empty($insertedSets) ) 
                $singleExercise->warmupWorkoutSet()->whereNotIn('id',$insertedSets)->delete();
        }

        /** Delete only in case of edit **/
        if( $editWorkout && !empty($singleExerciseDetail) ){
            $deleteExerciseDetails = $workout->exerciseDetails()->whereNotIn('id',$singleExerciseDetail)->get();
            foreach( $deleteExerciseDetails as $singleWorkout )
                $singleWorkout->delete(); 
        }
        
        return compact('singleExerciseDetail');
    }

    /** send info to klaviyo **/
    function sendUserToKlaviyo($user,$list_id = NULL){

        $apiKey =  config('klaviyo.klaviyo_private_api');
        $postdata = [
            'api_key' => $apiKey,
            'profiles' => array ('0' => array ( 'email' => $user->email ))
        ];
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://a.klaviyo.com/api/v2/list/".$list_id."/members?api_key=".$apiKey,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postdata),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);  
        curl_close($curl);
        return $response;
    }
}
