<?php

namespace App\Http\Controllers;

use App\Exports\CompaniesExport;

use App\Models\Company;
use App\Models\CompanyDetail;
use App\Models\Vehicle;
use App\Models\Country;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, Hash, Auth, mail, Password,Config};
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
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

    /*
    Method Name:    getList
    Developer:      Sushil
    Purpose:        To get list of all companies
    Params:
     */
    public function getList(Request $request, $deleted = "")
    {
        $start = $end = "";
        if ($request->filled('daterange_filter')) {
            $daterange = $request->daterange_filter;
            $daterang = explode(' - ', $daterange);
            $start = $daterang[0] . ' 00:00:00';
            $end = $daterang[1] . ' 23:05:59';
        }

        $data = Vehicle::when(!empty($start) && !empty($end), function ($q, $from) use ($start, $end) {
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
            })
            ->when(jsdecode_userdata($request->user_id), function ($query, $user_id) {
                $query->where('id', $user_id);
            });

        $deletedVehicle = (clone $data)->onlyTrashed()->sortable(['id' => 'desc'])
            ->paginate(Config::get('constants.PAGINATION_NUMBER'), '*', 'dpage');
        $data = $data->sortable(['id' => 'desc'])->paginate(Config::get('constants.PAGINATION_NUMBER'));

        return view('company.vehicle.list', compact('data', 'deletedVehicle'));
    }
    /* End Method getList */

    /*
    Method Name:    del_record
    Developer:      Sushil
    Purpose:        To delete any user by id
    Params:         [id]
     */
    public function del_record($id)
    {
        try {
            $comapnyId = jsdecode_userdata($id);
            Vehicle::where('id', $comapnyId)->delete();
            return redirect()->back()->with('status', 'success')->with('message', 'Company details ' . Config::get('constants.SUCCESS.DELETE_DONE'));
        } catch (Exception $ex) {
            return redirect()->back()->with('status', 'error')->with('message', $ex->getMessage());
        }
    }
    /* End Method del_record */

    /*
    Method Name:    del_restore
    Developer:      Sushil
    Purpose:        To restore deleted user by id
    Params:         [id]
     */
    public function del_restore($id)
    {
        try {
            $userId = jsdecode_userdata($id);
            Vehicle::where('id', $userId)->restore();
            return redirect()->back()->with('status', 'success')->with('message', 'Vehicle ' . Config::get('constants.SUCCESS.RESTORE_DONE'));
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
            $companyId = jsdecode_userdata($id);

            $companyDetail = vehicle::find($companyId);
            if (!$companyDetail) {
                return redirect()->route('vehicle.list');
            }

            $country = Country::pluck('name', 'id');
            return view('company.vehicle.edit', compact('companyDetail'));

        } else {
            $vehicleId = jsdecode_userdata($id);

            $request->validate([
                'name' => 'required|string|max:100',
                'vehicle_num' => 'required',
            ]);

            try {
                $status = 0;
                if ($request->status == "on") {
                    $status = 1;
                }
                $company = Vehicle::findOrFail($vehicleId);
                $company->name = $request->name;
                $company->extra_notes = $request->extra_notes;
                $company->vehicle_num = $request->vehicle_num;
                $company->status = $request->input('status', 0);

                $company->save();

                return redirect()->route('c.vehicle.list')->with('status', 'success')->with('message', 'Vehicle details ' . Config::get('constants.SUCCESS.UPDATE_DONE'));
            } catch (\Exception$e) {
                return redirect()->back()->withInput()->with('status', 'error')->with('message', $e->getMessage());
            }
        }
    }
    /* End Method edit_form */

    /*
    Method Name:    add_form
    Developer:      Sushil
    Created Date:   2022-06-23 (yyyy-mm-dd)
    Purpose:        Form to add user details
    Params:         [id]
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
        } else {
            $validationRules = [
                'name' => 'required|string|max:100',
                'vehicle_num' => 'required|unique:users,email',
                'user_id'   =>'required',
            ];
            $request->validate($validationRules, [
                'name.required' => 'Vehicle Name is required',
            ]);

            try {
                $data = [
                    'name' => $request->name,
                    'vehicle_num' => $request->vehicle_num,
                    'user_id' => $request->user_id,
                    'extra_notes' => $request->extra_notes,
                    'status' => 1,
                ];
                DB::beginTransaction();

                $result = Vehicle::create($data);

                if ($result) {
                    DB::commit();
                    return [
                        'success' => true,
                        'msg' => 'Vehicle created successfully.',
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
    }

    public function view_detail($id, Request $request)
    {
        $vehicle_id = jsdecode_userdata($id);
        $vehicle_Detail = Vehicle::withTrashed()->find($vehicle_id);

        $response = [
            'name' => $vehicle_Detail->name,
            'vehicle_num' => $vehicle_Detail->vehicle_num,
            'extra_notes' => $vehicle_Detail->extra_notes,
            'edit_user' => route('c.vehicle.edit', ['id' => jsencode_userdata($vehicle_Detail->id)]),
        ];
        return [
            'status' => 'true',
            'data' => $response,
        ];
        if (!$vehicle_Detail) {
            return redirect()->route('vehicle.list');
        }

        return view('company.vehicle.view_detail', compact('vehicle_Detail', 'userType'));
    }
    /* End Method view_detail */

    public function changeStatus(Request $request)
    {
        try {
            $company = Vehicle::withTrashed()->find(jsdecode_userdata($request->id));
            $company->status = $request->status;
            $company->save();

            return response()->json(['success' => 'Status change successfully.', 'message' => 'Vehicle ' . Config::get('constants.SUCCESS.STATUS_UPDATE')]);
        } catch (\Exception$e) {
            return redirect()->back()->with('status', 'error')->with('message', $e->getMessage());
        }
    }

    public function export()
    {
        return \Excel::download(new CompaniesExport, 'company.xlsx');
    }

}
