<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserManageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    private $user_repo;

    function __construct()
    {
        $this->user_repo = new UserManageRepository();
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_User');

        if ($check_premission == false) {
            return abort(403);
        }
        //End

        if (isset($request->json)) {


            $users = User::role('user');
            if(auth()->user()->hasRole('user'))
                $users = $users->where('id','!=',auth()->user()->id);

            $data =  DataTables::of($users)
                ->addIndexColumn()

                ->addColumn('status', function ($item) {
                    if ($item->status == 0) {
                        return '<span class="badge badge-soft-danger badge-border">Inactive</span>';
                    }

                    if ($item->status == 1) {
                        return '<span class="badge badge-soft-success badge-border">Active</span>';
                    }
                })

                ->addColumn('permissions', function ($item) {
                    $permissions = $item->getDirectPermissions()->pluck('name')->toArray();


                    $data = '';
                    foreach ($permissions as $perm) {
                        // Replace underscores with spaces and correct specific permission names
                        $formatted_perm = str_replace('_', ' ', $perm);
                        // Add formatted permission to dropdown
                        $data .= '<a class="dropdown-item" style="cursor: none;" href="javascript:;">' . $formatted_perm . '</a>';
                    }

                    return '<div class="dropdown action-label scrollbar">
                                <a class="custom-badge status-purple dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false" >
                                    View Permissions
                                </a>
                                <div class="dropdown-menu dropdown-menu-end status-staff" style="max-height: 200px; position: relative;overflow: hidden;width: 100%;overflow-y: scroll;">
                                    ' . $data . '
                                </div>
                            </div>';
                })
                ->addColumn('action', function ($item) {
                    $user = Auth::user();
                    $edit_url = route('users.update.form', $item->ref_no);
                    $view_url = route('users.view_details', $item->ref_no);

                    $actions = '';
                    $item_cancel = new User();
                    $item_cancel->status = 5;

                    $actions .= action_btns($actions, $user, 'User', $edit_url, $item->id, $item_cancel, $view_url);

                    $action = '<div class="dropdown dropdown-action">
                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                        '</div></div>';

                    return $action;
                })
                ->rawColumns(['action', 'status', 'profile', 'permissions'])
                ->make(true);

            return $data;
        }

        return view('users.index');
    }

    public function create_form(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Create_User');

        if ($check_premission == false) {
            return abort(403);
        }
        //END

        $action = ['Read', 'Create', 'Update', 'Delete'];
        $permissions = [
            'User',
            'Order',
            'Concession',
        ];

        $permission_list = [];

        foreach ($permissions as $perm) {
            $permission_list[$perm] = [];
            foreach ($action as $act) {
                $permission_list[$perm][] = $act . '_' . $perm;
            }
        }

        $permission_list['Order'][] = 'Approve_Order';
        $permission_list['Order'][] = 'Complete_Order';

        return view('users.create', [
            'permissions' => $permissions,
            'permission_list' => $permission_list
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                'last_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                'email' => 'required|email|max:190|unique:users,email,NULL,id,deleted_at,NULL',
                'contact' => 'required|digits:10|unique:users,contact,NULL,id,deleted_at,NULL',
                'permissions' => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }


        $request->merge([
            'password' => 'User@1234'
        ]);

        $data = $this->user_repo->create_users($request, 'user');

        $data['status'] = true;
        $data['message'] = 'New User Created Successfully!';
        $data['route'] = route('users');

        return response()->json($data);
    }
    public function update_form(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_User');

        if ($check_premission == false) {
            return abort(403);
        }
        //END

        $user = User::where('ref_no', $ref_no)->first();

        if (!$user) {
            return abort(404);
        }

        //check user exist with current location

        $action = ['Read', 'Create', 'Update', 'Delete'];
        $permissions = [
            'User',
            'Order',
            'Concession',
        ];

        $permission_list = [];

        foreach ($permissions as $perm) {
            $permission_list[$perm] = [];
            foreach ($action as $act) {
                $permission_list[$perm][] = $act . '_' . $perm;
            }
        }

        $permission_list['Order'][] = 'Approve_Order';
        $permission_list['Order'][] = 'Complete_Order';

        $user_permission = $user->getDirectPermissions()->pluck('name')->toArray();

        return view('users.update', [
            // 'action' => $action,
            'permissions' => $permissions,
            'permission_list' => $permission_list,
            'user_permission' => $user_permission,
            'user' => $user
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                'last_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                'email' => 'required|email|max:190|unique:users,email,' . $id . ',id,deleted_at,NULL',
                'contact' => 'required|digits:10|unique:users,contact,' . $id . ',id,deleted_at,NULL',
                'permissions' => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $data = $this->user_repo->update_users($request);

        $data['status'] = true;
        $data['message'] = 'Selected User Updated Successfully!';
        $data['route'] = route('users');

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $data = $this->user_repo->delete_user($request);

        $data['route'] = route('business.users');

        return response()->json($data);
    }

    public function view_details(Request $request, $ref_no)
{
    $user = Auth::user();
    $check_permission = user_permission_check($user, 'Read_User');

        if ($check_permission == false) {
            return abort(403);
        }

    $users = User::role('user')->where('ref_no', $ref_no)->first();

    if (!$users) {
        return abort(404);
    }

    $user_permission = $users->getDirectPermissions()->pluck('name')->toArray();

    return view('users.view_details', [
        'users' => $users,
        'user_permission' => $user_permission
    ]);
}
}
