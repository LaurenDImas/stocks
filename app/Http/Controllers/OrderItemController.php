<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\OrderItem;
use App\Models\OrderItemDetail;
use App\Models\StoreStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        // GET LIST ITEMS
        $items = Item::select('id','name','category');
        
        // IF BY CATEGORY
        $items->when(request()->category, function($e){
            $e->where('category', request()->category);
        });

        // IF SEARCH
        $items->when(request()->search, function($e){
            $e->where('name','LIKE','%'.request()->search.'%')
              ->orWhere('barcode','LIKE','%'.request()->search.'%')
              ->orWhere('category','LIKE','%'.request()->search.'%');
        });
        
        return response()->json([
            'message' => 'Successfully to get data',
            'data'    => $items->get()
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function history()
    {   
        // GET LIST ORDER
        $orderItems = OrderItem::query();
        
        // SHOW BY STORE ID
        $orderItems->when(Auth::user()->role !== "admin", function($e){
            $e->whereHas('user', function($e){
                $e->where('store_id', Auth::user()->store_id);
            });
        });

        // SHOW BY ORDER ID
        $orderItems->when(request()->order_id, function($e){
            $e->where('order_id', 'LIKE', '%'.request()->order_id.'%');
        });
        
        // SHOW BY TYPE
        $orderItems->when(request()->type, function($e){
            // SHOW DATA TITO IF ORDER HAVE STORE_ID
            if(request()->type == 'tito'){
                $e->whereNotNull('store_id');
            }else if(request()->type){
                $e->whereNull('store_id');
            }
        });

        // SHOW BY STATUS
        $orderItems->when(request()->status, function($e){
            // SHOW DATA TITO IF ORDER HAVE STORE_ID
            if(request()->status == 'ongoing'){
                $e->where('status','On Queue');
            }else if(request()->status == 'to_receive'){
                $e->where('status','In Transit');
            }
        });
                            
        // MAPING DATA
        $orderItems = $orderItems->orderBy('date', request()->type == "oldest" ? "ASC" : 'DESC')->get();

        $orderItems = $orderItems->map(function($orderItem){
            return $this->mapping($orderItem);
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
            'date'                     => 'required|date_format:Y-m-d',
            'item_details.*.item_id'   => 'required',
            'item_details.*.qty'       => 'required',
        ]);
        
        if ($validator->fails()) {
            // RETURN ERROR VALIDATION
            return response()->json([
                "message" => "The given data was invalid.",
                "errors"  => $validator->errors()
            ], 422);
        }

        // SET DATA
        $request['order_id']    = $this->checkOrderID(random_int(100, 9999999999));
        $request['date']        = $request['date'];
        $request['user_id']     = Auth::user()->id;
        $request['full_fill_by']     = Auth::user()->store_id;
        // SAVE ITEM
        $saveOrderItem = OrderItem::create([
            "order_id"  => $request['order_id'],
            "date"      => $request['date'],
            "user_id"   => $request['user_id'],
            "full_fill_by"   => $request['full_fill_by'],
        ]);
        
        // SET ORDER ITEM ID FOR RESULTS
        $orderItemID = $saveOrderItem->id;

        if($saveOrderItem){
            // SAVE ITEM DETAIL ITEM
            $saveOrderItem->orderItemDetails()->createMany($request['order_item_details']);
        }

        // Result Order Detail
        $orderItem = OrderItem::find($orderItemID);

        
        return response()->json([
            'message' => 'Successfully to store data',
            'data'    => $this->mapping($orderItem),
        ]);
    }

     /**
     * Detail Order Item.
     */
    public function update($orderItemID)
    {
        // GET ORDER ITEM DATA
        $orderItem = OrderItem::with('orderItemDetails')->where('status', 'In Transit')->find($orderItemID);

        if(!$orderItem){
            return response()->json([
                'message' => 'Order items not valid',
                'errors'  => $orderItem
            ], 500);
        }

        $orderItem->update([
            "status" => "Finished",
        ]);
        
        foreach ($orderItem->orderItemDetails ?? [] as $value) {
            // STOCK EXISTS
            $storeStock = StoreStock::where([
                "order_item_detail_id" => $value->id,
            ])->first();

            // IF NOT EXISTS
            if(!$storeStock){
                // CREATE NEW DATA
                $storeStock = new StoreStock();
            }

            $storeStock->store_id = Auth::user()->store_id;
            $storeStock->order_item_detail_id = $value->id;
            $storeStock->item_id = $value->item_id;
            $storeStock->stock = $value->qty;
            $storeStock->arrived_date = request()->arrived_date;
            $storeStock->save();
        }

        
        if($orderItem->store_id){
            foreach ($orderItem->orderItemDetails ?? [] as $value) {
                // STOCK EXISTS
                $storeStock = StoreStock::find($value->store_stock_id);

                $storeStock->stock = $storeStock->stock - $value->qty;
                $storeStock->save();
            }
        }
        
        return response()->json([
            'message' => 'Successfully to store data',
            'data'    => $orderItem
        ]);
    }


    /**
     * MAPPING DATA 
     */
    public function mapping($orderItem){
        return [
            "id"            => $orderItem->id,
            "date"          => $orderItem->date,
            'order_status'  => $orderItem->status,
            'order_id'      => $orderItem->order_id,
            'order_by'      => $orderItem->user->store->name,
            'requestor'     => $orderItem->user->name,
            'full_fill_by'  => "Gudang",
            "order_item_details"    => $orderItem->orderItemDetails->map(function($e) use ($orderItem){
                return [
                    'item'          => $e->item->name,
                    'qty'           => $e->qty,
                    'category'      => $e->item->category,
                    'unit'      => $e->item->unit,
                ];
            })  
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
