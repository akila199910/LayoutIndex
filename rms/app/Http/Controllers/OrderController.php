<?php

namespace App\Http\Controllers;

use App\Models\Concession;
use App\Models\Order;
use App\Repositories\ConcessionRepository;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    private $order_repo;
    private $concession_repo;

    function __construct()
    {
        $this->order_repo = new OrderRepository();
        $this->concession_repo = new ConcessionRepository();
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
                ->addColumn('discount_amount', function ($item) {
                    $discount_amount = 'N/A';
                    if ($item->discount_amount ) {
                        $discount_amount = $item->discount_amount;
                    }
                    return $discount_amount;
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
                ->rawColumns(['action', 'status', 'discount_amount'])
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

        $concessions = Concession::where('status', 1)->get();

        return view('orders.create', [
            'concessions' => $concessions
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'concessions' => 'required|array|min:1',
                'concessions.*' => 'exists:concessions,id',
                'kitchen_time' => 'required',
            ],
            [
                'concessions.required' => 'Please select at least one item.',
                'concessions.*.exists' => 'Invalid concession selected.',
            ]
        );

            $concessions = $request->input('concessions', []);
            $quantities = $request->input('quantities', []);


            if ($validator->fails()) {
                return response()->json(['status' => false,  'message' => $validator->errors()]);
            }


            $total_price = 0;
            $concession_items = Concession::whereIn('id', $concessions)->get();

            foreach ($concession_items as $item) {
                $qty = $quantities[$item->id];
                $total_price += $item->price * $qty;
            }

            $request->merge([
                'total_price' => $total_price,
                'created_by' => Auth::user()->id,
                'concessions' => $concessions,
                'quantities' => $quantities
            ]);

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
        $concessions = Concession::where('status', 1)->get();


        if (!$find_order) {
            return abort(404);
        }

        return view('orders.update',[
            'find_order' => $find_order,
            'concessions' => $concessions,
            'selected_items' => $find_order->orderItems->keyBy('concession_id')
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'concessions' => 'required|array|min:1',
                'concessions.*' => 'exists:concessions,id',
                'kitchen_time' => 'required',
            ],
            [
                'concessions.required' => 'Please select at least one item.',
                'concessions.*.exists' => 'Invalid concession selected.',
            ]
        );

            $concessions = $request->input('concessions', []);
            $quantities = $request->input('quantities', []);


            if ($validator->fails()) {
                return response()->json(['status' => false,  'message' => $validator->errors()]);
            }


            $total_price = 0;
            $concession_items = Concession::whereIn('id', $concessions)->get();

            foreach ($concession_items as $item) {
                $qty = $quantities[$item->id];
                $total_price += $item->price * $qty;
            }

            $request->merge([
                'total_price' => $total_price,
                'concessions' => $concessions,
                'quantities' => $quantities
            ]);


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

    public function get_concessions(Request $request)
    {
        $concessions_item = $this->concession_repo->concessions_list($request)->first();

        return view('orders._concession_div', [
            'concessions_item' => $concessions_item
        ]);
    }
}
