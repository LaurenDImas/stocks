<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    /**
     * Get a List
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
     public function index()
     {
        // GET ITEMS DATA
        $items = Item::get();
        
        return response()->json([
            'message'      => "Successfully get data",
            'data' => $items
         ]);
     }

    /**
     * Register a Item.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // VALIDATION FORM
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:items,name',
            'barcode'   => 'required|unique:items,barcode',
            'category'  => 'required',
            'unit'      => 'required',
        ]);
        
        if ($validator->fails()) {
            // RETURN ERROR VALIDATION
            return response()->json([
                "message" => "The given data was invalid.",
                "errors"  => $validator->errors()
            ], 422);
        }
        
        // CREATE URSER
        $item = Item::create($validator->validated());

        return response()->json([
            'message' => 'Successfully registered',
            'data' => $item
        ], 201);
    }

     /**
     * Update a Item.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // VALIDATION FORM
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:items,name,'.$id,
            'barcode'   => 'required|string|unique:items,barcode,'.$id,
            'category'  => 'required',
            'unit'      => 'required',
        ]);
        
        if ($validator->fails()) {
            // RETURN ERROR VALIDATION
            return response()->json([
                "message" => "The given data was invalid.",
                "errors"  => $validator->errors()
            ], 422);
        }
        
        // GET ITEM
        $item = Item::find($id);

        // UPDATE URSER
        $update = $item->update($validator->validated());

        return response()->json([
            'message' => 'Successfully updated',
            'data' => $update
        ], 201);
    }
    
    /**
     * Get the Item by ID.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // GET PROFILE ITEM
        $item = Item::find($id);
        return response()->json([
            'message' => 'Successfully signed out',
            'data'    => $item
        ]);
    }

    /**
     * DELETE ITEM.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function destroy($id)
    {
        // DELETE
        $item = Item::find($id)->delete();
        return response()->json([
            'message' => 'Successfully signed out',
            'data'    => $item
        ]);
    }
}
