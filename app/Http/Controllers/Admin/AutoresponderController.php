<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\AutoResponder;
use DB;

class AutoresponderController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:template-list|template-edit', ['only' => ['getList']]);
        // $this->middleware('permission:template-create', ['only' => ['add']]);
        $this->middleware('permission:template-edit', ['only' => ['edit_form','update_record']]);
        // $this->middleware('permission:template-delete', ['only' => ['del_record']]);
    }
    /*
    Method Name:    getList
    Developer:      Shiv K. Agg
    Created Date:   2022-07-02 (yyyy-mm-dd)
    Purpose:        To get all added templates
    Params:
    */
    public function getList(Request $request)
    {
        if($request->has('search_keyword') && $request->search_keyword != '')
        {
            $keyword = $request->search_keyword;
        }
        else
        {
            $keyword = '';
        }
        $data = AutoResponder::when($request->search_keyword, function ($q) use ($request)
        {
            $q->where('template_name', 'like', '%' . $request->search_keyword . '%')
                ->orWhere('template', 'like', '%' . $request->search_keyword . '%')
                ->orWhere('id', $request->search_keyword);
        })
            ->sortable(['id' => 'desc'])
            ->paginate(Config::get('constants.PAGINATION_NUMBER'));
        return view('admin.autoresponder.list', compact('data','keyword'));
    }
    /* End Method getList */

    /*
    Method Name:    edit_form
    Developer:      Shiv K. Agg
    Created Date:   2022-07-02 (yyyy-mm-dd)
    Purpose:        To update template details
    Params:         [edit_record_id, subject, template_name, template, status]
    */
    public function edit_form($id)
    {
        $tempId = jsdecode_userdata($id);

        $record = AutoResponder::find($tempId);
        if(!$record)
        return redirect()->route('autoresponder.list');
        return view('admin.autoresponder.edit', compact('record'));
    }
    /* End Method edit_form */

     /*
    Method Name:    update_record
    Developer:      Shiv K. Agg
    Created Date:   2022-07-02 (yyyy-mm-dd)
    Purpose:        To update template details
    Params:         [edit_record_id, subject, template_name, template, status]
    */
    public function update_record(Request $request)
    {
        $request->validate(['subject' => 'required', 'template' => 'required']);
        try
        {
            $data = array(
                'subject' => $request->subject,
                'template' => $request->template
            );
            $tempId = jsdecode_userdata($request->edit_record_id);
            
            $record = AutoResponder::where('id', $tempId)
                ->update($data);
            return redirect()->route('autoresponder.list')
                ->with('status', 'success')
                ->with('message', 'Email template ' . Config::get('constants.SUCCESS.UPDATE_DONE'));
        }
        catch(\Exception $e)
        {
            return redirect()->back()
                ->with('status', 'error')
                ->with('message', $e->getMessage());
        }
    }
    /* End Method update_record */
}
