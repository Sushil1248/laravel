<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:permission-list|permission-create|permission-edit|permission-delete|permission-view', ['only' => ['getList']]);
        $this->middleware('permission:permission-create', ['only' => ['create_record','add_form']]);
        $this->middleware('permission:permission-edit', ['only' => ['edit_form','update_record']]);
        $this->middleware('permission:permission-delete', ['only' => ['delete_record']]);
        
    }

    public function getList(Request $request){
        $keyword = $request->search_keyword;
        $data = Permission::when($keyword, function($q) use($request) {
            $q->where('name', 'like', '%'.$keyword.'%')
            ->orWhere('group_name','like', '%'.$keyword.'%' );
        })->latest()->paginate(Config::get('constants.PAGINATION_NUMBER'));
        return view('admin.permission.list', compact('data','keyword'));
    }
    public function add_form(){
        return view('admin.permission.add');
    }

    public function create_record(Request $request){
        $request->validate([
            'name' => 'required|unique:permissions',
            'guard_name' => 'required'
        ]);
        try {
            $postData = $request->all();
            $data = array(
                'name' => $postData['name'],
                'group_name' => $postData['group_name'],
                'guard_name' => $postData['guard_name']
            );
            $record = Permission::create($data);
            $role = Role::find(1);
            $role->givePermissionTo($record);
            if($record){
                $routes = ($request->action == 'saveadd') ? 'permission.add' : 'permission.list';
                return redirect()->route($routes)->with('status', 'success')->with('message', 'Permission '.Config::get('constants.SUCCESS.CREATE_DONE'));
            }
        } catch ( \Exception $e ) {
            return redirect()->back()->with('status', 'error')->with('message', $e->getMessage());
        }
    }


    public function edit_form( $id ){
        $permissionId = jsdecode_userdata($id);
        $permission = Permission::find( $permissionId );   
        return view('admin.permission.edit', compact('permission'));
    }

    public function update_record(Request $request){
        try {
            $permissionId = jsdecode_userdata($request->id);
            Permission::where('id',$permissionId)->update([
                'group_name' => $request->group_name,
                'name' => $request->name
            ]);
            return redirect()->route('permission.list')->with('status', 'success')->with('message', 'Permission '.Config::get('constants.SUCCESS.UPDATE_DONE'));
        } catch ( \Exception $e ) {
            return redirect()->back()->with('status', 'error')->with('message', $e->getMessage());
        }
    }

    public function delete_record($id){
        try {
            
            $permissionId = jsdecode_userdata($id);
            
            Permission::where('id',$permissionId)->delete();
            return redirect()->back()->with('status', 'success')->with('message', 'Permission '.Config::get('constants.SUCCESS.DELETE_DONE'));
        }catch(Exception $ex){
            return redirect()->back()->with('status', 'error')->with('message', $e->getMessage());
        }
    }
}
