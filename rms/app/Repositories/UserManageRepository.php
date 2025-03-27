<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserBusiness;
use Illuminate\Support\Facades\Hash;



class UserManageRepository

{
    public function create_users($request,$role)
    {
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->name = $request->first_name.' '. $request->last_name;
        $user->email = $request->email;
        $user->contact = $request->contact;
        $user->status = $request->status == true ? 1 : 0;
        $user->password = Hash::make($request->password);
        $user->save();

        $ref_no = refno_generate(16, 2, $user->id);
        $user->ref_no = $ref_no;
        $user->update();


        $user->assignRole($role);

        //Check permission available or not
        if (isset($request->permissions) && !empty($request->permissions)) {
            $user->givePermissionTo($request->permissions);
        }


        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->name,
            'contact' => $user->contact,
            'email' => $user->email,
        ];
    }

    public function update_users($request)
    {
        $user = User::find($request->id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->name = $request->first_name.' '. $request->last_name;
        $user->email = $request->email;
        $user->contact = $request->contact;
        $user->status = $request->status == true ? 1 : 0;
        $user->update();


        //Check permission available or not
        if (isset($request->permissions) && !empty($request->permissions)) {
            $user->syncPermissions($request->permissions);
        }

        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->name,
            'contact' => $user->contact,
            'email' => $user->email,
        ];
    }

    public function delete_user($request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'User Not Found'
            ];
        }

        $user->delete();

        return [
            'status' => true,
            'message' => 'Selected User Deleted Successfully!'
        ];
    }

}
