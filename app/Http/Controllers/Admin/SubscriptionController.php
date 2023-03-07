<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Config,DB,Validator};
use App\Models\{SubscriptionPlan};
use Spatie\Permission\Models\Permission;
use Auth;
use Laravel\Cashier\Cashier;

class SubscriptionController extends Controller
{
    function __construct(){
        $this->stripe = Cashier::stripe();
    }
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
        $data = SubscriptionPlan::when( !empty($start) && !empty($end) , function($q , $from) use( $start , $end ) {
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
        
        return view('admin.subscription-plan.list', compact('data','deletedData'));
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
            $subscriptionId = jsdecode_userdata($id);
            $subscriptionPlan = SubscriptionPlan::withTrashed()->find( $subscriptionId );
            $this->updateProductStatus(  $subscriptionPlan , 0 );
            SubscriptionPlan::where('id',$subscriptionId)->delete();
        	return redirect()->back()->with('status', 'success')->with('message', 'Subscription plan '.Config::get('constants.SUCCESS.DELETE_DONE'));
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
            $subscriptionId = jsdecode_userdata($id);
            $subscriptionPlan = SubscriptionPlan::withTrashed()->find( $subscriptionId );
            $this->updateProductStatus(  $subscriptionPlan );
            SubscriptionPlan::where('id',$subscriptionId)->restore();
        	return redirect()->back()->with('status', 'success')->with('message', 'Subscription plan '.Config::get('constants.SUCCESS.RESTORE_DONE'));
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
        $subscriptionId = jsdecode_userdata($id);
        $subscriptionDetail = SubscriptionPlan::find($subscriptionId);
        if(!$subscriptionDetail)
            return redirect()->route('exercise.list');
        return view('admin.subscription-plan.edit',compact('subscriptionDetail'));
        
    }
    /* End Method edit_form */


     /*
     Method Name:    add_form
     Developer:      Shiv K. Agg
     Created Date:   2022-06-23 (yyyy-mm-dd)
     Purpose:        Form to add user details
    Params:         [id]
     */
    public function add(Request $request , $subscription_id = null ){
        $subscriptionPlan = false;
        if( jsdecode_userdata($subscription_id) )
            $subscriptionPlan = SubscriptionPlan::find(jsdecode_userdata($subscription_id));
        $request->validate([
            'name'        =>   'required|max:100',
            'price'       =>    $subscriptionPlan ? '' : 'required|numeric',
            'number_of_months'  =>  $subscriptionPlan ? '' : 'required|numeric'
        ]);
        try {
            if( $subscriptionPlan ){
                $this->updateProduct( $request , $subscriptionPlan );
                $subscriptionData = [
                    'name'  =>  $request->name,
                    'description'   =>  $request->description,
                    'features_offered'  =>  $request->features_offered
                ];
                $subscriptionPlan->update( $subscriptionData );
                $isUpdated = true;
            }else{
                $pricing = $this->createProgram( $request );
                $subscriptionData = [
                    'name'  =>  $request->name,
                    'price' =>  $request->price,
                    'billing_interval'  =>  'month',//For now all plans will be in months
                    'billing_interval_count'    =>  $request->number_of_months,
                    'product_id'    =>  $pricing->product,
                    'price_id'      =>  $pricing->id,
                    'description'   =>  $request->description,
                    'features_offered'  =>  $request->features_offered,
                    'raw_response'  =>  $pricing,
                    'status'        =>  1
                ];
                $subscriptionPlan = SubscriptionPlan::create( $subscriptionData );
            }
            $message = 'Subscription plan detail '. Config::get('constants.SUCCESS.' . ( empty($isUpdated) ? "CREATE_DONE" : "UPDATE_DONE" ) );
            
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
            ]);
        }
        
    }
    
    

    /* End Method view_detail */
    
    public function changeStatus(Request $request)
    {
        try {
            $subscriptionPlan = SubscriptionPlan::withTrashed()->find( jsdecode_userdata($request->id) );
            $subscriptionPlan->status = $request->status;
            $this->updateProductStatus(  $subscriptionPlan , $request->status );
            $subscriptionPlan->save();
            return response()->json(['success' => 'Status change successfully.','message' => 'Subscription plan '.Config::get('constants.SUCCESS.STATUS_UPDATE')]);
        } catch ( \Exception $e ) {
            return response()->json(['error' => 'Status change successfully.','message' => $e->getMessage() ]);
            return redirect()->back()->with('status', 'error')->with('message', $e->getMessage());
        }
    }

    public function view_detail( $subscription_id ){
        $subscriptionPlan = SubscriptionPlan::withTrashed()->find( jsdecode_userdata($subscription_id) );
        if( $subscriptionPlan ){
            $responseData = [
                
            ];
            return response()->json(['status' => true,'data' => $responseData ]);
        }
    }

    /** Create program and its price in Stripe **/
    private function createProgram( Request $request ){
        return $this->stripe->prices->create([
            'unit_amount'   => intval( $request->price * 100 ),
            'currency'      => config('cashier.currency'),
            'recurring'     => ['interval' => 'month','interval_count'  =>  intval($request->number_of_months)],
            'product_data'  =>  [
                'name'  =>  $request->name
            ]
        ]);
    }

    /** Update stripe product **/
    private function updateProduct( Request $request , $subscription ){
        $this->stripe->products->update(
            $subscription->product_id,
            ['name' =>  $request->name]
        );
    }

    /** Update stripe product status **/
    private function updateProductStatus(  $subscription , $status = 1 ){
        $this->stripe->products->update(
            $subscription->product_id,
            ['active' =>  $status ? true : false]
        );
    }
}
