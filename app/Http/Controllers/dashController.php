<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\job;
use Laravel\Lumen\Routing\Controller as BaseController;
use DB;
use App\Http\Model\job_submits as job_submit;

class dashController extends BaseController
{
    public function __construct()
    {
        $this->middleware('checkAdmin');
    }

    public function index()
    {
        return "Your are Admin Now , Welcome Admin!";
    }

    public function jobList()
    {
        $data = job::all();
            return response()->json([
                "status" => 200 , "data" => $data
            ]);
    }

    public function createJob(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->validate($request,[
                "category" => 'required',
                "jobTitle" => 'required|unique:jobs',
                "level_requirement" => 'required',
                "sallary" => 'required|min:6|max:10',
                "jobdesk" => 'required',
            ]);
    
            $jobCreate = job::create([
                "category" => $request->input('category'),
                "jobTitle" => $request->input('jobTitle'),
                "level_requirement" => $request->input('level_requirement'),
                "sallary" => $request->input('sallary'),
                "jobdesk" => $request->input('jobdesk'),
            ]);
            DB::commit();

            return response()->json([
                "status" => 200, "message" => "A new job " . $jobCreate->jobTitle . " created! " 
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                "status" => 401 , "message" => $th
            ]);
        }
    }

    public function updateJob(Request $request)
    {
        $id = $request->input('id');
    
        $this->validate($request,[
            "category" => 'required',
            "jobTitle" => 'required|unique:jobs',
            "level_requirement" => 'required',
            "sallary" => 'required|min:6|max:10',
            "jobdesk" => 'required',
        ]);
        
        $job = job::find($id);
        $job->category = $request->input('category');
        $job->jobTitle = $request->input('jobTitle');
        $job->level_requirement = $request->input('level_requirement');
        $job->sallary = $request->input('sallary');
        $job->jobdesk = $request->input('jobdesk');
        $job->save();

        if($job->save()){
            return response()->json([
                "status" => 200 , "message" => "Job updated!"
            ]);
        }else{   
            return response()->json([
                "status" => 401 , "message" => $e
            ]);
        }
    }

    public function updateJobStatus(Request $request)
    {
        $id = $request->input('id');
        $job = job::find($id);
        $job->status = $request->input('status');
        $job->save();

        return response()->json([
            "status" => 200 , "message" => "Job Status Updated!"
        ]);
    }

    public function jobStatus(Request $request)
    {
        $status = $request->get('status');
        $data = DB::table("jobs")->where("status",$status)->get();

        return response()->json([
            "status" => 200 , "message" => $data
        ]);
    }

    public function showJobSubmit()
    {
        
    $job_submit = DB::table('job_submits as jss')
        ->Join('jobs as j','jss.job_id','=','j.id')
        ->Join('user_identitys as ui','jss.user_id','=','ui.user_id')
        ->select(
            'jss.id as job_submit_id',
            'j.jobTitle as job_title',
            'j.level_requirement as job_level_requirement',
            'ui.name as user_name',
            'ui.age as user_age'
        )
        ->get();
    
        return response()->json([
            "status" => 200 , "data" => $job_submit
        ]);
    }

    public function showJobSubmited(Request $request)
    {
        $user_data_job_submited = DB::table('job_submits as jss')->Where('jss.id',$request->input('job_submit_id'))
            ->leftJoin('jobs as j','jss.job_id','=','j.id')
            ->leftJoin('users as u','jss.user_id','=','u.id')
            ->leftJoin('user_identitys as ui','jss.user_id','=','ui.user_id')
            ->select(
                'ui.name as user_name',
                'u.email as user_email',
                'ui.age as user_age',
                'ui.address as user_address',
                'ui.skills as user_skills',

                'j.jobTitle as job_title',
                'j.category as job_category',
                'j.level_requirement as job_level_requirement',
                'j.sallary as job_sallary',
                'j.jobdesk as job_jobdesk',
                'j.status as job_status'
            )->first();

        return response()->json([
            "status" => 200 , "message" => "Data Found" , "data" => $user_data_job_submited
        ]);
    // DB::table('users as u')
    //     ->Join('job_submits as js','u.id','=','js.user_id')
    //     ->select('u.')

    // $result = [];
    //   foreach($job_submit as $key => $value){
    //       $j_array = json_decode($value);
    //       $result[$key] = [
    //           "id" => json_encode($j_array->id),
    //           "job_id" => json_encode($j_array->job_id)
    //       ];
    //   }
    //   $test = $result;
    //   return $test; die;
    }
}
