<?php

namespace App\Http\Controllers;

use App\Models\Concession;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    private $order_repo;

    function __construct()
    {
        $this->order_repo = new OrderRepository();
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Order');

        if ($check_premission == false) {
            return abort(403);
        }


        if ($request->json) {

            $order = $this->order_repo->order_list($request);

            $data =  DataTables::of($order)
                ->addIndexColumn()

                ->addColumn('status', function ($item) {
                    if ($item->status == 0) {
                        return '<span class="badge badge-soft-warning badge-border">Pending</span>';
                    }

                    if ($item->status == 1) {
                        return '<span class="badge badge-soft-primary badge-borders">In Progress</span>';
                    }
                    if ($item->status == 1) {
                        return '<span class="badge badge-soft-success badge-borders">Completed</span>';
                    }
                })

                ->addColumn('action', function ($item) {
                    $user = Auth::user();
                    $edit_route = route('orders.update.form', $item->ref_no);
                    $view_url = route('orders.view_details', $item->ref_no);

                    $actions = '';
                    $actions = action_btns($actions, $user, 'Order', $edit_route, $item->id,'',$view_url);

                    $action = '<div class="dropdown dropdown-action">
                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                    '</div></div>';

                    return $action;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);

            return $data;
        }

        return view('orders.index');
    }
    public function create_form()
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Order');

        if ($check_premission == false) {
            return abort(403);
        }

        return view('orders.create');
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

        $data = $this->order_repo->create($request);

        $data['status'] = true;
        $data['message'] = 'New Order Created Successfully!';
        $data['route'] = route('orders');

        return response()->json($data);
    }

    public function update_form($id)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_Order');

        if ($check_premission == false) {
            return abort(403);
        }

        $find_order = Order::where(['ref_no' => $id])->first();

        if (!$find_order) {
            return abort(404);
        }

        return view('orders.update',[
            'find_order' => $find_order
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

        $data = $this->order_repo->update($request);

        $data['route'] = route('orders');

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $data = $this->order_repo->delete($request);

        $data['route'] = route('orders');

        return response()->json($data);
    }

    public function view_details(Request $request, $ref_no)
    {
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Order');

        if ($check_premission == false) {
            return abort(403);
        }
        // End

        $orders = Order::Where(['ref_no' => $ref_no])->first();

        if (!$orders) {
            return abort(404);
        }

        return view('orders.view_details', [
            'orders' =>  $orders
        ]);
    }
}
