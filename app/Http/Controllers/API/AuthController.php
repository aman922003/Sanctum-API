<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function SignUp(Request $request)
    {
        // return response()->json($request->all());
        $validateUser = Validator::make(  
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);
            if($validateUser->fails())
            {
                return response()->json([
                     'status' => false,
                     'message' => 'Validation Error',
                     'error' => $validateUser->errors()->all()
                ],401);
            }
            else
            {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'Successfully Created User',
                    'user' => $user
               ],status: 200);
            }
    }

    public function Login(Request $request)
    {
        $validateUser = Validator::make(  
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails())
            {
                return response()->json([
                     'status' => false,
                     'message' => 'Authentication Fails',
                     'error' => $validateUser->errors()->all()
                ],404);
            }

            if(Auth::attempt(['email'=>$request->email,'password'=>$request->password]))
            {
                $authuser = Auth::user();
                return response()->json([
                    'status' => true,
                    'message' => 'Successfully Loged in User',
                    'token' => $authuser->createToken('API Token')->plainTextToken,
                    'token_type' => 'bearer'
                ],status: 200);
            }
            else
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Authentication Fails',
                    'error' => $validateUser->errors()->all()
               ],401);
            }
            
    }

    public function Logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'status' => true,
            'user' => $user,
            'message' => 'Successfully Logged out User'
        ],status: 200);
    }
}
