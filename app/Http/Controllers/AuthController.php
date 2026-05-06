<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends  Controller{

    public function register(RegisterRequest $request){
        $validate = $request->validated();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $user->assignRole('user');

        SendEmail::dispatch($user);

        return response()->json([
            'status'=>'success',
            'data'=> new UserResource($user),
        ],200);

    }

    public function login(LoginRequest $request){

        $validate = $request->validated();

        $creds = $request->only('email','password');
        if(!Auth::attempt($creds)){
            return response()->json([
                'message'=>'Invalid creds'
            ],401);
        }

        $user = Auth::user();

        return response()->json([
            'status' => true,
            'data'=> new UserResource($user),
        ]);
    }
    public function Logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Logged out']);
    }
}
