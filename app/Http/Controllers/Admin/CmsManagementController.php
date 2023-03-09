<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Config,DB,Validator};
use App\Models\{CmsPage};
use Spatie\Permission\Models\Permission;
use Auth;

class CmsManagementController extends Controller
{

    /*
    Method Name:    getList
    Developer:      Shiv K. Agg 
    Purpose:        To get list of all exercises
    Params:
    */
    public function getList( Request $request ){
        $start = $end = "";
        if( $request->filled('daterange_filter') ) {
            $daterange = $request->daterange_filter;
            $daterang = explode(' - ',$daterange);
            $start = $daterang[0].' 00:05:00';
            $end = $daterang[1].' 23:05:59';
        } 
        $data = CmsPage::when( !empty($start) && !empty($end) , function($q , $from) use( $start , $end ) {
            $q->whereBetween( 'created_at' , [$start , $end] );
        })->when($request->search ,function($qu , $keyword ) {
            $qu->where(function ($q) use( $keyword ) {
                $q->where('name', 'like', '%'.$keyword.'%');
            });
        })
        ->when( $request->filled('status') , function($qu){
            $qu->where('status',request('status'));
        });
        $deletedData = (clone $data)->onlyTrashed()->sortable(['id' => 'desc'])
        ->paginate(Config::get('constants.PAGINATION_NUMBER'),'*','dpage');
        $data = $data->sortable(['id' => 'desc'])->paginate(Config::get('constants.PAGINATION_NUMBER'));
        
        return view('admin.cms-management.list', compact('data','deletedData'));
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
            $pageId = jsdecode_userdata($id);
            CmsPage::where('id',$pageId)->delete();
        	return redirect()->back()->with('status', 'success')->with('message', 'CMS detail '.Config::get('constants.SUCCESS.DELETE_DONE'));
        } catch(Exception $ex) {
            return redirect()->back()->with('status', 'error')->with('message', $ex->getMessage());
        }
    }
    /* End Method del_record */

    /*
    Method Name:    del_restore
    Developer:      Shiv K. Agg 
    Purpose:        To restore deleted Media by id
    Params:         [id]
    */
    public function del_restore($id){
        try {
            $pageId = jsdecode_userdata($id);
            CmsPage::where('id',$pageId)->restore();
        	return redirect()->back()->with('status', 'success')->with('message', 'CMS detail '.Config::get('constants.SUCCESS.RESTORE_DONE'));
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
        $pageId = jsdecode_userdata($id);
        $cmsDetail = CmsPage::find($pageId);
        if(!$cmsDetail)
            return redirect()->route('exercise.list');
        return view('admin.cms-management.edit',compact('cmsDetail'));
        
    }
    /* End Method edit_form */


     /*
     Method Name:    add_form
     Developer:      Shiv K. Agg
     Created Date:   2022-06-23 (yyyy-mm-dd)
     Purpose:        Form to add user details
    Params:         [id]
     */
    public function add(Request $request , $smspage_id = null ){
        $request->validate([
            'name'        =>   'required|max:100'
        ]);
        try {
            $postData = $request->all();
            $postData['media_id'] = jsdecode_userdata($request->media_id);
            $postData = removeEmptyElements( $postData );
            // return $postData;
            if( jsdecode_userdata($smspage_id) && ($cmsPage = CmsPage::find(jsdecode_userdata($smspage_id))) ){
                $cmsPage->update( $postData );
                $isUpdated = true;
            }else{
                $postData['status'] = 1;
                $cmsPage = CmsPage::create( $postData );
            }
            $message = 'CMS page detail '. Config::get('constants.SUCCESS.' . ( empty($isUpdated) ? "CREATE_DONE" : "UPDATE_DONE" ) );
            
            if( $request->expectsJson() ){
                return response()->json([
                    'success'    =>  true,
                    'msg' => $message
                ]);
            }
            return redirect()->route('steph-workout.list')->with('status', 'success')->with('message', $message );
        } catch ( \Exception $e ) {
            return response()->json([
                'success'    =>  false,
                'msg'       =>      $e->getMessage()
            ],400);
        }
        
    }
    
    

    /* End Method view_detail */
    
    public function changeStatus(Request $request)
    {
        try {
            $media = CmsPage::withTrashed()->find( jsdecode_userdata($request->id) );
            $media->status = $request->status;
            $media->save();
            return response()->json(['success' => 'Status change successfully.','message' => 'CMS page '.Config::get('constants.SUCCESS.STATUS_UPDATE')]);
        } catch ( \Exception $e ) {
            return response()->json(['error' => 'Status change successfully.','message' => $e->getMessage() ]);
            return redirect()->back()->with('status', 'error')->with('message', $e->getMessage());
        }
    }

    public function view_detail( $smspage_id ){
        $cmsPage = CmsPage::withTrashed()->find( jsdecode_userdata($smspage_id) );
        if( $cmsPage ){
            $responseData = [
                'name'  =>  $cmsPage->name,
                'category'  =>  $cmsPage->category ? $cmsPage->category->name : '',
                'exercise'  =>  $cmsPage->exercise ? $cmsPage->exercise->name : '',
                'sets'      =>  $cmsPage->sets,
                'reps'      =>  $cmsPage->reps,
                'workout_info'  =>  $cmsPage->workout_info,
                'edit_workout'  =>  route('steph-workout.edit',['id'=>jsencode_userdata($cmsPage->id)])
            ];
            return response()->json(['status' => true,'data' => $responseData ]);
        }
    }
}
