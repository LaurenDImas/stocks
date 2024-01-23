<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    /**
     * Get a List
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
     public function index()
     {
        // GET STORES DATA
        $stores = Store::get();
        
        return response()->json([
            'message'      => "Successfully get data",
            'data' => $stores
         ]);
     }

    /**
     * Register a Store.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // VALIDATION FORM
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100|unique:stores,name',
        ]);
        
        if ($validator->fails()) {
            // RETURN ERROR VALIDATION
            return response()->json([
                "message" => "The given data was invalid.",
                "errors"  => $validator->errors()
            ], 422);
        }
        
        // CREATE URSER
        $store = Store::create($validator->validated());

        return response()->json([
            'message' => 'Successfully registered',
            'data' => $store
        ], 201);
    }

     /**
     * Update a Store.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // VALIDATION FORM
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100|unique:stores,name,'. $id,
        ]);
        
        if ($validator->fails()) {
            // RETURN ERROR VALIDATION
            return response()->json([
                "message" => "The given data was invalid.",
                "errors"  => $validator->errors()
            ], 422);
        }
        
        // GET STORE
        $store = Store::find($id);

        // UPDATE URSER
        $update = $store->update(array_merge(
            $validator->validated(),
            ['password' => $request->password ? bcrypt($request->password) : $store->password]
        ));

        return response()->json([
            'message' => 'Successfully updated',
            'data' => $update
        ], 201);
    }
    
    /**
     * Get the Store by ID.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // GET PROFILE STORE
        $store = Store::find($id);
        return response()->json([
            'message' => 'Successfully signed out',
            'data'    => $store
        ]);
    }

    /**
     * DELETE STORE.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function destroy($id)
    {
        // DELETE
        $store = Store::find($id)->delete();
        return response()->json([
            'message' => 'Successfully signed out',
            'data'    => $store
        ]);
    }
}
