<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $this->checkUserAuth();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'age' => 'required|integer',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
                'errorCode' => 422
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'username' => $request->username,
            'age' => $request->age,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'admin' => false,
            'profile_img' => Null,
        ]);
        Auth::login($user);
        $token = $user->createToken($user->name . '-token')->plainTextToken;
        return response()->json([
            'status' => true,
            'user' => $user,
            'message' => "New user registered",
            'token' => $token,
            'httpCode' => 200
        ]);
    }

    public function login(Request $request)
    {
        $this->checkUserAuth();
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken($user->name . '-token')->plainTextToken;
            return response()->json([
                'status' => true,
                'user' => $user,
                'token' => $token,
                'message' => 'Login succesfull',
                'httpCode' => 200
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Login succesfull',
                'httpCode' => 401
            ]);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens->delete();
        return response()->json([
            'status' => true,
            'messagge' => 'User logout',
            'httpCode' => 200
        ]);
    }
}
