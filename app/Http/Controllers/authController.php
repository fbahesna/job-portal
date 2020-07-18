<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Model\User;
use App\Http\Model\user_roles as role;
use DB;

class authController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|unique:users|max:255',
            'password' => 'required|min:6'
        ]);
 
        $email = $request->input("email");
        $password = $request->input("password");
 
        $hashPwd = Hash::make($password);
 
        $data = [
            "email" => $email,
            "password" => $hashPwd,
        ];

        DB::beginTransaction();
        try{
            $create_user = User::create($data);
            //sekaligus menambah untuk role user
            role::create([
                "user_id" => $create_user->id,
                "role_number" => 2,
                "created_at" => date("Y-m-d H:i:s")
            ]);

            $data = [
                "message" => "register_success",
                "status"    => 201,
            ];

            DB::commit();
        }catch(\Exception $e){
            $data = [
                "message" => "register_failed",
                "reason" => $e,
                "status"   => 404,
            ];
            
            DB::rollback();
        }
 
        return response()->json($data, $data['status']);
    }


    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);
 
        $email = $request->input("email");
        $password = $request->input("password");

        $user = User::where("email", $email)->first();

        $get_user_role = DB::table('users as u')
            ->Join('user_roles as ur','u.id','=','ur.user_id')
            ->Where('u.id',$user->id)
            ->select('ur.role_number as user_role')
            ->first();

        if (!$user) {
            $data = [
                "message" => "login_failed",
                "status"    => 401,
                "result"  => [
                    "token" => null,
                ]
            ];
            return response()->json($data, $data['status']);
        }
 
        if (Hash::check($password, $user->password)) {
            $new_token  = $this->generateRandomString();
 
            $user->update([
                'token' => $new_token
            ]);
 
            $out = [
                "message" => "login_success",
                "status"    => 200,
                "user_role" => $get_user_role->user_role,
                "result"  => [
                    "token" => $new_token,
                ]
            ];
        } else {
            $out = [
                "message" => "login_failed",
                "status"    => 401,
                "result"  => [
                    "token" => null,
                ]
            ];
        }
       
        return response()->json($out, $out['status']);
    }

    function generateRandomString($length = 80)
    {
        $karakkter = '012345678dssd9abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $panjang_karakter = strlen($karakkter);
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $karakkter[rand(0, $panjang_karakter - 1)];
        }
        return $str;
    }

    //
}
