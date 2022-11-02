<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5|max:12'
        ]);

        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);

        return User::create($data);
    }

    function login(Request $request){
       if (Auth::attempt($request->only('email', 'password'))) {
           $user = User::find(Auth::id());
        $token =  $user->createToken('webappTtoken')->plainTextToken;
        $token2 = PersonalAccessToken::findToken($token);

        $user = $token2->tokenable;
        return ['token' => $token, 'user' => $user];
       }
       return ["message" => 'Credential invalid.'];
    }
    function logout(){
        $user = User::find(auth('sanctum')->user()->id);
        return $user->tokens()->delete();
    }
}
