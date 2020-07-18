<?php

namespace App\Http\Controllers;

use App\Http\Model\user_identitys as identity;
use Illuminate\Http\Request;
use App\Http\Model\User;
use DB;

use Laravel\Lumen\Routing\Controller as BaseController;

class userController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return "You in userController ";
    }

    public function userProfile(Request $request)
    {
        return identity::find($request->input('user_id'));
    }

    public function userFillIdentity(Request $request)
    {
        $this->validate($request,[
            "email" => 'required',
            "name" => 'required',
            "age" => 'required',
            "address" => 'required',
            "skills" => 'required',
        ]);
        
       if(!$request->get('token')){
           return response()->json([
               "status" => 401,
               "message" => "User Not Registered!"
           ]);
       }else{
        $user_id = DB::table('users')->Where('email',$request->get('email'))->select('id')->first();
        if($user_id == null){
            return response()->json([
                "status" => 400 , "message" => "User Not Found"
            ]);
        }
        $check_user_identity =  DB::table('user_identitys')->where('user_id',$user_id->id)->first();
        if($check_user_identity != null){
            DB::beginTransaction();
               try {
                identity::update([
                    "user_id" => $user_id->id,
                    "name" => $request->input('name'),
                    "age" => $request->input('age'),
                    "address" => $request->input('address'),
                    "skills" => $request->input('skills')
                ]);
                DB::commit();
                return response()->json([
                    "status" => 201 , "message" => "Identity Updated!"
                ]);
    
               } catch (\Exception $e) {
                   DB::rollback();
                   return response()->json([
                       "status" => 401 , "message" => "Error"
                   ]);
               }
        }else{
            DB::beginTransaction();
               try {
                identity::create([
                    "user_id" => $user_id->id,
                    "name" => $request->input('name'),
                    "age" => $request->input('age'),
                    "address" => $request->input('address'),
                    "skills" => $request->input('skills')
                ]);
                DB::commit();
                return response()->json([
                    "status" => 201 , "message" => "Identity Updated!"
                ]);
    
               } catch (\Exception $e) {
                   DB::rollback();
                   return response()->json([
                       "status" => 401 , "message" => "Error"
                   ]);
               }
            }
        }
    }

    public function userEditIdentity(Request $request)
    {
        $this->validate($request,[
            "name" => 'required|unique:user_identitys',
            "age" => 'required',
            "address" => 'required',
            "skills" => 'required',
        ]);
        DB::beginTransaction();
        try {
            DB::table('user_identitys')
                    ->Where('user_id',$request->input('user_id'))
                    ->update([
                        "name" => $request->input('name'),
                        "age" => $request->input('age'),
                        "address" => $request->input('address'),
                        "skills" => $request->input('skills')
                    ]);
            DB::commit();
            return response()->json([
                "status" => 201 , "message" => "Your Identity Updated!"
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "status" => 401 , "message" => "Error"
            ]);
        }
    }
}