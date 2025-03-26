<?php

namespace App\Http\Controllers;

use App\Repositories\ConcessionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                    $actions = action_btns($actions, $user, 'Concession', $edit_route, $item->id,'',$view_url);

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
}
