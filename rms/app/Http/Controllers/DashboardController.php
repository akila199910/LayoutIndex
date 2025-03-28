<?php

namespace App\Http\Controllers;

use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    private $order_repo;

    function __construct()
    {
        $this->order_repo = new OrderRepository();
    }
    public function index() {

        $today = now()->toDateString();
        $toDayTotalOrders = DB::table('orders')->where('deleted_at', NULL)->whereDate('kitchen_time', $today)->count();
        $toDayPendingOrders = DB::table('orders')->where('deleted_at', NULL)->where('status',0)->whereDate('kitchen_time', $today)->count();
        $toDayProgressOrders = DB::table('orders')->where('deleted_at', NULL)->where('status',1)->whereDate('kitchen_time', $today)->count();
        $toDayCompleteOrders = DB::table('orders')->where('deleted_at', NULL)->where('status',2)->whereDate('kitchen_time', $today)->count();

        $upComingTotalOrders = DB::table('orders')->where('deleted_at', NULL)->where('kitchen_time','>=',$today)->count();
        $upComingPendingOrders = DB::table('orders')->where('deleted_at', NULL)->where('status',0)->where('kitchen_time','>=',$today)->count();
        $upComingProgressOrders = DB::table('orders')->where('deleted_at', NULL)->where('status',1)->where('kitchen_time','>=',$today)->count();
        $upComingCompleteOrders = DB::table('orders')->where('deleted_at', NULL)->where('status',2)->where('kitchen_time','>=',$today)->count();

        $allTotalOrders = DB::table('orders')->where('deleted_at', NULL)->count();
        $allPendingOrders = DB::table('orders')->where('deleted_at', NULL)->where('status',0)->count();
        $allProgressOrders = DB::table('orders')->where('deleted_at', NULL)->where('status',1)->count();
        $allCompleteOrders = DB::table('orders')->where('deleted_at', NULL)->where('status',2)->count();





        return view('dashboard',[
            'toDayTotalOrders'=>$toDayTotalOrders,
            'toDayPendingOrders'=>$toDayPendingOrders,
            'toDayProgressOrders'=>$toDayProgressOrders,
            'toDayCompleteOrders'=>$toDayCompleteOrders,

            'upComingTotalOrders'=>$upComingTotalOrders,
            'upComingPendingOrders'=>$upComingPendingOrders,
            'upComingProgressOrders'=>$upComingProgressOrders,
            'upComingCompleteOrders'=>$upComingCompleteOrders,

            'allTotalOrders'=>$allTotalOrders,
            'allPendingOrders'=>$allPendingOrders,
            'allProgressOrders'=>$allProgressOrders,
            'allCompleteOrders'=>$allCompleteOrders
        ]);
    }

    public function getOrders(Request $request){

        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Order');

        if ($check_premission == false) {
            return abort(403);
        }


        if ($request->json) {

            $order = $this->order_repo->order_list($request);
            $orders = $order->sortBy('kitchen_time');

            $data =  DataTables::of($orders)
                ->addIndexColumn()

                ->addColumn('status', function ($item) {
                    if ($item->status == 0) {
                        return '<span class="badge badge-soft-warning badge-border">Pending</span>';
                    }

                    if ($item->status == 1) {
                        return '<span class="badge badge-soft-primary badge-borders">In Progress</span>';
                    }
                    if ($item->status == 2) {
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
                    $actions = action_btns($actions, $user, 'Order', $edit_route, $item->id,$item,$view_url);

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
    }
}
