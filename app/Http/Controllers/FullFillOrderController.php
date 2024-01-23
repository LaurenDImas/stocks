<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\OrderItem;
use App\Models\OrderItemDetail;
use App\Models\StoreStock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FullFillOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // GET LIST ORDER
        $orderItems = OrderItem::query();
        
        
        $orderItems->when(Auth::user()->role !== "admin", function($e){
            $e->where('store_id', Auth::user()->store_id);
        });

        $orderItems = $orderItems->when(request()->order_id, function($e){
                            $e->where('order_id', 'LIKE', '%'.request()->order_id.'%');
                        })->where('status','!=','Finished');
                            
        // MAPING DATA
        $orderItems = $orderItems->orderBy('date', request()->order ?? 'ASC')->get();

        $orderItems = $orderItems->map(function($orderItem){
            return [
                "id"            => $orderItem->id,
                "date"          => $orderItem->date,
                'order_status'  => "Waiting for confirmation",
                'order_id'      => $orderItem->order_id,
                'order_by'      => $orderItem->user->store->name,
                'requestor'     => $orderItem->user->name,
                'full_fill_by'  => "Gudang",
                "order_item_details"    => $orderItem->orderItemDetails->map(function($orderItemDetail){
                    return [
                        'item'          => $orderItemDetail->item->name,
                        'qty'           => $orderItemDetail->qty,
                        'category'      => $orderItemDetail->item->category,
                        'unit'          => $orderItemDetail->item->unit,
                    ];
                })  
            ];
        });
        return response()->json([
            'message' => 'Successfully to get data',
            'data'    => $orderItems
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         // VALIDATION FORM
        $validator = Validator::make($request->all(), [
            '*.id'  => 'required',
        ]);
        
        if ($validator->fails()) {
            // RETURN ERROR VALIDATION
            return response()->json([
                "message" => "The given data was invalid.",
                "errors"  => $validator->errors()
            ], 422);
        }

        // UPDATE OR CREATE STOCK OPNAME
        foreach($request->all() as $e){
            // GET ORDER ITEM BY ID
            $orderItem = OrderItem::find($e['id']);
            $orderItem->status = 'In Transit';

            // SAVE ORDER ITEM
            $orderItem->save();
        }

        
        return response()->json([
            'message' => 'Successfully to store data',
            'data'    => true
        ]);
    }

}
