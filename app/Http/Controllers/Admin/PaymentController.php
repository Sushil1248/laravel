<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Config,DB,Validator};
use App\Models\{Payment};


class PaymentController extends Controller
{
    public function getList( Request $request ){
        $start = $end = "";
        if( $request->filled('daterange_filter') ) {
            $daterange = $request->daterange_filter;
            $daterang = explode(' - ',$daterange);
            $start = $daterang[0].' 00:05:00';
            $end = $daterang[1].' 23:05:59';
        } 
        $data = Payment::when( !empty($start) && !empty($end) , function($q , $from) use( $start , $end ) {
            $q->whereBetween( 'created_at' , [$start , $end] );
        })
        ->when( $request->filled('status') , function($qu){
            $qu->where('status',request('status'));
        });
        $data = $data->sortable(['id' => 'desc'])->paginate(Config::get('constants.PAGINATION_NUMBER'));
        
        return view('admin.payment.list', compact('data'));
    }
}