<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, Hash, Auth, mail, Password,Config};
use App\Models\{User,PasswordReset,UserDetails,State,Country,GymEquipment,SubscriptionPlan,Company, Device};
use App\Traits\AutoResponderTrait;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DB, Session;
use Spatie\Permission\Models\{Role, Permission};


class HomeController extends Controller
{
    use AutoResponderTrait;

    public function index( Request $request )
    {
        if(!Auth::user()->hasRole('Administrator')){
            return redirect()->route('company_home');
        }
        $start = $end = "";
        if( $request->filled('daterange_filter') ) {
            $daterange = $request->daterange_filter;
            $daterang = explode(' - ',$daterange);
            $start = $daterang[0].' 00:05:00';
            $end = $daterang[1].' 23:05:59';
        }
        $totalUsers = User::whereHas('roles', function ($query) {
            $query->where('name', '=', '1_User');
        })->where('id','<>',Auth::id())->when($start && $end ,function($query, $role) use ($start , $end) {
           $query->whereBetween( 'created_at' , [$start , $end] );
        } )->count();

        $activeUsers = User::whereHas('roles', function ($query) {
            $query->where('name', '=', '1_User');
        })->where('id','<>',Auth::id())->when($start && $end ,function($query, $role) use ($start , $end) {
           $query->whereBetween( 'created_at' , [$start , $end] );
        } )->active()->count();

        $activeCompanies = User::whereHas('roles', function ($query) {
            $query->where('name', '=', '1_Company');
        })->where('id','<>',Auth::id())->when($start && $end ,function($query, $role) use ($start , $end) {
           $query->whereBetween( 'created_at' , [$start , $end] );
        } )->active()->count();

        $activeDevices = Device::when($start && $end ,function($query, $role) use ($start , $end) {
            $query->whereBetween( 'created_at' , [$start , $end] );
        } )->active()->count();

        $totalCompanies = User::whereHas('roles', function ($query) {
            $query->where('name', '=', '1_Company');
        })->when($start && $end ,function($query, $role) use ($start , $end) {
           $query->whereBetween( 'created_at' , [$start , $end] );
        } )->count();

        $totalPlans = SubscriptionPlan::when($start && $end ,function($query) use ($start , $end) {
            $query->whereBetween( 'created_at' , [$start , $end] );
        } )->count();
        $activePlans = SubscriptionPlan::active()->when($start && $end ,function($query) use ($start , $end) {
            $query->whereBetween( 'created_at' , [$start , $end] );
        } )->count();
        // $recentUsers = User::active()->role('Customer')->latest()->get();

        $recentCompanies = Company::active()->latest()->get();

        return view('admin.home', compact('totalUsers','totalPlans','activeUsers','activeDevices','activePlans', 'recentCompanies','totalCompanies','activeCompanies') );
    }

    public function company_index( Request $request )
    {
        // $role = Role::find(3);
        // $permission = Permission::findByName('user-add');
        // $role->givePermissionTo($permission);

        $start = $end = "";
        if( $request->filled('daterange_filter') ) {
            $daterange = $request->daterange_filter;
            $daterang = explode(' - ',$daterange);
            $start = $daterang[0].' 00:05:00';
            $end = $daterang[1].' 23:05:59';
        }
        $companyId = Auth::user()->id;

        $totalUsers = User::whereHas('companyUsers', function ($query) use ($companyId) {
                            $query->where('company_id', $companyId);
                        })->where('id','<>',Auth::id())->when($start && $end ,function($query, $role) use ($start , $end) {
                            $query->whereBetween( 'created_at' , [$start , $end] );
                        } )->count();

        $activeUsers =  User::whereHas('companyUsers', function ($query) use ($companyId) {
                            $query->where('company_id', $companyId);
                        })->where('id','<>',Auth::id())->when($start && $end ,function($query, $role) use ($start , $end) {
                            $query->whereBetween( 'created_at' , [$start , $end] );
                        } )->active()->count();

        return view('company.home', compact('totalUsers','activeUsers') );
   }

    public function addUserSubscription( User $user ){

        return view('add-user-subscription', [
            'intent' => $user->createSetupIntent(),
            'user'      =>  $user,
            'plans'     =>  SubscriptionPlan::active()->get()
        ]);
    }

    public function assignSubscription(User $user , Request $request){
        try {
            $user->createOrGetStripeCustomer();
            $user->syncStripeCustomerDetails();
            $user->addPaymentMethod($request->payment_method);
            $plan = SubscriptionPlan::find( $request->plan );
            $user->newSubscription(
                $plan->name, $plan->price_id
            )->create($request->payment_method);
            return "Added Subscription";
        } catch ( \Exception $e ) {
            return "ERROR - " . $e->getMessage();

        }
        return $request->all();
    }

    public function about()
    {
        return view('frontend.about');
    }
    public function contact()
    {
        return view('frontend.contact');
    }
    public function apps()
    {
        return view('frontend.apps');
    }
    public function preorder()
    {
        return view('frontend.preorder');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|alpha',
            'last_name' => 'required|alpha',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message"   => $validator->errors()
            ]);
        }
        try {

            $password = bcrypt($request->password);
            $data = [
                'first_name' =>$request->first_name,
                'last_name' =>$request->last_name,
                'password' => $password,
                'email' =>$request->email,
            ];
            $user = User::create($data);
            $user->assignRole('1_User');

            if ($user) {
                UserDetails::create(['user_id' => $user->id]);

                return response()->json([
                    "success" => true,
                    "message"   => "User registered successfully"
                ]);
            } else {
                $response['success'] = false;
                $response['message'] = "Something went wrong";
            }

            return response()->json([
                "success" => false,
                "message"   => "Something went wrong"
            ]);
        } catch ( \Exception $e ) {
           return response()->json([
                "success" => false,
                "message"   => $e->getMessage()
            ]);
        }
    }
    public function loginView(Request $request)
    {
        if (auth::check()) {

            return redirect()->route('home');
        } else {
            session()->forget('email');
            return view('frontend.login');
        }
    }
    /**public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
        ]);
        $user = User::where('email', $email)->first();
        if($user){
            $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $attempt = [
                $fieldType => $request->email,
                'password' => $request->password
            ];

            if ( auth()->attempt($attempt,$request->has('remember')) && auth()->user()->hasRole('1_User')) {
                User::where('email',$request->email)->update(['remember_token' =>$request->has('remember')]);

                return redirect()->route('home');
            } else {

                Auth::logout();
                return redirect()
                    ->route('login-view')
                    ->with('status', 'Error')
                    ->with('message', Config::get('constants.ERROR.WRONG_CREDENTIAL'));
            }
        }else{
            return redirect()
                    ->route('login-view')
                    ->with('status', 'Error')
                    ->with('message', "User doesnot exist ");
        }
    } */

    public function resetPasswordView()
    {
        if (auth::check()) {
            return redirect()->route('home');
        }

        return view('frontend.forgot-password');
    }

    public function login(Request $request)
    {
        //validate incoming request
        $messages = [
			'email.required' => 'We need to know your email address',
			'email.email' => 'Provide a an valid email address',
			'password.required' => 'You can not left password empty.',
			'password.string' => 'password field must be a string.'
		];
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message"   => $validator->errors()->all()
            ]);
        }

        try {
            $email = $request->email;
            $user = User::where('email', $email)->first();
            if($user){
                $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
                $attempt = [
                    $fieldType => $request->email,
                    'password' => $request->password
                ];
                if(!auth()->attempt($attempt)){
                    return response()->json([
                        "success" => false,
                        "message"   => "Invalid credentials"
                    ]);
                }
                $role_name = Auth::user()->getRoleNames()->first();
                if($role_name == '1_User') {
                    return response()
                        ->json([
                            "success" => true,
                            "redirect_to"   =>  route('home'),
                            "message"       =>  "Login successfully"
                        ]);

                }else{
                    Auth::logout();
                    return response()
                    ->json([
                        "success" => false,
                        "redirect_to"   =>  route('home'),
                        "message"       =>  "Invalid user"
                    ]);
                }
            }else{
                return response()->json([
                    "success" => false,
                    "message"   => "User doesnot exist"
                ]);
            }
        } catch(\Exception $e) {
            return response()
            ->json([
                "success" => false,
                "message"       =>  $e->getMessage()
            ]);
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }

    public function logout(){
		Auth::logout();
        return redirect()->to('/');
    }

    public function resetPasswordLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message"   => $validator->errors()
            ]);
        }
        $user = User::where('email', $request->email)->first();

        $template = $this->get_template_by_name('FORGOT_PASSWORD');

        if (is_null($user)) {
            return response()->json([
                "success" => false,
                "message"   => "Please provide valid email"
            ]);
        }

        $passwordReset = PasswordReset::updateOrCreate(['email' => $user->email], ['email' => $user->email, 'token' => Str::random(12)]);

        $link = route('tokencheck', $passwordReset->token);

        $string_to_replace = [
            '{{$name}}',
            '{{$token}}'
        ];
        $string_replace_with = [
            $user->name,
            $link
        ];
        $newval = str_replace($string_to_replace, $string_replace_with, $template->template);

        $logId = $this->email_log_create($user->email, $template->id, 'FORGOT_PASSWORD');

        $result = $this->send_mail($user->email, $template->subject, $newval);

        if ($result) {
            $this->email_log_update($logId);
            return response()->json([
                "success" => true,
                "message"   => "We have sent you an email with password reset link."
            ]);

        } else {
            return response()->json([
                "success" => false,
                "message"   => "Oops!! Something went wrong or your session has been expired."
            ]);
        }
    }
    public function popup(){
        return view('frontend.popup');
    }
    public function verifyResetPasswordToken($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();

        if (is_null($passwordReset)) {
            return redirect()->route('popup');
        }

        if (Carbon::parse($passwordReset->created_at)
            ->addMinutes(240)
            ->isPast()) {
            $passwordReset->delete();

            return redirect()->route('popup');
        }
        Session::put('forgotemail', $passwordReset->email);

        return redirect()->route('setnewpassword');
    }

    public function setNewPassword()
    {
	    if (auth::check()) {
            return redirect()->route('/');
        }

        if (Session::has('forgotemail') || Session::has('message') ) {
            return view('frontend.update-password');
        } else {
            return redirect()
                ->route('setnewpassword')
                ->with('status', 'Error')
                ->with('message', "Oops!! Something went wrong or your session has been expired");
            // return response()->json([
            //     "success" => false,
            //     "message"   => "Oops!! Something went wrong or your session has been expired"
            // ]);
        }
    }

    public function updateNewPassword(Request $request)
    {

        if (!Session::has('forgotemail')) {
            return redirect()
                ->route('setnewpassword')
                ->with('status', 'Error')
                ->with('message', "Oops!! Something went wrong or your session has been expired");
        }

        $email = Session::get('forgotemail');
        $request->validate([
            'password' => 'required_with:password_confirmation|string|confirmed'
        ]);
        try {
            $data = [
                'password' => bcrypt($request->password),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ];
            $record = User::where('email', $email)->update($data);
            PasswordReset::where('email', $email)->delete();
            //dd("adsf");
            Session::forget('forgotemail');
            return redirect()
                ->route('setnewpassword')
                ->with('status', 'Success')
                ->with('message', "Your password has been updated successfully");
        } catch(\Exception $e) {
            return redirect()
                ->route('setnewpassword')
                ->with('status', 'Error')
                ->with('message', $e->getMessage());

        }

    }

    public function contactUs(Request $request)
     {
             $messages = [
                 'name.required' => 'The name field is required',
                 'name.string' => 'The name must be a string.',
                 'email.required' => 'The email field is required',
                 'email.email' => 'Provide an valid email address',
                 'mobile.required' => 'Mobile number field is required',
                 'mobile.numeric' => 'Mobile number should contain only numbers',
                 'subject.required' => 'Subject field is required',
                 'feature.required' => 'Feature field is required',
             ];
             $rules = [
                 'name' => 'required|string',
                 'email' => 'required|email',
                 'mobile' => 'required|numeric|digits_between:10,12',
                 'subject' => 'required',
                 'feature' => 'required',
             ];
             $request->validate($rules, $messages);
             try
             {
                 if(auth::check()){
                     $data['user_id'] = Auth::user()->id;
                 }
                 $logtoken = Str::random(12);
                 $template = $this->get_template_by_name('CONTACT_INFO');
                 $string_to_replace = array('{{$name}}', '{{$username}}', '{{$email}}', '{{$mobile}}', '{{$subject}}', '{{$feature}}','{{$logToken}}');
                 $string_replace_with = array('Admin', $request->name, $request->email, $request->mobile, $request->subject, $request->feature, $logtoken);
                 $newval = str_replace($string_to_replace, $string_replace_with, $template->template);
                 $to_email = Config::get('constants.ADMIN_EMAIL');
                 $logId = $this->email_log_create($to_email , $template->id, 'CONTACT_INFO', $logtoken);
                 $result = $this->send_mail($to_email, $template->subject, $newval);
                 if($result){
                     $this->email_log_update($logId);
                 }
                 return redirect()->back()->with('status', 'success')->with('message', Config::get('constants.SUCCESS.CONTACT_DONE'));

             } catch ( \Exception $e ) {

                 return redirect()->back()->with('status', 'error')->with('message', $e->getMessage());
             }

     }


    public function getCountryStates( $countryId ){
        $response = [];
        $countryId = jsdecode_userdata($countryId);
        $country = Country::find( $countryId );
        $states = [];
        if( $country ){
            $countryStates = $country->states()->pluck('name','id');
            foreach( $countryStates as $stateId => $stateName )
                $states[jsencode_userdata($stateId)] = $stateName;
        }
        return [
            'status'    =>  true,
            'data'      =>  $states
        ];
    }

    public function getStateCities( $stateId ){
        $response = [];
        $stateId = jsdecode_userdata($stateId);
        $state = State::find( $stateId );
        $cities = [];
        if( $state ){
            $stateCities = $state->cities->pluck('city_name','id');
            foreach( $stateCities as $cityId => $cityName )
                $cities[jsencode_userdata($cityId)] = $cityName;
        }
        return [
            'status'    =>  true,
            'data'      =>  $cities
        ];
    }

    public function getExercises( Request $request ){
        $records_per_request = 10;
        $exercises = Exercise::where('name','like',"%{$request->term}%")->select(['id','name as text']);
        if( jsdecode_userdata($request->ignore_exercise) )
            $exercises->where('id','!=',jsdecode_userdata($request->ignore_exercise) );
        $exercises = $exercises->simplePaginate( $records_per_request );
        $data = $exercises->items();
        $new_data = [];
        foreach( $data as $singleExercise ){
            $new_data[] = [
                'id'    =>  jsencode_userdata($singleExercise->id),
                'text'  =>  $singleExercise->text
            ];

        }
        return [
            'results'   =>  $new_data,
            'pagination'    =>  [
                'more'  =>  $exercises->hasMorePages()
            ]
        ];
    }
}
