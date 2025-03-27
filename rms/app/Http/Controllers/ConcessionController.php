<?php

namespace App\Http\Controllers;

use App\Models\Concession;
use App\Models\Order;
use App\Repositories\ConcessionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ConcessionController extends Controller
{
    private $concession_repo;

    function __construct()
    {
        $this->concession_repo = new ConcessionRepository();
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Concession');

        if ($check_premission == false) {
            return abort(403);
        }


        if ($request->json) {

            $categories = $this->concession_repo->concessions_list($request);

            $data =  DataTables::of($categories)
                ->addIndexColumn()

                ->addColumn('image', function ($item) {
                    $url = config('awsurl.url').($item->image);

                    if ($item->image == '' || $item->image == 0) {
                        return '<img src="'.asset('layout_style/img/default.png').'" border="0" width="50" height="50"style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
                  }
                    return '<img src="' . $url . '" border="0" width="50" height="50" style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
                })
                ->addColumn('status', function ($item) {
                    if ($item->status == 0) {
                        return '<span class="badge badge-soft-danger badge-border">Inactive</span>';
                    }

                    if ($item->status == 1) {
                        return '<span class="badge badge-soft-success badge-borders">Active</span>';
                    }
                })

                ->addColumn('action', function ($item) {
                    $user = Auth::user();
                    $edit_route = route('concessions.update.form', $item->ref_no);
                    $view_url = route('concessions.view_details', $item->ref_no);

                    $actions = '';
                    $item_cancel = new Order();
                    $item_cancel->status = 5;

                    $item->status;
                    $actions = action_btns($actions, $user, 'Concession', $edit_route, $item->id,$item_cancel,$view_url);

                    $action = '<div class="dropdown dropdown-action">
                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                    '</div></div>';

                    return $action;
                })
                ->rawColumns(['action', 'status','image'])
                ->make(true);

            return $data;
        }

        return view('concessions.index');
    }
    public function create_form()
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Concession');

        if ($check_premission == false) {
            return abort(403);
        }

        return view('concessions.create');
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|regex:/^[a-z 0-9 A-Z]+$/u|max:190|unique:concessions,name,NULL,id,deleted_at,NULL',
                'image'=>'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
                'price' => 'required',
                'description' => 'nullable|max:190',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $data = $this->concession_repo->create($request);

        $data['status'] = true;
        $data['message'] = 'New Concession Created Successfully!';
        $data['route'] = route('concessions');

        return response()->json($data);
    }

    public function update_form($id)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_Concession');

        if ($check_premission == false) {
            return abort(403);
        }

        $find_concession = Concession::where(['ref_no' => $id])->first();

        if (!$find_concession) {
            return abort(404);
        }

        return view('concessions.update',[
            'find_concession' => $find_concession
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|regex:/^[a-z A-Z 0-9]+$/u|max:190|unique:concessions,name,'.$id.',id,deleted_at,NULL',
                'image'=>'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
                'price' => 'required',
                'description' => 'nullable|max:190',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $data = $this->concession_repo->update($request);

        $data['route'] = route('concessions');

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $data = $this->concession_repo->delete($request);

        $data['route'] = route('concessions');

        return response()->json($data);
    }

    public function view_details(Request $request, $ref_no)
    {
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Concession');

        if ($check_premission == false) {
            return abort(403);
        }
        // End

        $concessions = Concession::Where(['ref_no' => $ref_no])->first();

        if (!$concessions) {
            return abort(404);
        }

        return view('concessions.view_details', [
            'concessions' =>  $concessions
        ]);
    }
}
