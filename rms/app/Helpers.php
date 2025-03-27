<?php

use App\Models\Business;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\ClientException;

if (!function_exists('getClientIdAndSecret')) {

    function getClientIdAndSecret($provider)
    {
        $client = DB::table('oauth_clients')->where('provider', $provider)->first();
        return $client;
    }
}

if (!function_exists('file_upload')) {

    function file_upload($file, $path)
    {
        $path_store = Storage::disk('s3')->put($path, $file);

        return $path_store;
    }
}

if (!function_exists('resize_file_upload')) {

    function resize_file_upload($upload_file, $path, $height, $width)
    {
        // Ensure the directory exists in the storage path
        $storageDirectory = storage_path('app/resize_image');
        if (!File::exists($storageDirectory)) {
            File::makeDirectory($storageDirectory, 0755, true);
        }

        $image = $upload_file;
        $image_name = time() . '.' . $image->getClientOriginalExtension();
        $filePath = $storageDirectory . '/' . $image_name;

        // Resize and save the image in the storage path
        $resize_image = Image::make($image->getRealPath());
        $resize_image->resize($height, $width)->save($filePath);

        $s3Path = $path . '/' . $image_name;
        $path_store = Storage::disk('s3')->put($s3Path, file_get_contents($filePath));

        if ($path_store) {
            File::delete($filePath); // Delete the local file
        }

        // Get the S3 URL of the uploaded file

        return $s3Path;
    }
}


if (!function_exists('mailNotification')) {
    function mailNotification($data)
    {
        Mail::send($data["view"], $data, function ($message) use ($data) {
            $message->to($data["email"])
                ->subject($data["title"]);
        });
    }
}

if (!function_exists('mailNotificationAttach')) {
    function mailNotificationAttach($data, $pdf)
    {
        Mail::send($data["view"], $data, function ($message) use ($data, $pdf) {
            $message->to($data["email"])
                ->subject($data["title"])
                ->attachData($pdf, "attachment.pdf");
        });
    }
}

if (!function_exists('action_buttons')) {
    function action_buttons($action, $edit_url, $route_id, $view_url)
    {
        if ($edit_url != '') {
            $action .= '<a href="' . $edit_url . '" class="dropdown-item" title="Edit"><i class="fa-solid fa-pen-to-square m-r-5"></i>Edit</a> ';
        }

        if (Auth()->user()->hasRole('super_admin')) {
            $action .= '<button type="button" class="dropdown-item" title="Delete" onclick="deleteConfirmation(' . $route_id . ')" data-id="' . $route_id . '"><i class="fa-solid fas fa-trash m-r-5"></i>Delete</button>  ';
        }

        if ($view_url != '') {
            $action .= '<a class="dropdown-item" title="View"  href="' . $view_url . '"><i class="fa-solid fa-eye m-r-5"></i>View</a>';
        }

        return $action;
    }
}

if (!function_exists('refno_generate')) {

    function refno_generate($length, $type, $id)
    {
        // 0 = Digits
        if ($type == 0) {
            $pool = '0123456789';
        }

        // 1 = Letter Only
        if ($type == 1) {
            $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        // 2 = Digit and Letter
        if ($type == 2) {
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $id_length = strlen($id);
        $ref_length = $length - $id_length;

        $ref_no = $id;
        if ($ref_length > 0) {
            $otp = substr(str_shuffle(str_repeat($pool, $ref_length)), 0, $ref_length);
            $ref_no = $otp . $id;
        }

        return $ref_no;
    }
}


if (!function_exists('action_btns')) {
    function action_btns($action, $user, $permission, $edit_url, $route_id, $item,  $view_url)
    {
        if ($edit_url != '' && $user->hasPermissionTo('Update_' . $permission)) {
            $action .= '<a class="dropdown-item" title="Edit" href="' . $edit_url . '"><i class="fa-solid fa-pen-to-square m-r-5"></i> Edit</a>';
        }

        if ($user->hasPermissionTo('Delete_' . $permission)) {
            $action .= '<a class="dropdown-item" title="Delete" href="javascript:;" onclick="deleteConfirmation(' . $route_id . ')" data-id="' . $route_id . '"><i class="fa-solid fas fa-trash m-r-5"></i> Delete</a>';
        }

        if ($user->hasPermissionTo('Create_SendToKitchen') && $item->status == 0) {
            $action .= '<a class="dropdown-item" title="Approve" href="javascript:;" onclick="change_status(' . $route_id . ',1)" data-id="' . $route_id . '"><i class="fa-solid fas fa-check m-r-5"></i> Send To Kitchen</a>';
        }

        if ($user->hasPermissionTo('Read_' . $permission) && $view_url != '') {
            $action .= '<a class="dropdown-item" title="View" href="' . $view_url . '"><i class="fa-solid fa-eye m-r-5"></i> View</a>';
        }

        return $action;
    }
}


if (!function_exists('user_permission_check')) {
    function user_permission_check($user, $permission)
    {
        $status = false;

        if ($user->hasPermissionTo($permission)) {
            $status = true;
        }

        return $status;
    }
}
