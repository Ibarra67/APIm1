<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $model;

    public function __construct(){
        $this->model = new User();
    }
  
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        try{
            
            if(!Auth::attempt($credentials)){
                return response(['message' => "Account is not registered"], 200);
            } 

            $user = $this->model->where('email', $request->email)->first();            
            $token = $user->createToken($request->email . Str::random(8))->plainTextToken;

            return response(['token' => $token], 200);

        }catch(\Exception $e){
            return response(['message' => $e->getMessage()], 400);
        }
    }

     
    public function register(Request $request)
{try {

    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|confirmed|min:6',
    ]);

    try {
        $request['role'] = '1';
        $this->model->create($request->all());
        return response(['message' => "Successfully created"], 201); // 2
    } catch (\Exception $e) {
        return response(['message' => $e->getMessage()], 500); // 500 Internal Server Error
    }
    // Log success
    \Log::info('Registration successful');

    return response(['message' => "Successfully created"], 201);
} catch (\Exception $e) {
    // Log the exception
    \Log::error($e);

    return response(['message' => $e->getMessage()], 500);
}
}
}