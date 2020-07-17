<?php

namespace App\Http\Controllers;
use App\Http\Model\job;
use App\Http\Model\User;
use App\Http\Model\job_submits as job_submit;
use DB;
use Illuminate\Http\Request;

class freelanceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return response()->json([
            "message" => "welcome freelance"
        ]);
    }

    public function showjobList()
    {
        $data = DB::table('jobs')
                ->Where("status","published")
                ->get();

        return response()->json([
            "status" => 200 , "data" => $data
        ]);
    }

    public function jobSubmit(Request $request)
    {
       $user_data = User::where('email',$request->get('email'))->select('id')->first();

        if(!$user_data){
            return response()->json([
                "status" => 401 , "message" => "User Not Registered"
            ]);
        }

        $check_identity = DB::table('users as u')
            ->Join('user_identitys as ui','u.id','=','ui.user_id')
            ->where('u.id',$user_data["id"])
            ->select(
                'ui.user_id as user_id'
            )
            ->first();
        
        $check_double_apply = DB::table('job_submits')->where('user_id',$user_data["id"])->first();

        if($check_identity == null){
            return response()->json([
                "status" => 400 , "message" => "Please Complete Your Profile First"
            ]);
        }

        if($check_double_apply != null){
            return response()->json([
                "status" => 400 , "message" => "You Only Can Submit Once"
            ]);
        }

       $job_id = $request->get('job_id');

        DB::beginTransaction();
        try {
            $apply = job_submit::create([
                "job_id" => $job_id,
                "user_id" => $user_data["id"]
            ]);
            DB::commit();
        return response()->json([
            "status" => 200 , "message" => "Thank you for submitting your application"
        ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "status" => 400 , "message" => $e
            ]);
        }
    }
}
