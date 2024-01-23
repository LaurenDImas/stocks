<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\OutItem;
use App\Models\StoreStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OutItemController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function scanBarcode($barcode)
    {
        $item = Item::where('barcode', $barcode)->first();

        
        if (!$item) {
            return response()->json([
                'message' => 'Barang tidak tersedia',
                'data'    => null
            ],500);
        }

        // GET STOCK
        $items = StoreStock::join('items', 'items.id', '=', 'store_stocks.item_id')
                    ->where('store_stocks.store_id', Auth::user()->store_id)
                    ->where('items.barcode', $barcode)
                    ->orderBy('store_stocks.arrived_date','ASC')
                    // ->when($item->category, function($e) use ($item){
                    //     if($item->category == "Packaging"){
                    //         $e->orderBy('store_stocks.arrived_date','ASC');
                    //     }else{
                    //         $e->orderBy('store_stocks.expired_date','ASC');
                    //     }
                    // })
                    ->get([
                        'store_stocks.id',
                        'store_stocks.item_id',
                        'items.name',
                        'items.barcode',
                        'store_stocks.stock as in_stock',
                        'store_stocks.arrived_date as date',
                    ]);

        if (!$items) {
            return response()->json([
                "message" => "The given data was invalid.",
                "errors"  => "Item doesn't exists"
            ],500);
        }


        return response()->json([
            'message' => 'Successfully get data',
            'item'    => [
                "name"     => $item->name,
                "category" => $item->category,
                "unit" => $item->unit,
            ],
            'data'    => $items,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        $validator = Validator::make(request()->all(), [
            '*.id'   => 'required',
            '*.in_stock'  => 'required|numeric|min:1',
            '*.out_stock' => 'required|numeric|lte:*.in_stock',
        ]);

        if ($validator->fails()) {
            // RETURN ERROR VALIDATION
            return response()->json([
                "message" => "The given data was invalid.",
                "errors"  => $validator->errors()
            ], 422);
        }

        // ORDER DATA ASC
        $datas = collect($request->all())->where('in_stock','>',0)->sortBy("date");
        
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

        // IF FIFO SUCCESS THEN UPDATE STOCK
        foreach ($datas as $value) {
            $storeStock = StoreStock::find($value['id']);
            if($storeStock){
                $storeStock->update([
                    "stock" => $storeStock->stock - $value['out_stock']
                ]);
            }
        }   

        return response()->json([
            'message' => 'Successfully to save data',
            'data' => true
        ]);
    }
}
