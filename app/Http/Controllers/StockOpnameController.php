<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StockOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // VALIDATION DATE
        $validator = Validator::make(request()->all(), [
            'date' => 'required|date_format:Y-m-d',
        ]);
        
        if ($validator->fails()) {
            // RETURN ERROR VALIDATION
            return response()->json([
                "message" => "The given data was invalid.",
                "errors"  => $validator->errors()
            ], 422);
        }
        
        // GET LIST ITEMS
        $items = Item::select('id','name','category')->with([
            'stockOpname' => function($e){
                $e->where('date', request()->date);
            }
        ]);
        
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

        // MAPING DATA
        $items = $items->get()->map(function($e){
            return [
                "item_id"       => $e->id,
                "name"          => $e->name,
                "category"      => $e->category,
                "qty"           => $e->stockOpname->qty ?? 0,
                "current_qty"   => $e->storeStocks->sum("stock")
            ];
        });
        
        return response()->json([
            'message' => 'Successfully to get data',
            'data'    => $items
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // SET USER ID
        $request['user_id'] = Auth::user()->id;
        
        // UPDATE OR CREATE STOCK OPNAME
        collect($request->stock_opname)->map(function($e) use ($request){
            // STOCK OPNAME EXISTS
            $stockOpname = StockOpname::where([
                                'item_id' => $e['item_id'],
                                'date' => $request['date'],
                                'user_id' => $request['user_id'],
                            ])->first();

            // IF NOT EXISTS
            if(!$stockOpname){
                // CREATE NEW DATA
                $stockOpname = new StockOpname();
            }

            $stockOpname->item_id = $e['item_id'];
            $stockOpname->date = $request['date'];
            $stockOpname->user_id = $request['user_id'];
            $stockOpname->qty = $e['qty'];
            $stockOpname->save();
        });

        
        return response()->json([
            'message' => 'Successfully to store data',
            'data'    => true
        ]);
    }

}
