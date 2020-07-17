<?php

namespace App\Http\Controllers;

use App\Http\Model\user_identitys as identity;
use Illuminate\Http\Request;
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
        dd($request->get('user_id'));
        return identity::find($request->input('user_id'));
    }

    public function userFillIdentity(Request $request)
    {
        $this->validate($request,[
            "name" => 'required',
            "age" => 'required',
            "address" => 'required',
            "skills" => 'required',
        ]);

       if(!$request->input('user_id') && !$request->get('token')){
           return response()->json([
               "status" => 401,
               "message" => "User Not Registered!"
           ]);
       }else{
           DB::beginTransaction();
           try {
            identity::create([
                "user_id" => $request->input('user_id'),
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