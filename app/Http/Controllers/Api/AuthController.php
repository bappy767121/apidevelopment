<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $access_token = $user->createToken('accessToken')->accessToken;

        return response()->json(['message' => 'Login successful', 'customer_name' => $user->name, 'access_token' => $access_token]);
        }else{
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        
    }
    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        // You might also generate and return a JWT token here

        return response()->json([
            'message' => 'Registration successful',
            'name' => $user->name,
            'email' => $user->email,
        ]);

    }

    public function logout(Request $request)
    {
        auth()->user()->token()->revoke();
        return response()->json(['message' => 'Logout Successfully']);

    }
    
    public function show($id){
        $user = User::find($id);
        if($user){
            return response()->json(['user' => $user]);
        }else{
            return response()->json(['message' => 'Data has been not found']);
        }
        
    }
}
