<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\StoreStock;
use App\Models\TransferInOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransferInOutController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function stores()
    {
        // GET LIST STORE
        $stores = Store::query();

        // IF SEARCH
        $stores->when(request()->search, function($e){
            $e->where('name','LIKE','%'.request()->search.'%');
        })->where('id',"!=", Auth::user()->store_id);

        $stores = $stores->orderBy('name', request()->order ?? "ASC")->get();

        return response()->json([
            'message'      => "Successfully get data",
            'data' => $stores
         ]);
     }

     
    /**
     * Display a listing of the resource.
     */
    public function items($storeID)
    {
        // GET LIST ITEMS
        $items = StoreStock::join('items', 'items.id', '=', 'store_stocks.item_id')
                    ->where('store_stocks.store_id', $storeID)
                    ->where('store_stocks.stock','>', 0)
                    ->when(request()->category, function($e){
                        $e->where('items.category', request()->category);
                    })
                    ->get([
                        'store_stocks.id',
                        'store_stocks.item_id',
                        'items.name',
                        'items.barcode',
                        'store_stocks.stock as in_stock',
                        'store_stocks.arrived_date as date',
                    ]);
        
        $itemsGroup = [];
        foreach ($items as $key => $value) {
            $itemsGroup[$value['item_id']]["name"] = $value['name'];
            $test[$value['item_id']]["data"][] =  collect($value)->sortBy('date') ;

            // SORT ASC BY ARRIVED DATE
            usort($test[$value['item_id']]["data"], function($a, $b) {
                return $a['date'] <=> $b['date'];
            });

            $itemsGroup[$value['item_id']]["data"] = $test[$value['item_id']]["data"];
        }

        return response()->json([
            'message' => 'Successfully to get data',
            'data'    => array_values($itemsGroup)
        ]);   
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'store_id'          => 'required',
            'date_order'        => 'required',
            'items.*.id'        => 'required',
            'items.*.item_id'   => 'required',
            'items.*.date'      => 'required|date_format:Y-m-d',
            'items.*.in_stock'  => 'required|numeric|min:1',
            'items.*.out_stock' => 'required|numeric|lte:items.*.in_stock',
        ]);

        if ($validator->fails()) {
            // RETURN ERROR VALIDATION
            return response()->json([
                "message" => "The given data was invalid.",
                "errors"  => $validator->errors()
            ], 422);
        }

        // ORDER DATA ASC
        $datas = collect($request->items)->where('in_stock','>',0)->sortBy("date");
        // FIFO LOGIC
        $checkFifo  = true;
        $totalData  = count($datas);
        foreach ($datas as $key => $value) {
            if($totalData > 1 && $key != $totalData-1){
                if($value['out_stock'] < $value['in_stock']){
                    $checkFifo = false;
                    break;
                }
            }
        }

        // IF CHECK FIFO FALSE
        if (!$checkFifo) {
            // RETURN ERROR VALIDATION
            return response()->json([
                "message" => "The given data was invalid.",
                "errors"  => "HARAP GUNAKAN YANG LAMA TERLEBIH DAHULU"
            ], 500);
        }
        
        // IF FIFO SUCCESS THEN STORE TO ORDER ITEMS
        // SET DATA
        $order['order_id']    = $this->checkOrderID(random_int(100, 9999999999));
        $order['store_id']    = $request['store_id'];
        $order['date']        = $request['date_order'];
        $order['user_id']     = Auth::user()->id;
        $order['full_fill_by']     = Auth::user()->store_id;
        
        // SET DATA FOR ORDER ITEM DETAILS 
        $orderItemDetail      = $datas->map(function($e){
            return [
                'store_stock_id'    => $e['id'],
                'item_id'           => $e['item_id'],
                'qty'               => $e['out_stock'],
            ];
        })->toArray();

        // SAVE ITEM
        $saveOrderItem = OrderItem::create($order);
        
        // SET ORDER ITEM ID FOR RESULTS
        $orderItemID = $saveOrderItem->id;

        if($saveOrderItem){
            // SAVE ITEM DETAIL ITEM
            $saveOrderItem->orderItemDetails()->createMany($orderItemDetail);
        }

        // Result Order Detail
        $orderItem = OrderItem::find($orderItemID);
          

        return response()->json([
            'message' => 'Successfully to save data',
            'data' => $this->mapping($orderItem)
        ]);
    }

    /**
     * MAPPING DATA 
     */
    public function mapping($orderItem){
        $orderItemDetails = $orderItem->orderItemDetails[0];
        return [
            "id"                    => $orderItem->id,
            "date"                  => $orderItem->date,
            'order_status'          => $orderItem->status,
            'order_id'              => $orderItem->order_id,
            'order_by'              => $orderItem->user->store->name,
            'requestor'             => $orderItem->user->name,
            'full_fill_by'          => $orderItem->store->name,
            "order_item_details"    => [
                [
                    'item'          => $orderItemDetails->item->name,
                    'qty'           => collect($orderItem->orderItemDetails)->sum('qty'),
                    'category'      => $orderItemDetails->item->category,
                    'unit'      => $orderItemDetails->item->unit,
                ]
            ]
        ];
    }

    
    // GENERATE ORDER ID
    private function checkOrderID($orderID){
        $orderItem = OrderItem::where('order_id', $orderID)->exists();
        if($orderItem){
            $orderID  = random_int(100, 9999999999999);
            return $this->checkOrderID($orderID);
        }

        return $orderID;
    }
}
