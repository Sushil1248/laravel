<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;

use App\Models\Company;

use App\Models\CompanyUsers;
use App\Models\Country;
use App\Models\Device;
use App\Models\{Role, UserVehicles};
use App\Models\State;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\UserDetails;use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Str;



class UserController extends Controller
{
    public function __construct()
    {
        // $this->middleware("permission:user-list|user-create|user-edit|user-delete|user-view", ['only' => ['getList']]);
        // $this->middleware("permission:user-create", ['only' => ['add']]);
        // $this->middleware("permission:user-update-password", ['only' => ['updatePassword']]);
        // $this->middleware("permission:user-edit", ['only' => ['edit']]);
        // $this->middleware("permission:user-delete", ['only' => ['del_record']]);
        // $this->middleware("permission:user-view", ['only' => ['view_detail']]);
    }

    public function getList(Request $request, $deleted = "")
    {
         if(! auth()->user()->hasRole('Administrator')){
            return redirect()->back();
        }
        $start = $end = "";
        if ($request->filled('daterange_filter')) {
            $daterange = $request->daterange_filter;
            $daterang = explode(' - ', $daterange);
            $start = $daterang[0] . ' 00:00:00';
            $end = $daterang[1] . ' 23:05:59';
        }

        $data = User::whereHas('roles', function ($query) {
            $query->where('name', '!=', '1_Company');
        })
        ->when(!empty($start) && !empty($end), function ($q, $from) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        })
        ->when($request->search, function ($qu, $keyword) {
           $qu->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', '%' . $keyword . '%')
                    ->orWhere('last_name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%')
                    ->orWhere('id', $keyword);
            });
        })
            ->when($request->filled('status'), function ($qu) {
                $qu->where('status', request('status'));
            })->when(jsdecode_userdata($request->user_id), function ($query, $user_id) {
            $query->where('id', $user_id);
        })
            ->where('id', '<>', Auth::id())
              ->where('id', '<>', 1);
        $deletedUsers = (clone $data)->onlyTrashed()->sortable(['id' => 'desc'])
            ->paginate(Config::get('constants.PAGINATION_NUMBER'), '*', 'dpage');
        $data = $data->sortable(['id' => 'desc'])->paginate(Config::get('constants.PAGINATION_NUMBER'));
        $role = Role::where('created_by', Auth::user()->id)->pluck('name', 'id');

        $country = Country::pluck('name', 'id');
        $vehicles=[];
        $vehicles = Vehicle::all();
        if(auth()->user()->hasRole(Auth::user()->id.'_Company')){
            $company = User::where('id', Auth()->user()->id)
                    ->with('company_detail:id,user_id,company_name')
                    ->get()
                    ->pluck('company_detail.company_name', 'id');
        }else{
            $company = Role::findByName('1_Company')
                        ->users()
                        ->active()
                        ->with('company_detail:id,user_id,company_name')
                        ->get()
                        ->pluck('company_detail.company_name', 'company_detail.user_id');
        }
        $vehicle_assigned = UserVehicles::all();

       return view('admin.user.list', compact('data', 'deleted', 'country', 'company', 'deletedUsers', 'vehicles','role'));
    }
    /* End Method getList */

    /*
    Method Name:    del_record
    Developer:      Shiv K. Agg
    Purpose:        To delete any user by id
    Params:         [id]
     */
    public function del_record($id)
    {
        try {
            $userId = jsdecode_userdata($id);


            $user_delete  = User::where('id', $userId)->delete();
            if($user_delete){

            }
            return redirect()->back()->with('status', 'success')->with('message', 'User details ' . Config::get('constants.SUCCESS.DELETE_DONE'));
        } catch (Exception $ex) {
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
    public function del_restore($id)
    {
        try {
            $userId = jsdecode_userdata($id);
            User::where('id', $userId)->restore();
            return redirect()->back()->with('status', 'success')->with('message', 'User details ' . Config::get('constants.SUCCESS.RESTORE_DONE'));
        } catch (Exception $ex) {
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
    public function edit($id, Request $request)
    {

        if ($request->isMethod('get')) {
            $userId = jsdecode_userdata($id);
            $user_ids = [];
            $companyId = Auth::user()->id;
            $user_ids = User::whereHas('companyUsers', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('id', '<>', Auth::id())->get()->pluck('id')->toArray();

            $role = Auth::user()->getRoleNames()->first();

            if (stripos(strtolower($role), 'company') == false) {
                $companyIds = Auth::user()->companyUsers()->pluck('company_id')->first();
                array_push($user_ids, $companyIds);
            }

            $role = Role::where('created_by', Auth::user()->id)
                    ->orWhereIn('created_by', $user_ids)
                    ->pluck('name', 'id');
            $userDetail = User::with('user_detail')->find($userId);
            $states = State::where('country_id', 1)->get();
            if (!$userDetail) {
                return redirect()->route('user.list');
            }

            $country = Country::pluck('name', 'id');
            if(!auth()->user()->hasRole('Administrator')){
                $company = User::where('id', Auth()->user()->id)
                        ->with('company_detail:id,user_id,company_name')
                        ->get()
                        ->pluck('company_detail.company_name', 'id');
            }else{
                $company = Role::findByName(Auth::user()->id.'_Company')
                            ->users()
                            ->active()
                            ->with('company_detail:id,user_id,company_name')
                            ->get()
                            ->pluck('company_detail.company_name', 'company_detail.user_id');
            }
           return view('admin.user.edit', compact('userDetail', 'company', 'states', 'country','role'));

        } else {
            $userId = jsdecode_userdata($id);
            $request->validate([
                'first_name' => 'required|string|max:100',
                'role'       =>'required',
                'company' => 'required',
                'last_name' => 'string|max:100',
                'email' => 'required|unique:users,email,' . $userId,
                'address' => '',
                'mobile' => 'nullable|numeric|unique:user_details,mobile,' . $userId . ',user_id',
                'city' => '',
                'state' => '',
                'dob' => 'date',
            ]);
            try {
                $status = 0;
                if ($request->status == "on") {
                    $status = 1;
                }
                $web_access = 0;
                if ($request->web_access == "on") {
                    $web_access = 1;
                }

                $users = User::findOrFail($userId);
                $users->roles()->detach();
                $users->first_name = $request->first_name;
                $users->last_name = $request->last_name;
                $users->email = $request->email;
                $users->status = $request->input('status', 0);
                $users->web_access = $web_access;
                if($request->has('role')){
                    $role = Role::findByName($request->role);
                    $users->assignRole($role);
                }else{
                    $users->assignRole('1_User');
                }
                $users->save();

                $company_user = CompanyUsers::updateOrCreate(['user_id' => $userId], removeEmptyElements([
                    'company_id' => jsdecode_userdata($request->company),
                ]));

                $user_detail = UserDetails::updateOrCreate(['user_id' => $userId], removeEmptyElements([
                    'address' => $request->address,
                    'city_id' => jsdecode_userdata($request->city),
                    'state_id' => jsdecode_userdata($request->state),
                    'country_id' => jsdecode_userdata($request->country),
                    'zipcode' => $request->zipcode,
                    'mobile' => $request->mobile,
                    'weight' => $request->weight,
                    'height' => $request->height,
                    'body_fat' => $request->body_fat,
                    'bmi' => $request->bmi,
                    'dob' => $request->dob,
                    'gender' => $request->gender,
                ]));
                return redirect()->back()->with('status', 'success')->with('message', 'User details ' . Config::get('constants.SUCCESS.UPDATE_DONE'));
            } catch (\Exception$e) {
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
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            $roles = Role::whereNotIn('name', ['Company', 'Administrator']);
            /** When company user is login **/
            if (Auth::user()->hasRole(['HR', 'Employee', 'Company'])) {
                $roles->whereIn('name', ['HR', 'Employee']);
            }

            $roles = $roles->get();
            $states = State::where('country_id', 1)->get();
            // $companies = User::role('Company')->get();
            return view('admin.user.add', compact('roles', 'userType', 'states'));
        } else {
            $validationRules = [
                'first_name' => 'required|string',
                'company' => 'required',
                'email' => 'required|unique:users,email',
                'password' => 'required|string',
                'address' => '',
                'city' => 'nullable|string|max:50',
                'state' => 'nullable|string|max:50',
                'zipcode' => 'nullable|numeric',
                'weight' => 'numeric',
                'height' => 'numeric',
                'mobile' => 'nullable|numeric|unique:user_details,mobile',
                'dob' => 'date',
            ];
            $request->validate($validationRules, [
                'password.required' => 'Password is required',
            ]);

            try {
                $web_access=0;
                if($request->has('web_access')){
                    $web_access=1;
                }
                $random_unique_id =Str::random(6);
                $data = [
                    'first_name' => $request->first_name,
                    'unique_id' => $random_unique_id,
                    'last_name' => $request->last_name,
                    'password' => bcrypt($request->password),
                    'email' => $request->email,
                    'status' => 1,
                    'web_access'=>$web_access
                ];
                DB::beginTransaction();

                $user = User::create($data);

                activity()
                ->performedOn($user)
                ->withProperties(['attributes' => $user->toArray()])
                ->log('User created');

                if($request->has('role')){
                    $role = Role::findByName($request->role);
                    $user->assignRole($role);
                }else{
                    $user->assignRole('1_User');
                }
                if ($user) {
                    $role = strtolower(trim_role_name($request->role));
                    if($role == "driver"){
                        $first_name = strtoupper(substr($request->first_name, 0, 3));
                        $dev_data = [

                            'device_name' => $first_name.'-'.$random_unique_id.'-'.date('Ym'),
                            'device_activation_code' => rand(8, 99999999),
                            'user_id' => $user->id,
                            'status' => 1,
                        ];
                        $device = Device::create($dev_data);
                    }
                    // $user->syncRoles('Customer');
                    // $this->sendVerifyEmail( $user );
                    $company_user_details = ['user_id' => $user->id, 'company_id' => $request->company];
                    $company_user = CompanyUsers::create(removeEmptyElements($company_user_details));
                    $details = [
                        'user_id' => $user->id,
                        'address' => $request->address,
                        'country_id' => jsdecode_userdata($request->country),
                        'city_id' => jsdecode_userdata($request->city),
                        'state_id' => jsdecode_userdata($request->state),
                        'zipcode' => $request->zipcode,
                        'mobile' => $request->mobile,
                        'weight' => $request->weight,
                        'height' => $request->height,
                        'body_fat' => $request->body_fat,
                        'bmi' => $request->bmi,
                        'gender' => $request->gender,
                        'dob' => $request->dob,
                    ];
                    $result = UserDetails::create(removeEmptyElements($details));

                    if ($result) {
                        DB::commit();
                        return [
                            'success' => true,
                            'msg' => 'User created successfully.',
                        ];
                    }
                }
                DB::rollBack();
                return [
                    'success' => false,
                    'msg' => Config::get('constants.ERROR.OOPS_ERROR'),
                ];
            } catch (\Exception$e) {
                DB::rollBack();
                return [
                    'success' => false,
                    'msg' => $e->getMessage(),
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
    public function updatePassword(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            return view('admin.user.password', compact('userType', 'id'));
        }
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);
        $userId = jsdecode_userdata($id);
        User::where('id', $userId)->update([
            'password' => bcrypt($request->password),
        ]);
         if(auth()->user()->hasRole('1_Company')){
            return redirect()->back()->with('status', 'success')->with('message', 'User password ' . Config::get('constants.SUCCESS.UPDATE_DONE'));
        }
        return redirect()->route('user.list')->with('status', 'success')->with('message', 'User password ' . Config::get('constants.SUCCESS.UPDATE_DONE'));
    }

    /*
    Method Name:    view_detail
    Developer:      Shiv K. Agg
    Purpose:        To get detail of users
    Params:         [id]
     */
    public function view_detail($id, Request $request)
    {
        $userId = jsdecode_userdata($id);
        $userDetail = User::withTrashed()->find($userId);
        $response = [
            'first_name' => $userDetail->first_name,
            'last_name' => $userDetail->last_name,
            'email' => $userDetail->email,
            'role'  => trim_role_name($userDetail->roles->first()->name),
            'mobile' => $userDetail->user_detail ? $userDetail->user_detail->mobile : '',
            'address' => $userDetail->user_detail ? $userDetail->user_detail->address : '',
            'country' => $userDetail->user_detail && $userDetail->user_detail->country ? $userDetail->user_detail->country->name : '',
            'company' => $userDetail->company_id() ? get_company_name($userDetail->company_id()) : '',
            'state' => $userDetail->user_detail && $userDetail->user_detail->state ? $userDetail->user_detail->state->name : '',
            'city' => $userDetail->user_detail && $userDetail->user_detail->city ? $userDetail->user_detail->city->city_name : '',
            'weight' => $userDetail->user_detail ? $userDetail->user_detail->weight . $userDetail->user_detail->weight_unit : '',
            'height' => $userDetail->user_detail ? $userDetail->user_detail->height . $userDetail->user_detail->height_unit : '',
            'gender' => $userDetail->user_detail ? ucfirst($userDetail->user_detail->gender) : '',
            'dob' => $userDetail->user_detail ? $userDetail->user_detail->dob : '',
            'edit_user' => route('user.edit', ['id' => jsencode_userdata($userDetail->id)]),
            // 'subscriptions' => strval(view('admin.user.subscriptions', compact('userDetail'))),
         ];
        return [
            'status' => 'true',
            'data' => $response,
        ];
        if (!$userDetail) {
            return redirect()->route('user.list');
        }

        return view('admin.user.view_detail', compact('userDetail', 'userType'));
    }
    /* End Method view_detail */

    public function changeStatus(Request $request)
    {
        try {
            $user = User::withTrashed()->find(jsdecode_userdata($request->id));
            $user->status = $request->status;
            $user->save();

            return response()->json(['success' => 'Status change successfully.', 'message' => 'User ' . Config::get('constants.SUCCESS.STATUS_UPDATE')]);
        } catch (\Exception$e) {
            return redirect()->back()->with('status', 'error')->with('message', $e->getMessage());
        }
    }

    public function export()
    {
        return \Excel::download(new UsersExport, 'users.xlsx');
    }

    public function getDevices(Request $request)
    {
        $userId = jsdecode_userdata($request->id);
        $userData = User::where('id', $userId)->first();
        $data = User::where('id', $userId)->first()->devices()->paginate(Config::get('constants.PAGINATION_NUMBER'));
        $deletedDevices = User::where('id', $userId)->first()->devices()->onlyTrashed()->sortable(['id' => 'desc'])->paginate(Config::get('constants.PAGINATION_NUMBER'));
        return view('admin.user.devices', compact('userId', 'data', 'deletedDevices', 'userData'));
    }

    public function addDevice(Request $request)
    {
        $userId = jsdecode_userdata($request->id);
        $validationRules = [
            'device_name' => 'required|string',
        ];

        try {
            $data = [
                'device_name' => $request->device_name,
                'device_activation_code' => rand(8, 99999999),
                'user_id' => $request->user_id,
                'status' => 1,
            ];
            DB::beginTransaction();

            $device = Device::create($data);
            if ($device) {
                DB::commit();
                return [
                    'success' => true,
                    'msg' => 'Device created successfully.',
                ];

            }
            DB::rollBack();
            return [
                'success' => false,
                'msg' => Config::get('constants.ERROR.OOPS_ERROR'),
            ];
        } catch (\Exception$e) {
            DB::rollBack();
            return [
                'success' => false,
                'msg' => $e->getMessage(),
            ];
        }
    }

    public function del_device($id)
    {
        try {
            $deviceId = jsdecode_userdata($id);
            Device::where('id', $deviceId)->delete();
            return redirect()->back()->with('status', 'success')->with('message', 'Device details ' . Config::get('constants.SUCCESS.DELETE_DONE'));
        } catch (Exception $ex) {
            return redirect()->back()->with('status', 'error')->with('message', $ex->getMessage());
        }
    }

    public function device_restore($id)
    {
        try {
            $deviceId = jsdecode_userdata($id);
            Device::where('id', $deviceId)->restore();
            return redirect()->back()->with('status', 'success')->with('message', 'Device details ' . Config::get('constants.SUCCESS.RESTORE_DONE'));
        } catch (Exception $ex) {
            return redirect()->back()->with('status', 'error')->with('message', $ex->getMessage());
        }
    }

    public function changeDeviceStatus(Request $request)
    {
        try {
            $user = Device::withTrashed()->find(jsdecode_userdata($request->id));
            $user->status = $request->status;
            $user->save();

            return response()->json(['success' => 'Status change successfully.', 'message' => 'Device ' . Config::get('constants.SUCCESS.STATUS_UPDATE')]);
        } catch (\Exception$e) {
            return redirect()->back()->with('status', 'error')->with('message', $e->getMessage());
        }
    }


    // Devices
    public function sendPushNotification(Request $request)
    {
        $tokens = [];

        if ($request->has('device_id')) {
            $deviceId = jsdecode_userdata($request->device_id);
            $token = Device::where(['id' => $deviceId, 'is_activate' => 1, 'status' => 1])->pluck('device_token')->first();
            if ($token != null) {
                $tokens[] = $token;
            } else {
                return [
                    'success' => false,
                    'msg' => "Oops! Either there is no device token or token is not activated",
                ];
            }
        } else if ($request->has('user_id')) {
            $userId = jsdecode_userdata($request->user_id);
            $token = Device::where(['user_id' => $userId, 'is_activate' => 1, 'status' => 1])->get('device_token')->toArray();
            if ($token != null) {
                foreach ($token as $key => $item) {
                    if (is_array($token)) {
                        if ($token[0]['device_token'][0] != null) {
                            $tokens[] = $token[0]['device_token'];
                        }
                    }
                }
            }
        } else {
            $tokenx = Device::get('device_token')->toArray();
            if ($tokenx != null) {
                foreach ($tokenx as $key => $token) {
                    foreach ($token as $key => $item) {
                        if (is_array($token)) {
                            if ($token['device_token'] != null) {
                                $tokens[] = $token['device_token'];
                            }
                        }
                    }
                }
            }
        }

        $firebaseToken = array_values($tokens);

        $SERVER_API_KEY = env('FCM_SERVER_KEY');
        $data = [
            "registration_ids" => $firebaseToken,
            "data" => [
                "title" => "FREIGHT MANAGEMENT Tracking",
                "body" => "FREIGHT MANAGEMENT Tracking Applcation",
                "custom_data" => [
                    "title"=>$request->title,
                    "message"=> $request->message,
                    "type" =>"default"
                ]
            ],
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
        if (json_decode($response)->success) {
            return [
                'success' => true,
                'msg' => 'Notification Sent Succesfully.',
            ];
        } else {
            return [
                'success' => false,
                'msg' => "Firebase returned '" .json_decode($response)->results[0]->error . "' error",
            ];
        }
    }


    //users
    public function sendPushNotificationToUsers(Request $request)
    {
        $tokens = [];
        if ($request->has('user_id')) {
            $deviceId = jsdecode_userdata($request->user_id);
            $token = User::where(['id' => $deviceId,'status' => 1])->pluck('device_token')->first();
            if ($token != null) {
                $tokens[] = $token;
            } else {
                return [
                    'success' => false,
                    'msg' => "Oops! Either there is no device token or token is not activated",
                ];
            }
        } else {
            if(Auth::user()->hasRole('Administrator')){
                $tokenx = User::select('device_token')->get();
            }else{
                $companyId = Auth::user()->id;
                $user_ids = User::whereHas('companyUsers', function ($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })->where('id', '<>', Auth::id())->get()->pluck('id', 'first_name');

                $tokenx  = User::whereHas('companyUsers', function ($query) use ($companyId, $user_ids) {
                    $query->where('company_id', $companyId);
                    $query->orWhereIn('company_id',$user_ids);
                })
                ->where('id', '<>', Auth::id())->get();
            }

            if ($tokenx != null) {
                foreach ($tokenx as $key => $token) {
                    if ($token['device_token'] != null) {
                        $tokens[] = $token['device_token'];
                    }
                }
            }
        }

        $firebaseToken = array_values($tokens);

        $SERVER_API_KEY = env('FCM_SERVER_KEY');

        $data = [
            "registration_ids" => $firebaseToken,
            "data" => [
                "title" => "FREIGHT MANAGEMENT Tracking",
                "body" => "FREIGHT MANAGEMENT Tracking Applcation",
                "custom_data" => [
                    "title"=>$request->title,
                    "message"=> $request->message,
                    "type" =>"default"
                ]
            ],
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

        if (json_decode($response)->success) {
            return [
                'success' => true,
                'msg' => 'Notification Sent Succesfully.',
            ];
        } else {
            return [
                'success' => false,
                'msg' => Config::get('constants.ERROR.OOPS_ERROR'),
            ];
        }
    }


    public function assignUserVehicle(Request $request){
        try {
            $userId = jsdecode_userdata($request->user_id);
            $user = User::find($userId);
            $user->vehicles()->detach();
            if(!isset($request->vehicle) || empty($request->vehicle)){
                $user->vehicles()->detach();
                return [
                    'success' => true,
                    'msg' => 'Vehicles Unassigned successfully.',
                ];
            }

            $vehicles = Vehicle::whereIn('id', $request->vehicle)->get();

            foreach ($vehicles as $vehicle) {
                foreach ($vehicles as $vehicle) {
                    UserVehicles::updateOrCreate(
                        ["user_id" => $userId, "vehicle_id" => $vehicle->id],
                        ["user_id" => $userId, "vehicle_id" =>$vehicle->id]
                    );
                }
            }

            return [
                'success' => true,
                'msg' => 'Vehicles assigned successfully.',
            ];

        } catch ( \Exception $e ) {
            return [
                'success' => false,
                'msg' => Config::get('constants.ERROR.OOPS_ERROR'),
            ];

        }
    }

    public function track_device($token = null)
    {
        try {
            if (is_null($token)) {
                return redirect()->route('user.list')->withInput()->with('status', 'error')->with('message', "Oops! There must be no token for this Device exists.");
            }
            $devices=[];
            $device = Device::where('device_token', $token)->first();
            if (is_null($device)) {
                $user = User::where('device_token', $token)->first();
            } else {
                $user = User::where('id', $device['user_id'])->first();
            }

            if (is_null($user)) {
                return redirect()->route('user.list')->withInput()->with('status', 'error')->with('message', "Oops! No user found for this Device.");
            }

            $vehicles = $user->vehicles()->get();

            // Fetch latest coordinates/3
            $log_response = makeCurlRequest(env('MONGO_URL')."fetchlatestcoordinates/".$user['id'], 'GET', []);

            $data = json_decode($log_response)->data ?? null;

            return view('admin.user.track-device', compact('token', 'device', 'user', 'vehicles', 'data'));
        } catch (\Exception $e) {
            return redirect()->route('user.list')->withInput()->with('status', 'error')->with('message', "Oops! Something went wrong while tracking device.");
        }
    }

    // public function myRides($token){

    //     $data
    // }


}
