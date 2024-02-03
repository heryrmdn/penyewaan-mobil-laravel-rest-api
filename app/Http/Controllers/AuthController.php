<?php

namespace App\Http\Controllers;

use App\Models\User;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'alamat' => 'required',
            'nomor_telepon' => 'required',
            'nomor_sim' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        if (User::where('email', $request->email)->exists()) {
            return response()->json(["message" => "Email sudah terdaftar"], 400);
        }

        $request['password'] = bcrypt($request->password);
        User::create(request()->all());

        return response()->json(["message" => "Registrasi berhasil"], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email tidak terdaftar'], 400);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Password tidak sesuai'], 400);
        }

        $token = $user->createToken('access_token')->plainTextToken;

        return response()->json([
            'message' => 'Login sukses',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    }
}
