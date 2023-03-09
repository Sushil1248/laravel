<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\{AutoResponderTrait, SendResponseTrait};
use Illuminate\Support\Facades\{Validator,Auth,DB};
use App\Models\{Country,UserProgress,QuestionnaireType};
class UserController extends Controller
{
    use SendResponseTrait, AutoResponderTrait;

    /*
    Method Name:    completeProfile
    Created Date:   2022-09-23 (yyyy-mm-dd)
    Purpose:        Complete profile
    Params:         [gender,metric_values,height,weight,body_measurements,current_photos]
    */
    public function completeProfile( Request $request ){

        $validator = Validator::make($request->all(), [
            //'step_number'      =>  'required',
            'front'             =>  'file|mimetypes:image/*|max:' . config('constants.MAXIMUM_UPLOAD') * 1024,
            'side'              =>  'file|mimetypes:image/*|max:' . config('constants.MAXIMUM_UPLOAD') * 1024,
            'back'              =>  'file|mimetypes:image/*|max:' . config('constants.MAXIMUM_UPLOAD') * 1024,
        ]);
        if ($validator->fails()) {
            return $this->apiResponse('error', '422', $validator->errors()->all()[0] , $validator->errors() );
        }
        try {
            $details = [];
            if( $request->filled('step_number') ){
                $details['step_number'] = $request->step_number;
                switch( intval($request->step_number) ){
                    case 1:
                        $details = $request->only('gender','metric_values','height','weight','body_goal');
                        if($request->metric_values == 'Lb')
                        {
                            $details['weight'] = convertLbToKg($request->weight);
                            $details['height'] = convertInchToCm($request->height);
                        }
                    break;
                    case 2:
                        /** Current photos **/
                        $details['current_photos'] = [];
                        foreach( ['front','side','back'] as $singleImageType )
                            if( $request->hasFile($singleImageType) )
                                $details['current_photos'][$singleImageType] = str_replace("public/","",$request->$singleImageType->store('public/user-current-photos'));
                    break;
                }
            }else{
                /* Body measurement  */
                $details['body_measurements'] = $request->all();
            }
            Auth::user()->user_detail()->update( $details );

            $response = [];
            if($request->gender == 'female')
            {
                $user = Auth::user();
                $list_id = config('klaviyo.listFemaleId');
            }
            else{
                $user = Auth::user();
                $list_id = config('klaviyo.listMaleId');
            }
            $this->sendUserToKlaviyo( $user,$list_id );
            return $this->apiResponse('success', '200', 'Profile saved successfully.',$response);
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }



    /*
    Method Name:    getUser
    Created Date:   2022-09-23 (yyyy-mm-dd)
    Purpose:        Get profile of login user
    Params:         [gender,metric_values,height,weight,body_measurements,current_photos]
    */
    public function getUser( Request $request ){
        $user = $request->user();
        $userDetail = $user->user_detail;
        return $this->apiResponse('success', '200', 'Profile fetched successfully.',[
            'full_name'     =>  $user->full_name,
            'first_name'    =>  $user->first_name,
            'last_name'     =>  $user->last_name,
            'email'         =>  $user->email,
            'phone_number'  =>  $userDetail->mobile,
            'bio'           =>  $userDetail->bio,
            'gender'        =>  $userDetail->gender,
            'weight'        =>  $userDetail->weight,
            'body_fat'      =>  $userDetail->body_fat,
            'height'        =>  $userDetail->height,
            'bmi'           =>  $userDetail->bmi,
            'dob'           =>  $userDetail->dob,
            'profile_image' =>  $userDetail->profile_picture,
            'cover_image'   =>  $userDetail->cover_image,
            'metric_values' =>  $userDetail->metric_values,
            'notification'  =>  (int)$userDetail->notification,
            'main_boady_goals'  =>  $userDetail->body_goal,
            'notification_count'    =>  $user->unreadNotifications->count(),
            'country'       =>  [
                'id'    =>  $userDetail->country ? jsencode_userdata($userDetail->country->id) : '' ,
                'name'  =>  $userDetail->country ? $userDetail->country->name : ''
            ]
        ]);
    }

    /*
    Method Name:    getCountries
    Created Date:   2022-09-26 (yyyy-mm-dd)
    Purpose:        Get contries
    Params:         []
    */
    public function getCountries(){
        $countries = Country::pluck( 'name' , 'id' );
        $response_countries = [];
        foreach( $countries as $id => $name )
        $response_countries[] = [
            'id'    =>  jsencode_userdata( $id ),
            'name'  =>  $name
        ];
        return $this->apiResponse('success', '200', 'Countries fetched successfully.',[
            'countries' =>  $response_countries
        ]);
    }


    /*
    Method Name:    updateProfile
    Created Date:   2022-09-26 (yyyy-mm-dd)
    Purpose:        Update user profile
    Params:         []
    */
    public function updateProfile( Request $request ){
        $validations = [
            'name'      =>  'required',
            'profile_image' =>  'file|mimetypes:image/*|max:' . config('constants.MAXIMUM_UPLOAD') * 1024,
            'cover_image'   =>  'file|mimetypes:image/*|max:' . config('constants.MAXIMUM_UPLOAD') * 1024,
            'dob' =>  'required|date'
        ];
        if( $request->from_profile )
            unset( $validations['name'] , $validations['profile_image'] , $validations['dob'] );

        $validator = Validator::make($request->all(), $validations );
        if ($validator->fails()) {
            return $this->apiResponse('error', '422', $validator->errors()->all()[0] , $validator->errors() );
        }
        try {
            $inputs = removeEmptyElements( $request->all() );
            if( $request->has('notification') )
                $inputs['notification'] = $request->notification;
            if( !$request->from_profile ){
                $user = [
                    'first_name'    =>  $request->name
                ];
                Auth::user()->update( $user );
            }
            if( $request->filled('country') )
                $inputs['country_id'] = jsdecode_userdata($request->country);
            if( $request->hasFile('profile_image') )
                $inputs['profile_picture'] = str_replace("public/","",$request->profile_image->store('public/user-profile-picture'));

            if( $request->hasFile('cover_image') )
                $inputs['cover_image'] = str_replace("public/","",$request->cover_image->store('public/user-cover-image'));

            Auth::user()->user_detail->update( $inputs );
            return $this->apiResponse('success', '200', 'User profile updated successfully.', (array)$this->getUser($request)->getData()->data );
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }

    /*
    Method Name:    getNotifications
    Created Date:   2022-01-27 (yyyy-mm-dd)
    Purpose:        Get Notifications
    Params:         []
    */

    public function getNotifications(Request $response)
    {
        try{
            $getuser = Auth::user();
            $response = [
                //'query' =>  $getuser->notifications()->toSql()
            ];
            // return $getuser->notifications()->toSql();
            if( $getuser->user_detail && $getuser->user_detail->notification )
            foreach( $getuser->notifications()->limit(10)->get() as $key => $singleNotification ){
                $notification = [
                    'type'  =>  'admin',
                    'name'  =>  'IoT',
                    'image' =>  asset("assets/images/notification.png"),
                    'name_text' =>  '',
                    'message'   =>  config("constants.NOTIFICATION_MESSAGE.{$singleNotification->data['message']}") ? config("constants.NOTIFICATION_MESSAGE.{$singleNotification->data['message']}") : $singleNotification->data['message'],
                    'createdDate'   =>  $singleNotification->created_at->format("Y-m-d H:i:s"),
                    'show_renew'    =>  !$key,
                    'id'            =>  $singleNotification->id
                ];
                $action = $singleNotification->action;
                if( $action ){
                    $notification['type'] = 'user';
                    $notification['name'] = $action->user->full_name;
                    if( $action->user->user_detail && $action->user->user_detail->profile_picture )
                        $notification['image'] = $action->user->user_detail->profile_picture;
                }
                switch( $singleNotification->action_type ){
                    case('App\Models\ForumAnswerLike'):
                        $notification['message'] = $action->forumAnswer->answer;
                        $notification['name_text'] = config("constants.NOTIFICATION_MESSAGE.{$singleNotification->data['message']}");
                    break;
                    case('App\Models\ForumAnswer'):
                        $notification['message'] = $action->forumQuestion->title;
                        if( $action->forumAnswer )
                            $notification['message'] = $action->forumAnswer->answer;
                        $notification['name_text'] = config("constants.NOTIFICATION_MESSAGE.{$singleNotification->data['message']}");
                    break;
                }
                $response['userNotifications'][] = $notification;
            }
            return $this->apiResponse('success', '200', 'User notifications fetched successfully.' ,$response);
        } catch(Exception $e) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }


}
