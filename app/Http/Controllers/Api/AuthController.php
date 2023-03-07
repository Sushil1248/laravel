<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use Illuminate\Support\Str;
use App\Models\{User, UserDetail, PasswordReset};
use Illuminate\Support\Facades\{Validator, Hash, Auth, Event}; 
use Symfony\Component\HttpFoundation\Response; 
use Spatie\Permission\Models\{Role, Permission}; 
use App\Traits\{AutoResponderTrait, SendResponseTrait};
use App\Events\LoginLogsEvent; 
use Carbon\Carbon;
use DB,Config;

class AuthController extends Controller
{
    use SendResponseTrait, AutoResponderTrait;

    public function register(Request $request){
        

        $validator = Validator::make($request->all(), [
            'name'      =>  'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|string|min:6|confirmed',
            'profile_image' =>  'file|mimetypes:image/*|max:' . config('constants.MAXIMUM_UPLOAD') * 1024,
            'date_of_birth' =>  'required|date'
        ], [
            'email.required'    => 'We need to know your email address',
            'email.email'       => 'Provide a an valid email address',
            'password.required' => 'You can not left password empty.',
            'password.string'   => 'Password field must be a string.'
        ]);
        if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->all()[0] , $validator->errors() );
        } 
        try { 
            DB::beginTransaction();
            $user = User::create([
                'first_name'    =>  $request->name,
                'email'         =>  $request->email,
                'password'      =>  Hash::make($request->password),
                'status'        =>  1
            ]);
            $user->assignRole('Customer');
            $userDetail = [
                'dob'   =>  $request->date_of_birth
            ];
            if( $request->hasFile('profile_image') )
                $userDetail['profile_picture'] = str_replace("public/","",$request->profile_image->store('public/user-profile-picture'));

            $user->user_detail()->create($userDetail);
            $this->sendVerifyEmail( $user );
            $this->sendUserToKlaviyo( $user,$list_id = config('klaviyo.listId'));
            DB::commit();
            return $this->apiResponse('success', '200', 'Account created successfully. Please verify your account from mail sent on registered email.');
        } catch(Exception $e) {
            DB::rollBack();
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }

   
    private function sendVerifyEmail( $user ){
        PasswordReset::where('email',$user->email)->where('type','verify-email')->delete();
        $passwordReset = PasswordReset::create([
            'email' =>  $user->email,
            'type'  =>  'verify-email',
            'token' =>  Str::random(40)
        ]);
        //Specifying templet to send
        $template = $this->get_template_by_name('VERIFY_CUSTOMER');
        //Creating token email specifically 
        $stringToReplace = ['{{$name}}', '{{$token}}' ];
        $stringReplaceWith = [$user->full_name, route('verify-customer', ['token'=>$passwordReset->token] ) ];
        $newval = str_replace($stringToReplace, $stringReplaceWith, $template->template);
        return $this->send_mail($user->email, $template->subject, $newval);         
        
    }

    public function resendVerificationLink( Request $request ){
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email|exists:users'
        ], [
            'email.required'    => 'We need to know your email address',
            'email.email'       => 'Provide a an valid email address',
            'email.exists'      =>  'Email does not exist'
        ]);
        if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->all()[0] , $validator->errors() );
        } 
        if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->all()[0] , $validator->errors() );
        } 
        try { 
            $user = User::where('email',$request->email)->first();
            if( $user->email_verified_at )
                return $this->apiResponse('error', '404', "Your account is already verified." );
            $this->sendVerifyEmail( $user );
            return $this->apiResponse('success', '200', 'Verification email sent successfully. Please verify your account from mail sent on registered email.');
        } catch(\Exception $e) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email|exists:users',
            'password'  => 'required|string|min:6'
        ], [
            'email.required'    => 'We need to know your email address',
            'email.email'       => 'Provide a an valid email address',
            'password.required' => 'You can not left password empty.',
            'password.string'   => 'Password field must be a string.',
            'email.exists'      =>  'Email does not exist'
        ]);
        if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->all()[0] , $validator->errors() );
        } 
        try { 
            $user = User::where('email',$request->email)->first();
            
            if( !Hash::check( $request->password , $user->password ) )
                return $this->apiResponse('error', '404', "Password is inavlid." );
            
            if( !$user->status )
                return $this->apiResponse('error', '404', "Hey, We found that your account is deactivated by the admin! To activate your account, contact the admin at abc@example.com." );
            
            if( !$user->email_verified_at )
                return $this->apiResponse('error', '404', "Email not verified yet.",['is_verified'=>false]);

            return $this->apiResponse('success', '200', 'Login successfully', [
                'token' =>  $user->createToken('login')->accessToken,
                'profile_completed' =>  $user->profile_completed,
                'completed_steps'   =>  $user->user_detail->step_number,
                'questions_submitted'   =>  boolval( $user->questionnaires->count() ),
                'active_program'        =>  ($program = $user->getActiveProgram()) ? jsencode_userdata($program->program_id) : false
            ]);
        } catch(\Exception $e) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }

    public function logout( $user = null ){
        try{
            if( !$user )
                $user = Auth::user();
            if( $user ){
                $user->tokens->each(function($token, $key) {
                    $token->delete();
                });
            }
            return $this->apiResponse('success', '200', 'Logout successfully');
        }catch(\Exception $e){
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }

    /*
    Method Name:    passwordResetLink
    Created Date:   2022-09-21 (yyyy-mm-dd)
    Purpose:        Send a OTP email to reset new password if user forgot password
    Params:         [email]
    */ 
    public function passwordResetLink(Request $request)
    {  
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email|exists:users',
            
        ], [
            'email.required'    => 'We need to know your email address',
            'email.email'       => 'Provide a an valid email address'
        ]);
        if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->all()[0] , $validator->errors() );
        } 
        try { 
            $user = User::where('email', $request->email)->first();
            //check user existance  
            if (!$user)
                return $this->apiResponse('error', '404', config('constants.ERROR.NOT_VALID_EMAIL'));
            //Specifying templet to send
            $template = $this->get_template_by_name('FORGOT_PASSWORD');
            PasswordReset::where('email',$user->email)->where('type','password-reset')->delete();
            //Creating token email specifically 
            $passwordReset = PasswordReset::updateOrCreate( ['email' => $user->email,'type'  =>  'password-reset'], [  'token' => rand(100000,999999) ] );
            $otp = $passwordReset->token;
            $stringToReplace = ['{{$name}}', '{{$token}}'];
            $stringReplaceWith = [$user->full_name, $otp];
            $newval = str_replace($stringToReplace, $stringReplaceWith, $template->template);
            //mail logs
            $result = $this->send_mail($user->email, $template->subject, $newval);         
            if( $result ) 
                return $this->apiResponse('success', '200', 'OTP sent on email.');
            return $this->apiResponse('error', '404', 'Unable to send email.' );
        } catch(\Exception $e) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }
    /* End Method passwordResetLink */


    /*
    Method Name:    verifyOtp
    Created Date:   2022-09-21 (yyyy-mm-dd) 
    Purpose:        Verify forgot password token
    Params:         [token]
    */ 
    public function verifyOtp( Request $request ){
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email|exists:users',
            'otp'       =>  'required'
        ], [
            'email.required'    => 'We need to know your email address',
            'email.email'       => 'Provide a an valid email address'
        ]);
        if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->all()[0] , $validator->errors() );
        }
        try {
            $passwordReset = PasswordReset::where( 'email', $request->email )->where('type','password-reset')->where('token',$request->otp)->first();
            if( $passwordReset )
                return $this->apiResponse('success', '200', 'OTP Verified.');
            return $this->apiResponse('error', '404', 'Provided OTP is invalid.');
        } catch ( \Exception $e ) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }

    

    /*
    Method Name:    updateNewPassword
    Created Date:   2022-09-21 (yyyy-mm-dd)
    Purpose:        Update new password
    Params:         [email, password, password_confirmation]
    */ 
    public function updateNewPassword(Request $request)
    {   
        //validate incoming request   
        $rules = [ 
            'email'     => 'required|email|exists:users',
            'password'  => 'required|string|confirmed|min:6',
            'otp'       =>  'required'
        ];
        $messages = [
            'email.required'    => 'We need to know your email address',
            'email.email'       => 'Provide a an valid email address',
            'password.required'  => 'Password is required',
            'password.confirmed' => 'Confirmed password not matched with password'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->all()[0] , $validator->errors() );
        }
        try {
            $passwordReset = PasswordReset::where('email', $request->email)->where('type','password-reset')->first();
            if( !$passwordReset )
                return $this->apiResponse('error', '404', "Please verify OTP first.");
            if( $passwordReset->token != $request->otp )
                return $this->apiResponse('error', '404', 'Provided OTP is invalid.');
            $record = User::where('email', $request->email)->update([
                'password'      =>  Hash::make($request->password)
            ]);
            PasswordReset::where('email', $passwordReset->email)->where('type','password-reset')->delete();
            return $this->apiResponse('success', '200', 'Password updated sucessfully.');

            return $this->loginMethod($passwordReset->email, $request->password); 
        } catch ( \Exception $e ) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }
    /* End Method updateNewPassword */

    /*
    Method Name:    profile
    Created Date:   2022-04-13 (yyyy-mm-dd)
    Purpose:        Get profile detail on the basis of bearer token
    Params:         []
    */ 
    public function profile() 
    {
        try {
            $user = Auth::user(); 
            $userData = $user->toArray();
            if($user->hasRole( config('constants.ROLES.COMPANY') ) ) {  
                $userDetail = $user->company;
                $userData['thanks_message'] =  $userDetail ? $userDetail->thanks_message : NULL;
                $userData['notification']   =  $userDetail ? $userDetail->notification : '';
                $userData['logo']           =  $userDetail ? $userDetail->logo : '';
                $userData['company_name']   =  $userDetail ? $userDetail->company_name : '';
                $userData['slug']           =  $userDetail ? $userDetail->slug : '';
            } else { 
                $userDetail         = $user->userdetail;
                $userData[ 'dob']   = $userDetail ?  $userDetail->dob : '';
            } 
             
            $userData['phone_number']   = $userDetail ? $userDetail->phone_number : '';
            $userData['address']        = $userDetail ?  $userDetail->address : '';
            $userData['city']           = $userDetail ?  $userDetail->city : '';
            $userData['fax']            = $userDetail ?  $userDetail->fax : '';
            $userData['zip_code']       = $userDetail ?  $userDetail->zip_code : '';
            $userData['state_id']       = $userDetail ? encryptData($userDetail->state_id) : NULL;
            $userData['country_id']     = $userDetail ? encryptData($userDetail->country_id) : NULL; 

            return $this->apiResponse('success', '200', 'User profile ', $userData);
        } catch ( \Exception $e ) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }
    /* End Method profile */

    /*
    Method Name:    detailUpdate
    Created Date:   2022-04-13 (yyyy-mm-dd)
    Purpose:        Update user detail after login
    Params:         [first_name, last_name, phone_number, dob, address, city, state_id, country_id]
    */ 
    public function detailUpdate(Request $request)
    {  
        $validationRules = [
            'first_name'    => 'required|string|max:100', 
            'last_name'     => 'required|string|max:100', 
            'phone_number'  => 'required|unique:user_details,phone_number,'.Auth::id().',user_id', 
            'dob'           => 'before:'. date('Y-m-d') .'|date_format:Y-m-d',
            'address'       => 'string|max:200',
            'city'          => 'string|max:100',
            'state_id'      => 'string',
            'country_id'    => 'string',
            'logo'          => 'nullable|mimetypes:image/*|max:1024',
            'zip_code'      => 'max:30'
        ];
		
        if( Auth::user()->hasRole( config('constants.ROLES.COMPANY') ) ) {
            $validationRules['company_name'] = 'required|max:255'; 
            $uniqueRule = 'unique:company_details,slug,' . authId() .',user_id';
            $validationRules['slug'] = "required|max:255|regex:/^[a-zA-Z]+[a-zA-Z0-9-]*$/|$uniqueRule|max:255";
            $validationRules['phone_number'] = 'required|unique:company_details,phone_number,'.Auth::id().',user_id';
        }
        $validator = Validator::make($request->all(), $validationRules);
		if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->first());
        } 
        try {
            $user = User::findOrFail(Auth::id()); 
			$user->first_name   = $request->first_name;    
			$user->last_name    = $request->last_name;    

            $data = [ 
                'phone_number'  => $request->phone_number, 
                'address'       => $request->address, 
                'city'          => $request->city, 
                'fax'           => $request->fax, 
                'zip_code'      => $request->zip_code, 
                'state_id'      => $request->state_id ? decryptData($request->state_id): NULL, 
                'country_id'    => $request->country_id ? decryptData($request->country_id): NULL, 
            ];

            if( $user->hasRole(config('constants.ROLES.COMPANY')) ) {    
                $data['notification']   = $request->input('notification',0);
                $data['company_name']   = $request->company_name;
                $data['slug']           = $request->slug;

                if( $request->hasFile('logo') ) {
                    $mimeType       = $request->logo->getMimeType();
                    $content        = file_get_contents($request->logo->getRealPath());
                    $base64         = 'data:' . $mimeType . ';base64,' . base64_encode($content);
                    $data['logo']   = $base64;
                }
                $companyDetail= CompanyDetail::updateOrCreate( ['user_id' => Auth::id()], $data );
            } else {
                $data['dob']    = $request->dob; 
                $userDetail     = UserDetail::updateOrCreate( ['user_id' => Auth::id()], $data );
            }
            $user->save();  

            return $this->apiResponse('success', '200', 'Profile details '.config('constants.SUCCESS.UPDATE_DONE'));
        } catch(\Exception $e) {
            return $this->apiResponse('error', '400', $e->getMessage());
        } 
    }    
    /* End Method detailUpdate */
    
    /*
    Method Name:    updatePassword
    Created Date:   2022-04-13 (yyyy-mm-dd)
    Purpose:        Update password after login
    Params:         [old_password, password, password_confirmation]
    */ 
    public function updatePassword(Request $request)
    {
        $rules = [
            'old_password'          => 'required',
            'password'              => 'required|required_with:password_confirmation|string|confirmed|min:6',
            'password_confirmation' => 'required'
        ];
        $messages = [
            'password.required'     => 'Password is required',
            'password.confirmed'    => 'Confirmed password not matched with password'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) { 
            return $this->apiResponse('error', '422', $validator->errors()->first());
        }

        try {
            $user = User::findOrFail(authId());
            if (!(app('hash')->check($request->get('old_password'), $user->password))) {
                return $this->apiResponse('error', '422', [config('constants.ERROR.PASSWORD_MISMATCH')]);
            }
            if (strcmp($request->old_password, $request->password) == 0) {
                return $this->apiResponse('error', '422', [config('constants.ERROR.PASSWORD_SAME')]);
            } 
            User::where('id', authId())->update([ 'password' => app('hash')->make($request->password) ]);            

            return $this->apiResponse('success', '200',  'Password '.config('constants.SUCCESS.UPDATE_DONE'));
        } catch ( \Exception $e ) {
            return $this->apiResponse('error', '400', $e->getMessage());
        }
    }
    /* End Method updatePassword */

    

}