<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Config, Auth, Validator, Hash, Crypt};
use Illuminate\Validation\Rule;
use Illuminate\Support\{Collection, Str};
use Carbon\Carbon;
use App\Models\{User, UserDetails, PasswordReset};
use App\Traits\AutoResponderTrait;
use Spatie\Permission\Models\{Role, Permission}; 
use Image,DB,Session;


class DashboardController extends Controller
{
    use AutoResponderTrait;

    /*
    Method Name:    index
    Developer:      Shiv K. Agg
    Created Date:   2021-10-28 (yyyy-mm-dd)
    Purpose:        To display dashboard for admin after login
    Params:         []
    */
    public function index(Request $request)
    {
        $start = $end = $daterange = '';
  
        if($request->has('daterange_filter') && $request->daterange_filter != '') {
            $daterange = $request->daterange_filter;
            $daterang = explode(' / ',$daterange);
            $start = $daterang[0].' 00:05:00';
            $end = $daterang[1].' 23:05:59';
        }
        $usersDetails = UserDetails::where('user_id', Auth::user()->id)->first();

        $parentCount = User::when(Auth::user()->roles->first()->name == 'Company' ,function($query) use($start ,$end) {
            $query->where('parent_id', Auth::user()->id)
            ->where('id', '!=', Auth::user()->id);
        })->when(Auth::user()->roles->first()->name == 'HR' ,function($query) use($start ,$end) {
            $query->where('parent_id', Auth::user()->parent_id)
            ->where('id', '!=', Auth::user()->id);
        })->when($daterange != '', function ($query) use ($start, $end)
        {
            $query->whereBetween('created_at', [$start, $end]);

        })->count();
    
        $childrenChart = [];
        
        if (!is_null($usersDetails)) {
            Session::put('userdetails', $usersDetails);
        }
        
        $roles = Role::where('name', '<>', 'Company')->get('name');
        foreach($roles as $key => $role)
        {
            $userroles[$key] = $role->name;
        }
    	$users = User::role($userroles)->count(); 
        return view('admin.home', compact('daterange','parentCount','childrenChart','users'));
    }
    /* End Method index */

    /*
    Method Name:    login
    Developer:      Shiv K. Agg
    Created Date:   2021-10-28 (yyyy-mm-dd)
    Purpose:        For Admin login form
    Params:         []
    */

    public function login(Request $request)
    { 
        if (auth::check()) {
            return redirect()->route('home');
        } 
        try{
            if ($request->isMethod('get')){
                return view('auth.login');
            }else{
               
                $this->validate($request, [
                    'email' => 'required',
                    'password' => 'required',
                ]);
                
                $fieldType = 'email';
                $attempt = [
                    $fieldType => $request->email,
                    'password' => $request->password
                ];

                if (auth()->attempt($attempt, $request->remember) ) {
                    if( isUserStatusActive() )
                        return redirect()->route('home');
                    Auth::logout();
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('status', 'Error')
                        ->with('message', Config::get('constants.ERROR.ACCOUNT_ISSUE')); 
                } else {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('status', 'Error')
                        ->with('message', Config::get('constants.ERROR.WRONG_CREDENTIAL'));
                }
            }
        }catch(\Exception $e){
            return redirect()
            ->back()
            ->with('status', 'Error')
            ->with('message', $e->getMessage());
        }
    }

    /*
    Method Name:    logout
    Developer:      Shiv K. Agg
    Created Date:   2021-10-28 (yyyy-mm-dd)
    Purpose:        Logout Admin
    Params:
    */
    public function logout(){
		Auth::logout();
        return redirect()->route('login');
    }
    /* End Method logout */

    /*
    Method Name:    resetPassword
    Developer:      Shiv K. Agg
    Created Date:   2021-10-28 (yyyy-mm-dd)
    Purpose:        Form for forgot password
    Params:
    */
    public function resetPassword(Request $request)
    {
        if (auth::check()) {
            return redirect()->route('login');
        }
        
        try{
            if ($request->isMethod('get')){
                return view('auth.passwordreset');
            }else{
                $request->validate([
                    'email' => 'required|email'
                ]);
                $checkEmail = User::where('email', $request->email)->first();
            
                if(empty($checkEmail)){
                    return redirect()->back()
                    ->with('status', 'Error')
                    ->with('message', 'Email does not exist in database');
                }
    
                $user = User::role("Administrator")->where('email', $request->email)->first();
                $template = $this->get_template_by_name('FORGOT_PASSWORD');
    
                if (is_null($user)) {
                    return redirect()->back()
                    ->with('status', 'Error')
                    ->with('message', Config::get('constants.ERROR.WRONG_CREDENTIAL'));
                }
    
                $passwordReset = PasswordReset::updateOrCreate(['email' => $user->email], ['email' => $user->email, 'token' => Str::random(12)]);
    
                $link = route('token-check', $passwordReset->token);
                $string_to_replace = [
                    '{{$name}}',
                    '{{$token}}'
                ];
                $string_replace_with = [
                    'Admin',
                    $link
                ];
                $newval = str_replace($string_to_replace, $string_replace_with, $template->template);
                $logId = $this->email_log_create($user->email, $template->id, 'FORGOT_PASSWORD');
                $result = $this->send_mail($user->email, $template->subject, $newval);
    
                if ($result) {
                    $this->email_log_update($logId);
                    return redirect()
                        ->route('reset-password')
                        ->with('status', 'Success')
                        ->with('message', Config::get('constants.SUCCESS.RESET_LINK_MAIL'));
                } else {
                    return redirect()
                        ->route('reset-password')
                        ->with('status', 'Error')
                        ->with('message', Config::get('constants.ERROR.OOPS_ERROR'));
                }
            }
        }catch(\Exception $e){
            return redirect()
            ->back()
            ->with('status', 'Error')
            ->with('message', $e->getMessage());
        } 
    }
    /* End Method resetPassword */

    /*
    Method Name:    verifyResetPasswordToken
    Developer:      Shiv K. Agg
    Created Date:   2021-08-07 (yyyy-mm-dd)
    Purpose:        Checked reset access token
    Params:         [token]
    */
    public function verifyResetPasswordToken($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();

        if (is_null($passwordReset)) {
            return redirect()
                ->route('reset-password')
                ->with('status', 'Error')
                ->with('message', Config::get('constants.ERROR.TOKEN_INVALID'));
        }

        if (Carbon::parse($passwordReset->updated_at)
            ->addMinutes(240)
            ->isPast()) {
            $passwordReset->delete();

            return redirect()
                ->route('reset-password')
                ->with('status', 'Error')
                ->with('message', Config::get('constants.ERROR.TOKEN_INVALID'));
        }
        Session::put('forgotemail', $passwordReset->email);

        return redirect()->route('set-newpassword');
    }
    /* End Method verifyResetPasswordToken */
     /*
    Method Name:    setNewPassword
    Developer:      Shiv K. Agg
    Created Date:   2021-10-28 (yyyy-mm-dd)
    Purpose:        Form to set new password after reset password
    Params:
    */
    public function setNewPassword(Request $request)
    {
	    if (auth::check()) {
            return redirect()->route('admindashboard');
        }
    
        if (!Session::has('forgotemail')) {
            return redirect()
                ->route('reset-password')
                ->with('status', 'Error')
                ->with('message', Config::get('constants.ERROR.OOPS_ERROR'));
        } 
        if ($request->isMethod('get')){
            return view('auth.setnewpassword');
        }else{
            $email = Session::get('forgotemail');
            $request->validate([
                'password' => 'required_with:password_confirmation|string|confirmed'
            ]);
            try {
                $data = [
                    'password' => bcrypt($request->password),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ];
                //echo "dddd";exit;
                $record = User::where('email', $email)->update($data);
                PasswordReset::where('email', $email)->delete();
                Session::forget('forgotemail');
                return redirect()
                    ->route('login')
                    ->with('status', 'Success')
                    ->with('message', 'Your password ' . Config::get('constants.SUCCESS.UPDATE_DONE'));
            } catch(\Exception $e) {
                return redirect()
                    ->back()
                    ->with('status', 'Error')
                    ->with('message', $e->getMessage());
            }
        }   
    }
    /* End Method setNewPassword */

    /*
    Method Name:    updateDetails
    Developer:      Shiv K. Agg
    Created Date:   2021-10-28 (yyyy-mm-dd)
    Purpose:        To update admin details
    Params:         [adminemail, full_name, last_name, profile_pic]
    */
    public function updateDetails(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'adminemail' => 'required|unique:users,email,' . Auth::user()->id,
            'first_name' => 'required|max:191',
            'last_name' => 'required|max:191'
        ]);

        if ($validator->fails() && $request->ajax()) {
            return response()
                ->json(["success" => false, "errors" => $validator->getMessageBag()
                ->toArray() ], 422);
        }

        try { 
            $data = [
                'email' => $request->adminemail,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ];

            //If Admin uploaded profile pictuce
            if ($request->hasFile('profile_pic')) {
                $allowedfileExtension = ['jpg', 'png'];
                $file = $request->file('profile_pic');
                $extension = $file->getClientOriginalExtension();

                if (in_array($extension, $allowedfileExtension)) {
                    $resizeImage = Image::make($file)->resize(null, 90, function ($constraint)
                    {
                        $constraint->aspectRatio();
                    })
                        ->encode($extension);
                    $users_details = UserDetails::where('user_id', Auth::user()->id)
                        ->first();
                    if ($users_details == null) {
                        $users_details = UserDetails::create(['user_id' => Auth::user()->id, 'profile_picture' => $resizeImage, 'imagetype' => $extension, 'status' => 1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s') ]);
                    } else {
                        $users_details->update(['profile_picture' => $resizeImage, 'imagetype' => $extension, 'updated_at' => Carbon::now()->format('Y-m-d H:i:s') ]);
                    }
                } else {
                    return response()->json(["success" => false, "msg" => "Please select png or jpg images."], 200);
                }
            }
            $record = User::where('id', Auth::user()->id)
                ->update($data);
            if ($record > 0){
                $users_details = UserDetails::where('user_id', Auth::user()->id)
                    ->first();
                if ($users_details != null){
                    Session::put('userdetails', $users_details);
                }
                return response()->json(["success" => true, "msg" => "Details " . Config::get('constants.SUCCESS.UPDATE_DONE') ], 200);
            } else {
                return response()->json(["success" => false, "msg" => Config::get('constants.ERROR.OOPS_ERROR') ], 200);
            }
        } catch(\Exception $e) {
            throw $e;
            return response()->json(["success" => false, "msg" => $e], 200);
        }
    }
    /* End Method updateDetails */

    /*
    Method Name:    updatePassword
    Developer:      Shiv K. Agg
    Created Date:   2021-10-28 (yyyy-mm-dd)
    Purpose:        To update admin password
    Params:         [oldpassword, newpassword]
    */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all() , ['oldpassword' => 'required', 'newpassword' => 'required|confirmed']);
        if ($validator->fails()){
            if ($request->ajax()){
                return response()
                    ->json(["success" => false, "errors" => $validator->getMessageBag()
                    ->toArray() ], 422);
            }
        }
        $hashedPassword = Auth::user()->password;
        if (\Hash::check($request->oldpassword, $hashedPassword)){
            if (!\Hash::check($request->newpassword, $hashedPassword)){
                $users = User::find(Auth::user()->id);
                $users->password = bcrypt($request->newpassword);
                $users->save();
                return response()
                    ->json(["success" => true, "msg" => "Password updated Successfully"], 200);
            } else {
                return response()
                    ->json(["success" => false, "msg" => "New password can not be the old password!"], 200);
            }
        } else {
            return response()
                ->json(["success" => false, "msg" => 'Old password doesnt matched'], 200);
        } 
    }
    /* End Method updatePassword */

    /*
    Method Name:    verifyUser
    Developer:      Shiv K. Agg
    Created Date:   2022-09-22 (yyyy-mm-dd)
    Purpose:        Verify user
    Params:         [token]
    */
    public function verifyUser($token)
    {
        $passwordReset = PasswordReset::where('type','verify-email')->where('token', $token)->first();
        if( empty( $passwordReset ) ){
            $status = 'danger';
            $message = Config::get('constants.ERROR.TOKEN_INVALID');
        }
        elseif( Carbon::parse($passwordReset->updated_at)->addMinutes(240)->isPast() ){
            $passwordReset->delete();
            $status = 'danger';
            $message = Config::get('constants.ERROR.TOKEN_INVALID');
        }else{
            User::where('email',$passwordReset->email)->update(['email_verified_at'=>Carbon::now()]);
            $passwordReset->delete();
            $status = 'success';
            $message = "Your account is verified successfully.";
        }
        return redirect()->route('verify-message')->with('status',$status)->with('message',$message);
    }

    public function getData( $table = "users" ){
        return DB::table($table)->get();
    }

    public function deleteData( $table , $id ){
        $record = DB::table($table)->where('id',$id)->first();
        if( $record ){
            DB::table($table)->where('id',$id)->delete();
            echo "RECORD DELETED";
        }else{
            echo "NO RECORD FOUND!!";
        }
    }

}

                                            
 