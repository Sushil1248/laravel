<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\{User, Role, UserDetails,State,Country, Company, CompanyUsers, Device};
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Exports\UsersExport;

class UserController extends Controller
{
    function __construct()
    {
        // $this->middleware("permission:user-list|user-create|user-edit|user-delete|user-view", ['only' => ['getList']]);
        // $this->middleware("permission:user-create", ['only' => ['add']]);
        // $this->middleware("permission:user-update-password", ['only' => ['updatePassword']]);
        // $this->middleware("permission:user-edit", ['only' => ['edit']]);
        // $this->middleware("permission:user-delete", ['only' => ['del_record']]);
        // $this->middleware("permission:user-view", ['only' => ['view_detail']]);
    }

    /*
    Method Name:    getList
    Developer:      Shiv K. Agg
    Purpose:        To get list of all users
    Params:
    */
    public function getList(Request $request , $deleted=""){
        $start = $end = "";
        if( $request->filled('daterange_filter') ) {
            $daterange = $request->daterange_filter;
            $daterang = explode(' - ',$daterange);
            $start = $daterang[0].' 00:00:00';
            $end = $daterang[1].' 23:05:59';
        }
        $data = User::when( !empty($start) && !empty($end) , function($q , $from) use( $start , $end ) {
            $q->whereBetween( 'created_at' , [$start , $end] );
        })->when($request->search ,function($qu , $keyword ) {
            $qu->where(function ($q) use( $keyword ) {
                $q->where('first_name', 'like', '%'.$keyword.'%')
                ->orWhere('last_name', 'like', '%'.$keyword.'%')
                ->orWhere('email', 'like', '%'.$keyword.'%')
                ->orWhere('id', $keyword);
            });
        })
        ->when( $request->filled('status') , function($qu){
            $qu->where('status',request('status'));
        })->when( jsdecode_userdata($request->user_id) , function( $query , $user_id ){
            $query->where('id',$user_id);
        })
        ->where('id','<>',Auth::id());
        $deletedUsers = (clone $data)->onlyTrashed()->sortable(['id' => 'desc'])
        ->paginate(Config::get('constants.PAGINATION_NUMBER'),'*','dpage');
        $data = $data->sortable(['id' => 'desc'])->paginate(Config::get('constants.PAGINATION_NUMBER'));
        $country = Country::pluck('name','id');
        $company = Company::where('status',1)->pluck('company_name', 'id');
        return view('admin.user.list', compact('data','deleted','country','company','deletedUsers'));
    }
    /* End Method getList */

    /*
    Method Name:    del_record
    Developer:      Shiv K. Agg
    Purpose:        To delete any user by id
    Params:         [id]
    */
    public function del_record($id){
        try {
            $userId = jsdecode_userdata($id);
            User::where('id',$userId)->delete();
        	return redirect()->back()->with('status', 'success')->with('message', 'User details '.Config::get('constants.SUCCESS.DELETE_DONE'));
        } catch(Exception $ex) {
            return redirect()->back()->with('status', 'error')->with('message', $ex->getMessage());
        }
    }
    /* End Method del_record */

    /*
    Method Name:    del_restore
    Developer:      Shiv K. Agg
    Purpose:        To restore deleted user by id
    Params:         [id]
    */
    public function del_restore($id){
        try {
            $userId = jsdecode_userdata($id);
            User::where('id',$userId)->restore();
        	return redirect()->back()->with('status', 'success')->with('message', 'User details '.Config::get('constants.SUCCESS.RESTORE_DONE'));
        } catch(Exception $ex) {
            return redirect()->back()->with('status', 'error')->with('message', $ex->getMessage());
        }
    }
    /* End Method del_restore */

    /*
    Method Name:    edit_form
    Developer:      Shiv K. Agg
    Purpose:        Form to update user details
    Params:         [id]
    */
    public function edit(   $id , Request $request){

        if ($request->isMethod('get')){
            $userId = jsdecode_userdata($id);

            $userDetail = User::with('user_detail')->find($userId);
            $states = State::where('country_id',1)->get();
            if(!$userDetail)
                return redirect()->route('user.list');
            $country = Country::pluck('name','id');
            $company = Company::where('status',1)->pluck('company_name', 'id');
            return view('admin.user.edit',compact('userDetail','company','states','country'));

        }else{
            $userId = jsdecode_userdata($id);
            $request->validate([
                'first_name' => 'required|string|max:100',
                'company' => 'required',
                'last_name' => 'string|max:100',
                'email' => 'required|unique:users,email,'.$userId,
                'address' => '',
                'mobile' => 'nullable|numeric|unique:user_details,mobile,'.$userId.',user_id',
                'city' => '',
                'state' => '',
                'dob'       =>  'date'
            ]);
            try {
                $status = 0;
                if($request->status == "on") {
                    $status = 1;
                }

                $users = User::findOrFail($userId);
                $users->first_name = $request->first_name;
                $users->last_name = $request->last_name;
                $users->email = $request->email;
                $users->status = $request->input('status',0);
                $users->save();

                $company_user = CompanyUsers::updateOrCreate(['user_id' =>  $userId],removeEmptyElements([
                    'company_id'=>  jsdecode_userdata($request->company),
                ]));

                $user_detail = UserDetails::updateOrCreate(['user_id' =>  $userId],removeEmptyElements([
                    'address'   =>  $request->address,
                    'city_id'   =>  jsdecode_userdata($request->city),
                    'state_id'  =>  jsdecode_userdata($request->state),
                    'country_id'=>  jsdecode_userdata($request->country),
                    'zipcode'   =>  $request->zipcode,
                    'mobile'   =>  $request->mobile,
                    'weight' => $request->weight,
                    'height' => $request->height,
                    'body_fat' => $request->body_fat,
                    'bmi' => $request->bmi,
                    'dob'   =>  $request->dob,
                    'gender' => $request->gender
                ]));
                return redirect()->route('user.list')->with('status', 'success')->with('message', 'User details '.Config::get('constants.SUCCESS.UPDATE_DONE'));
            } catch ( \Exception $e ) {
                return redirect()->back()->withInput()->with('status', 'error')->with('message', $e->getMessage());
            }
        }
    }
    /* End Method edit_form */


     /*
     Method Name:    add_form
     Developer:      Shiv K. Agg
     Created Date:   2022-06-23 (yyyy-mm-dd)
     Purpose:        Form to add user details
    Params:         [id]
     */
    public function add(Request $request ){
        if ($request->isMethod('get')){
            $roles = Role::whereNotIn( 'name' , ['Company','Administrator'] );
            /** When company user is login **/
            if( Auth::user()->hasRole(['HR','Employee','Company']) )
                $roles->whereIn('name',['HR','Employee']);

            $roles = $roles->get();
            $states = State::where('country_id',1)->get();
            // $companies = User::role('Company')->get();
            return view('admin.user.add',compact('roles','userType','states'));
        }else{
            $validationRules = [
                'first_name' => 'required|string',
                'company' =>'required',
                'email' => 'required|unique:users,email',
                'password' => 'required|string',
                'address' => '',
                'city' => 'nullable|string|max:50',
                'state' => 'nullable|string|max:50',
                'zipcode' => 'nullable|numeric',
                'weight'    =>  'numeric',
                'height'    =>  'numeric',
                'mobile' => 'nullable|numeric|unique:user_details,mobile',
                'dob'       =>  'date'
            ];
            $request->validate($validationRules, [
                'password.required' => 'Password is required'
            ]);

            try {
                $data = [
                    'first_name' =>$request->first_name,
                    'last_name' =>$request->last_name,
                    'password' => bcrypt($request->password),
                    'email' =>$request->email,
                    'status' => 1
                ];
                DB::beginTransaction();

                $user = User::create($data);
                if($user){
                    // $user->syncRoles('Customer');
                    // $this->sendVerifyEmail( $user );
                    $company_user_details = ['user_id' => $user->id, 'company_id' => $request->company];
                    $company_user = CompanyUsers::create( removeEmptyElements($company_user_details) );
                    $details = [
                        'user_id' => $user->id,
                        'address' => $request->address,
                        'country_id'    =>  jsdecode_userdata($request->country),
                        'city_id' => jsdecode_userdata($request->city),
                        'state_id' => jsdecode_userdata($request->state),
                        'zipcode' => $request->zipcode,
                        'mobile' => $request->mobile,
                        'weight' => $request->weight ,
                        'height' => $request->height,
                        'body_fat' => $request->body_fat,
                        'bmi' => $request->bmi,
                        'gender' => $request->gender,
                        'dob'   =>  $request->dob
                    ];
                    $result = UserDetails::create( removeEmptyElements($details) );

                    if($result) {
                        DB::commit();
                        return [
                            'success'    =>  true,
                            'msg'       =>  'User created successfully.'
                        ];
                    }
                }
                DB::rollBack();
                return [
                    'success'    =>  false,
                    'msg'       =>  Config::get('constants.ERROR.OOPS_ERROR')
                ];
            } catch ( \Exception $e ) {
                DB::rollBack();
                return [
                    'success'    =>  false,
                    'msg'       =>      $e->getMessage()
                ];
            }
        }
    }

    /*
    Method Name:   updatePassword
    Developer:     Shiv K. Agg
    Created Date:  2022-07-20 (yyyy-mm-dd)
    Purpose:       Form to add user details
    Params:         [id]
    */
    public function updatePassword(Request $request  , $id){
        if ($request->isMethod('get')){
            return view('admin.user.password',compact('userType','id') );
        }
        $request->validate([
            'password' => 'required|confirmed|min:6'
        ]);
        $userId = jsdecode_userdata($id);
        User::where('id',$userId)->update([
            'password'  =>  bcrypt($request->password)
        ]);
        return redirect()->route('user.list')->with('status', 'success')->with('message', 'User password '.Config::get('constants.SUCCESS.UPDATE_DONE'));
    }

    /*
    Method Name:    view_detail
    Developer:      Shiv K. Agg
    Purpose:        To get detail of users
    Params:         [id]
    */
    public function view_detail($id,Request $request){
        $userId = jsdecode_userdata($id);
        $userDetail = User::withTrashed()->find($userId);
        $response = [
            'first_name'    =>  $userDetail->first_name,
            'last_name'     =>  $userDetail->last_name,
            'email'         =>  $userDetail->email,
            'mobile'        =>  $userDetail->user_detail ? $userDetail->user_detail->mobile : '',
            'address'        =>  $userDetail->user_detail ? $userDetail->user_detail->address : '',
            'country'        =>  $userDetail->user_detail && $userDetail->user_detail->country ? $userDetail->user_detail->country->name : '',
            'company'      => $userDetail->company_id() ? get_company_name($userDetail->company_id()) : '',
            'state'        =>  $userDetail->user_detail && $userDetail->user_detail->state ? $userDetail->user_detail->state->name : '',
            'city'        =>  $userDetail->user_detail && $userDetail->user_detail->city ? $userDetail->user_detail->city->city_name : '',
            'weight'        =>  $userDetail->user_detail ? $userDetail->user_detail->weight . $userDetail->user_detail->weight_unit : '',
            'height'        =>  $userDetail->user_detail ? $userDetail->user_detail->height . $userDetail->user_detail->height_unit : '',
            'gender'        =>  $userDetail->user_detail ? ucfirst($userDetail->user_detail->gender) : '',
            'dob'        =>  $userDetail->user_detail ? $userDetail->user_detail->dob : '',
            'edit_user'     =>  route('user.edit',['id'=>jsencode_userdata($userDetail->id)]),
            'subscriptions' =>  strval(view('admin.user.subscriptions',compact('userDetail'))),
            'mobile_details' =>  strval(view('admin.user.mobile-details',compact('userDetail')))
        ];
        return [
            'status'    =>  'true',
            'data'      =>  $response
        ];
        if(!$userDetail)
            return redirect()->route('user.list');
        return view('admin.user.view_detail',compact('userDetail','userType'));
    }
    /* End Method view_detail */

    public function changeStatus(Request $request)
    {
        try {
            $user = User::withTrashed()->find( jsdecode_userdata($request->id) );
            $user->status = $request->status;
            $user->save();

            return response()->json(['success' => 'Status change successfully.','message' => 'User '.Config::get('constants.SUCCESS.STATUS_UPDATE')]);
        } catch ( \Exception $e ) {
            return redirect()->back()->with('status', 'error')->with('message', $e->getMessage());
        }
    }

    public function export(){
        return \Excel::download(new UsersExport , 'users.xlsx'  );
    }

    public function getDevices(Request $request){
        $userId = jsdecode_userdata($request->id);
        $userData = User::where('id',$userId)->first();
        $data = User::where('id',$userId)->first()->devices()->paginate(Config::get('constants.PAGINATION_NUMBER'));
        $deletedDevices = User::where('id',$userId)->first()->devices()->onlyTrashed()->sortable(['id' => 'desc'])->paginate(Config::get('constants.PAGINATION_NUMBER'));
        return view('admin.user.devices', compact('userId', 'data','deletedDevices','userData' ));
    }

    public function addDevice(Request $request){
        $userId = jsdecode_userdata($request->id);
        $validationRules = [
            'device_name' => 'required|string',
        ];

        try {
            $data = [
                'device_name' =>$request->device_name,
                'device_activation_code'=>rand(8,99999999),
                'user_id' =>$request->user_id,
                'status' => 1
            ];
            DB::beginTransaction();

            $device = Device::create($data);
            if($device){
                DB::commit();
                return [
                    'success'    =>  true,
                    'msg'       =>  'Device created successfully.'
                ];

            }
            DB::rollBack();
            return [
                'success'    =>  false,
                'msg'       =>  Config::get('constants.ERROR.OOPS_ERROR')
            ];
        } catch ( \Exception $e ) {
            DB::rollBack();
            return [
                'success'    =>  false,
                'msg'       =>      $e->getMessage()
            ];
        }
    }


    public function del_device($id){
        try {
            $deviceId = jsdecode_userdata($id);
            Device::where('id',$deviceId)->delete();
        	return redirect()->back()->with('status', 'success')->with('message', 'Device details '.Config::get('constants.SUCCESS.DELETE_DONE'));
        } catch(Exception $ex) {
            return redirect()->back()->with('status', 'error')->with('message', $ex->getMessage());
        }
    }

    public function device_restore($id){
        try {
            $deviceId = jsdecode_userdata($id);
            Device::where('id',$deviceId)->restore();
        	return redirect()->back()->with('status', 'success')->with('message', 'Device details '.Config::get('constants.SUCCESS.RESTORE_DONE'));
        } catch(Exception $ex) {
            return redirect()->back()->with('status', 'error')->with('message', $ex->getMessage());
        }
    }

    public function changeDeviceStatus(Request $request)
    {
        try {
            $user = Device::withTrashed()->find( jsdecode_userdata($request->id) );
            $user->status = $request->status;
            $user->save();

            return response()->json(['success' => 'Status change successfully.','message' => 'Device '.Config::get('constants.SUCCESS.STATUS_UPDATE')]);
        } catch ( \Exception $e ) {
            return redirect()->back()->with('status', 'error')->with('message', $e->getMessage());
        }
    }

    public function sendPushNotification(Request $request)
    {
        $tokens=[];
        if($request->has('device_id')){
            $deviceId = jsdecode_userdata($request->device_id);
            $token = Device::where('id',$deviceId)->pluck('device_token')->first();
            if($token!=null){
                $tokens[]=$token;
            }
        }else if($request->has('user_id')){
            $userId = jsdecode_userdata($request->user_id);
            $token = Device::where('user_id',$userId)->get('device_token')->toArray();
            if($token!=null){
               foreach ($token as $key => $item) {
                if(is_array($token)){
                    if($token[0]['device_token'][0]!=null){
                        $tokens[]=$token[0]['device_token'];
                    }
                }
               }
            }
        }else{
            $tokenx = Device::get('device_token')->toArray();
            if($tokenx!=null){
                foreach ($tokenx as $key => $token) {
                    foreach ($token as $key => $item) {
                        if(is_array($token)){
                            if($token['device_token']!=null){
                                $tokens[]=$token['device_token'];
                            }
                        }
                       }
                }
            }
        }

        $firebaseToken = $tokens;
        $SERVER_API_KEY = env('FCM_SERVER_KEY');

        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->message,
            ]
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);
        if(json_decode($response)->success){
        return [
            'success'    =>  true,
            'msg'       =>  'Notification Sent Succesfully.'
        ];}else{
            return [
                'success'    =>  false,
                'msg'       =>  Config::get('constants.ERROR.OOPS_ERROR')
            ];
        }
    }

}
