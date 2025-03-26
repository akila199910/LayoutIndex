<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class OrderRepository
{

    public function order_list($request)
    {
        $orders = Order::all();

        return $orders;
    }

    public function create($request)
    {

        $new_order = new Order();
        $new_order->total_price = $request->total_price;
        $new_order->kitchen_time = $request->kitchen_time;
        $new_order->status = 0;
        $new_order->save();

        $ref_no = refno_generate(16, 2, $new_order->id);
        $new_order->ref_no = $ref_no;
        $new_order->update();

        return [
            'id' => $new_order->id,
            'total_price' => $new_order->total_price,
            'ref_no' => $new_order->ref_no,
            'kitchen_time' => $new_order->kitchen_time
        ];

   }

   public function update($request)
   {

        $find_order = Order::find($request->id);

        $find_order->total_price = $request->total_price;
        $find_order->kitchen_time = $request->kitchen_time;
        $find_order->status = $request->status;
        $find_order->update();

        return [
            'status' => true,
            'message' => 'Selected Order Updated Successfully!'
        ];

   }

   public function delete($request)
   {

        $concession = Order::find($request->id);

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
        $concession = Order::find($request->id);

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
