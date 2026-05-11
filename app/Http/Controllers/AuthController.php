<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendEmail;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends  Controller{

    public function __construct(private UserRepositoryInterface $UserRepository)
    {}

    public function register(CreateUserRequest $request){
        $validate = $request->validated();

        try{
            $user = $this->UserRepository->create($request);

            $user->assignRole('user');

            SendEmail::dispatch($user);

            return response()->json([
                'status'=>'success',
                'data'=> new UserResource($user),
            ],200);

        }catch(Exception $e){
            return response()->json([
                'status'=>'failed',
                'message'=>$e->getMessage()
            ],500);
        }



    }

    public function login(LoginRequest $request){

        try{

            $validate = $request->validated();

            $creds = $request->only('email','password');
            if(!Auth::attempt($creds)){
                return response()->json([
                    'message'=>'Invalid creds'
                ],401);
            }

            $user = Auth::user();

            return response()->json([
                'status' => 'Success',
                'data'=> new UserResource($user),
            ]);
        }
        catch(HttpResponseException $e){
            return response()->json([
                'status'=>'failed',
                'message'=>$e->getMessage()
            ],422);
        }
        catch(Exception $e){
            return response()->json([
                'status'=> 'failed',
                'message' => $e->getMessage()
            ],500);
        }

    }
    public function Logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Logged out']);
    }
}
