<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    function login(Request $request){
        $user= User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success'=>false,
                'msg' =>'These credentials do not match our records.',
                'severity'=>'danger',
                'summary'=>'Error Message'
            ]);
        }else{
            $token = $user->createToken('my-app-token')->plainTextToken;
            return response()->json([
                'user' => $user,
                'accessToken' => $token
            ]);
        }
    
        
    }
    
    public function adduser(Request $request){
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => ['required','unique:users','email'],
            'password' => ['required', 'min:8'],
            'terms' => ['required'],
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg' => $validator->errors()->all(),
                'severity'=>'danger',
                'summary'=>'Error Message'
            ]);
        }else{
            $user = new User;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->role = 'user';
                $user->terms = $request->terms;
            $user->save();
            return response()->json([
                'success'=>true,
                'msg' => ['User registered succesfully'],
                'severity'=>'success',
                'summary'=>'Success Message'
            ]);
        }
    }

    public function userList(){
        return ['name'=>'prem'];
    }

    public function allUser(Request $request){
        return User::where('first_name', $request->name)
        ->orWhere('last_name', $request->name)->get();
    }
}
