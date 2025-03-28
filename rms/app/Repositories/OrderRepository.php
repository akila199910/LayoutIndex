<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class OrderRepository
{

    public function order_list($request)
    {
        $today = now()->toDateString();

        $orders = Order::all();

        if ($request->day == 'today') {

            $orders = $orders->filter(function ($order) use ($today, $request) {
                $isToday = Carbon::parse($order->kitchen_time)->toDateString() === $today;

                if (isset($request->status) && $request->status !== '') {
                    return $isToday && $order->status == $request->status;
                }

                return $isToday;
            });
        }

        if ($request->day == 'upcoming') {
            $orders = $orders->filter(function ($order) use ($request) {
                $isUpcoming = Carbon::parse($order->kitchen_time)->isAfter(Carbon::today());

                if (isset($request->status) && $request->status !== '') {
                    return $isUpcoming && $order->status == $request->status;
                }

                return $isUpcoming;
            });
        }


        if ($request->day == 'all') {

            if (isset($request->status) && $request->status !== '') {
                $orders = $orders->where('status', $request->status);
            }
        }

        return $orders;
    }

    public function create($request)
    {

        $new_order = new Order();
        $new_order->total_price = $request->total_price;
        $new_order->kitchen_time = $request->kitchen_time;
        $new_order->status = 0;
        $new_order->created_by = $request->created_by;
        $new_order->discount_amount = $request->discount_amount;
        $new_order->save();

        $ref_no = refno_generate(16, 2, $new_order->id);
        $new_order->ref_no = $ref_no;
        $new_order->update();

        for ($i = 0; $i < count($request->concessions); $i++) {
            $concession_id = $request->concessions[$i];
            $quantity = $request->quantities[$concession_id] ?? null;

            if ($quantity !== null && $quantity > 0) {
                $order_concession = new OrderItem();
                $order_concession->order_id = $new_order->id;
                $order_concession->concession_id = $concession_id;
                $order_concession->qty = $quantity;
                $order_concession->save();
            }
        }



        return [
            'id' => $new_order->id,
            'total_price' => $new_order->total_price,
            'ref_no' => $new_order->ref_no,
            'kitchen_time' => $new_order->kitchen_time,
            'status' => $new_order->status,
            'created_by' => $new_order->created_by,
            'discount_amount' => $new_order->discount_amount
        ];

   }

   public function update($request)
   {

        $find_order = Order::find($request->id);

        OrderItem::where('order_id', $find_order->id)
        ->whereNotIn('concession_id', $request->concessions)
        ->delete();

        $find_order->total_price = $request->total_price;
        $find_order->kitchen_time = $request->kitchen_time;
        $find_order->status = $request->status;
        $find_order->discount_amount = $request->discount_amount;
        $find_order->update();

        for ($i = 0; $i < count($request->concessions); $i++) {
            $concession_id = $request->concessions[$i];
            $quantity = $request->quantities[$concession_id] ?? null;

            if ($quantity !== null && $quantity > 0) {
                $order_concession =  OrderItem::where(['order_id' => $find_order->id, 'concession_id' => $concession_id])->first();

                if (!$order_concession) {
                    $order_concession = new OrderItem();
                    $order_concession->order_id = $find_order->id;
                    $order_concession->concession_id = $concession_id;
                    $order_concession->qty = $quantity;
                    $order_concession->save();

                }

                $order_concession->order_id = $find_order->id;
                $order_concession->concession_id = $concession_id;
                $order_concession->qty = $quantity;
                $order_concession->update();
            }
        }

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
