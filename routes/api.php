<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController,UserController,ApiController,StephWorkoutController,ExerciseController,ProgressController,WorkoutController,UserRmController,UserWorkoutSetsController,WeeklyPlannerController,ForumController};
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [AuthController::class,'register']);
Route::post('resend-verification-link',[AuthController::class,'resendVerificationLink']);
Route::post('login', [AuthController::class,'login']);
Route::post('forgot/password', [AuthController::class,'passwordResetLink']);
Route::post('verify/otp', [AuthController::class,'verifyOtp']);
Route::post('reset/password', [AuthController::class,'updateNewPassword']);
Route::get('get-countries',[UserController::class,'getCountries']);
Route::get('activate/device',[ApiController::class,'activateDevice']);

Route::middleware(['auth:api','validateUser'])->group(function(){
    Route::get('logout', [AuthController::class,'logout']);
    Route::post('complete-profile',[UserController::class,'completeProfile']);
    Route::get('user',[UserController::class,'getUser']);
    Route::get('get-profile',[UserController::class,'getProfile']);
    Route::post('update-profile',[UserController::class,'updateProfile']);


    Route::prefix('user')->group(function () {
        Route::get('get-notifications',[UserController::class,'getNotifications']);
        Route::match(['get','post'], 'save-user-progress',[ProgressController::class,'saveUserProgress']);
        Route::get('get-user-progress',[ProgressController::class,'getUserProgress']);
        Route::get('get-weekly-pics',[ProgressController::class,'getWeeklyBodyPics']);
        Route::match(['get','post'], 'add-personal-records/{type?}{id?}',[ProgressController::class,'addPersonalRecords']);
        Route::get('skip-weekly-progress',[ProgressController::class,'skipWeeklyProgress']);
    });

    Route::prefix('program')->group(function () {
        /** User Questionnaire **/
        Route::get('get-questions',[ProgramController::class,'getQuestions']);
        Route::post('save-question-answers',[ProgramController::class,'saveQuestionnarieAnswers']);
        Route::get('get-question-answers',[ProgramController::class,'getQuestionnarieAnswers']);
        Route::get('get-programs',[ProgramController::class,'getPrograms']);
        Route::get('get-program/{id}',[ProgramController::class,'getProgram'])->middleware(['save_substitute','user_has_program']);
        /*Route::get('get-program-exercise/{id}',[ProgramController::class,'getProgramExercise']); API not used */
        Route::post('save-active-program',[ProgramController::class,'saveActiveProgram']);
        Route::get('get-program-workout-sets/{id}',[UserWorkoutSetsController::class,'getUserWorkoutSets'])->middleware('save_substitute');
        Route::match(['get','post'],'save-user-workout-sets',[UserWorkoutSetsController::class,'saveUserWorkoutSets']);
        Route::match(['get','post'],'save-user-warmup',[UserWorkoutSetsController::class,'saveUserWarmUp']);

    });

    Route::prefix('steph-workout')->group( function () {
        Route::get('get-categories',[StephWorkoutController::class,'getCategories']);
    });

    Route::prefix('workout')->group( function () {
        Route::get('get-categories',[WorkoutController::class,'getWorkoutCategories']);
        Route::get('get-category-workouts/{id}',[WorkoutController::class,'getCategoryWorkouts']);
        Route::get('get-workout/{id}',[WorkoutController::class,'getWorkout'])->middleware('save_substitute');
        // Route::get('get-workout-exercise/{id}',[WorkoutController::class,'getWorkoutExercise']);
    });

    Route::prefix('exercises')->group(function(){
        /** Exercise List and categories **/
        Route::get('get-primary-muscles',[ExerciseController::class,'getPrimaryMuscles']);
        Route::get('get-muscles-exercise/{id}',[ExerciseController::class,'getMusclesExercise']);
        Route::get('get-exercise/{id}',[ExerciseController::class,'getExercise']);
        Route::get('get-exercises',[ExerciseController::class,'getExercises']);
        // Route::get('get-personal-records/{type?}{id?}',[ExerciseController::class,'getPersonalRecords']);
    });

    /* User's RM */
    Route::prefix('user-rm')->group(function(){
        Route::get('get-primary-exercises',[UserRmController::class,'getPrimaryExercises']);
        Route::post('save-rm',[UserRmController::class,'saveRm']);
        Route::post('generate-rm',[UserRmController::class,'generateRm']);
        Route::get('do-not-know-rm',[UserRmController::class,'doNotKnowRm']);
    });

    /* User's weekly plan */
    Route::prefix('user-weekly-plan')->middleware(['user_has_program'])->group(function(){
        Route::post('get-details',[WeeklyPlannerController::class,'getDetails']);
        Route::post('save-details',[WeeklyPlannerController::class,'saveDetails']);
    });

     /* Community List */
    Route::prefix('community')->group(function(){
        Route::get('get-community-list',[ForumController::class,'getCommunityList']);
        // Route::get('get-categories',[ForumController::class,'getCategoryList']);
        Route::post('save-community-post',[ForumController::class,'saveCommunityPost']);
        Route::match(['get','post'],'reply-community-post',[ForumController::class,'replyCommunityPost']);
        Route::post('get-answers',[ForumController::class,'getAnswersList']);
        Route::post('like-unlike-answer',[ForumController::class,'likeUnlikeAnswer']);
    });
});


