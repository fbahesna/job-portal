<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\job;
use Laravel\Lumen\Routing\Controller as BaseController;
use DB;

class dashController extends BaseController
{
    public function index()
    {
        return "you in dash Controller";
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

    public function updateJob(Request $request , $id)
    {
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

    public function updateJobStatus(Request $request, $id)
    {
        dd($request->input('role'));
        $job = job::find($id);
        $job->status = $request->input('status');
        $job->save();

        return response()->json([
            "status" => 200 , "message" => "Job Status Updated!"
        ]);
    }

    public function test($id)
    {
        return "hai" . $id;
    }
}
