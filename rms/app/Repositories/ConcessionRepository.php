<?php

namespace App\Repositories;

use App\Models\Concession;
use Illuminate\Support\Facades\Storage;

class ConcessionRepository
{

    public function concessions_list($request)
    {
        $concessions = Concession::all();
        if (isset($request->concession_id) && !empty($request->concession_id))
            $concessions = $concessions->where('id', $request->concession_id);

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
        $new_concession->description = $request->description;
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

   public function update($request)
   {

        $find_concession = Concession::find($request->id);
        $file = '';
        if (isset($request->image) && $request->image->getClientOriginalName()) {
            $file = resize_file_upload($request->image, 'concessions', 500, 500);
        }
        else
        {
            if (!$find_concession->image)
                $file = '';
            else
                $file = $find_concession->image;
        }

        $find_concession->name = $request->name;
        $find_concession->image = $file;
        $find_concession->price = $request->price;
        $find_concession->status = $request->status==true ? 1 : 0;
        $find_concession->description = $request->description;
        $find_concession->update();

        return [
            'status' => true,
            'message' => 'Selected Concession Updated Successfully!'
        ];

   }

   public function delete($request)
   {

        $concession = Concession::find($request->id);

        if (!$concession) {
            return [
                'status' => false,
                'message' => 'Location Not Found'
            ];
        }

        $concession->delete();

        return [
            'status' => true,
            'message' => 'Selected Concession Deleted Successfully!'
        ];
}


   public function get_details($request)
    {
        $concession = Concession::find($request->id);

        $data = [
            'name' => $concession->name,
            'image' => $concession->image,
            'price' => $concession->price,
            'description' => $concession->description,
            'status_name' => $concession->status == 1 ? 'Active' : 'Inactive',
        ];

        return $data;
    }
}
