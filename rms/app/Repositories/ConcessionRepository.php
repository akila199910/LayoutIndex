<?php

namespace App\Repositories;

use App\Models\Concession;
use Illuminate\Support\Facades\Storage;

class ConcessionRepository
{

    public function concessions_list($request)
    {
        $concessions = Concession::all();

        return $concessions;
    }

    public function create($request)
    {

        $file = '';
        if (isset($request->image) && $request->image->getClientOriginalName()) {
            $file = resize_file_upload($request->image, 'concessions', 500, 500);
        }

        $new_concession =new Concession();
        $new_concession->name = $request->name;
        $new_concession->price = $request->price;
        $new_concession->image = $file;
        $new_concession->status = $request->status == true ? 1 : 0;
        $new_concession->save();

        $ref_no = refno_generate(16, 2, $new_concession->id);
        $new_concession->ref_no = $ref_no;
        $new_concession->update();

        return [
            'id' => $new_concession->id,
            'name' => $new_concession->name,
            'image' => $file
        ];

   }

//    public function update($request)
//    {

//         $category=Category::find($request->id);

//         $file = '';
//         if (isset($request->image) && $request->image->getClientOriginalName()) {
//             // $file = file_upload($request->image, 'categories');
//             $file = resize_file_upload($request->image, 'categories', 500, 500);
//         }
//         else
//         {
//             if (!$category->image)
//                 $file = '';
//             else
//                 $file = $category->image;
//         }

//         $category->name = $request->name;
//         $category->image = $file;
//         $category->business_id= $request->business_id;
//         $category->status = $request->status==true ? 1 : 0;
//         $category->update();

//         return [
//             'status' => true,
//             'message' => 'Selected Category Updated Successfully!'
//         ];

//    }

//    public function delete($request)
//    {

//         $category = Category::find($request->id);

//         if (!$category) {
//             return [
//                 'status' => false,
//                 'message' => 'Location Not Found'
//             ];
//         }

//         $category->delete();

//         return [
//             'status' => true,
//             'message' => 'Selected Category Deleted Successfully!'
//         ];
// }


//    public function get_details($request)
//     {
//         $category = Category::find($request->id);

//         $data = [
//             'name' => $category->name,
//             'status' => $category->status,
//             'status_name' => $category->status == 1 ? 'Active' : 'Inactive',
//         ];

//         return $data;
//     }
}
