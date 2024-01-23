<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Get a List User+
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
     public function index()
     {
        // GET USERS DATA
        $users = User::with('store')->get();
        
        return response()->json([
            'message'      => "Successfully get data",
            'data' => $users
         ]);
     }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function login(Request $request)
    {
        // VALIDATION LOGIN
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            // RETURN ERROR VALIDATION
            return response()->json($validator->errors(), 422);
        }

        // CHECK USER LOGIN
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        return response()->json([
            'message'      => "Successfully login",
            'access_token' => $token,
            'token_type' => 'bearer',
            'data' => auth()->user()
        ]);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // VALIDATION FORM
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'birthdate' => 'required|date_format:Y-m-d',
            'store_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            // RETURN ERROR VALIDATION
            return response()->json([
                "message" => "The given data was invalid.",
                "errors"  => $validator->errors()
            ], 422);
        }
        
        // CREATE URSER
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'Successfully registered',
            'data' => $user
        ], 201);
    }

     /**
     * Update a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // VALIDATION FORM
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users,email,'.$id,
            'password' => 'nullable|string|confirmed|min:6',
            'birthdate' => 'required|date_format:Y-m-d',
            'store_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            // RETURN ERROR VALIDATION
            return response()->json([
                "message" => "The given data was invalid.",
                "errors"  => $validator->errors()
            ], 422);
        }
        
        // GET USER
        $user = User::find($id);

        // UPDATE URSER
        $update = $user->update(array_merge(
            $validator->validated(),
            ['password' => $request->password ? bcrypt($request->password) : $user->password]
        ));

        return response()->json([
            'message' => 'Successfully updated',
            'data' => $update
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        // LOGOUT
        auth()->logout();
        return response()->json([
            'message' => 'Successfully get data',
            'data'    => null
        ]);
    }
    
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile($id=null)
    {
        // GET PROFILE USER
        $user = User::with('store')->find($id ?? auth()->user()->id);
        return response()->json([
            'message' => 'Successfully Get User',
            'data'    => $user
        ]);
    }

    /**
     * DELETE USER.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function destroy($id)
    {
        // DELETE
        $user = User::with('store')->find($id)->delete();
        return response()->json([
            'message' => 'Successfully signed out',
            'data'    => $user
        ]);
    }
}
