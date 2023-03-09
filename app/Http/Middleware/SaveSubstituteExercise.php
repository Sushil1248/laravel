<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\{ExerciseProgramDay,Exercise,WorkoutSubstituteExercise};
use Auth;
class SaveSubstituteExercise
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if( $request->filled('substitute_for_workout') && ( $exerciseProgramDay = ExerciseProgramDay::find( jsdecode_userdata($request->substitute_for_workout) ) ) ){
            $previousSubstituteExercise = $exerciseProgramDay->getSavedExercise( true );//Get subsctitute exercise only
            $nextExercise = $exerciseProgramDay->exercise->substituteExercises()->orderBy('exercises.id','asc');
            if( $previousSubstituteExercise )
                $nextExercise->where( 'exercises.id' , '>' , $previousSubstituteExercise->id );
            $nextExercise = $nextExercise->first();
            if( $nextExercise )
                WorkoutSubstituteExercise::updateOrCreate([
                    'user_id'   =>  Auth::id(),
                    'exercise_program_day_id'   =>  $exerciseProgramDay->id
                ],['exercise_id'    =>  $nextExercise->id]);
            else
                WorkoutSubstituteExercise::where('user_id',Auth::id())->where('exercise_program_day_id', $exerciseProgramDay->id )->delete();
        }

        return $next($request);
    }
}
