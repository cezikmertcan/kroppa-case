<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request){
        $validatedUser = Validator::make($request->all(),
        [
            'name'=>'required',
            'surname'=>'required',
            'password'=>'required',
            'email'=>"required|email|unique:users,email"
        ]);
        if($validatedUser->fails()){
            return response()->json(
                [
                    'status'=>false,
                    'message'=>'Validation errors.',
                    'errors'=>$validatedUser->errors()
                ],
                401);
        }
        $createdUser = User::create([
            'name'=>$request->name,
            'surname'=>$request->surname,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
        return response()->json(
            [
                'status'=>true,
                'message'=>"User created successfully."
            ]
        );
    }

    public function login(Request $request){
        $validatedUser = Validator::make($request->all(),
        [
            'password'=>'required',
            'email'=>"required|email"
        ]);
        if($validatedUser->fails()){
            return response()->json(
                [
                    'status'=>false,
                    'message'=>'Validation errors.',
                    'errors'=>$validatedUser->errors()
                ],
                401);
        }
        if(!Auth::attempt($request->only(['email','password']))){
            return response()->json(
                [
                    'status'=>false,
                    'message'=>"Wrong email/password combination."
                ]
            );
        }
        $loggedinUser = User::where('email',$request->email)->first();
        return response()->json(
            [
                'status'=>true,
                'message'=>"User logged in successfully.",
                'token'=>$loggedinUser->createToken("API TOKEN")->plainTextToken
            ]
        );
    }
}
