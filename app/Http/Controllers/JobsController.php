<?php

namespace App\Http\Controllers;

use App\Mail\JobNotificationEmail;
use App\Models\Category;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobType;
use App\Models\SavedJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Colors\Rgb\Channels\Red;

class JobsController extends Controller
{
    public function index(Request $request)
    {
        // dd($request->jobType);
        $categories = Category::where('status', 1)->get();
        $jobTypes = JobType::where('status', 1)->get();

        $jobs = Job::where('status', 1);

        // search using keywords and title-----------------
        if (!empty($request->keywords)) {
            $jobs = $jobs->where(function ($query) use ($request) {
                $query->orWhere('title', 'like', '%' . $request->keywords . '%');
                $query->orWhere('keywords', 'like', '%' . $request->keywords . '%');
            });
        }

        // search using location------------------------------
        if (!empty($request->location)) {
            $jobs = $jobs->where('location', 'like', '%' . $request->location . '%');
        }

        // search using categories -----------------------

        if (!empty($request->category)) {
            $jobs = $jobs->where('category_id', 'like', '%' . $request->category . '%');
        }

        // search using jobtype -----------------------
        $jobTypeArray = [];

        if (!empty($request->job_type)) {
            $jobTypeArray = explode(',', $request->job_type);

            $jobs = $jobs->whereIn('job_type_id', $jobTypeArray);
        }




        // search using experience -----------------------

        if (!empty($request->experience)) {
            $jobs = $jobs->where('experience',  $request->experience);
        }

        $jobs = $jobs->with(['jobType', 'category']);
        if (!empty($request->sort) && $request->sort == 0) {
            $jobs = $jobs->orderBy('created_at', 'ASC');
        } else {
            $jobs = $jobs->orderBy('created_at', 'DESC');
        }



        $jobs = $jobs->paginate(9);

        return view('front.jobs', [
            'jobs' => $jobs,
            'categories' => $categories,
            'jobTypes' => $jobTypes,
            'jobTypeArray' => $jobTypeArray
        ]);
    }

    public function jobDetail($id)
    {
        $jobDetail = Job::where([
            'id' => $id,
            'status' => 1
        ])->with('jobType')->first();

        if ($jobDetail == null) {
            abort(404);
        }
        $count = 0;
        if (Auth::user()) {
            $count = SavedJob::where([
                'user_id' => Auth::user()->id,
                'job_id' => $id
            ])->count();
        }

        // fetch job Applicants
        $applicants = JobApplication::where('job_id', $id)->with('user')->get();
        // dd($applicants);
        return view('front.jobDetail', [
            'jobDetail' => $jobDetail,
            'count' => $count,
            'applicants' => $applicants

        ]);
    }


    public function applyJob(Request $request)
    {
        $id = $request->id;

        $job = Job::where('id', $id)->first();
        // dd($job);
        // if job not found in db
        if ($job == null) {
            $message = 'Job does not exist ';
            session()->flash('error', $message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }

        // you can not apply on your own job
        $employer_id = $job->user_id;

        // dd($employer_id);
        if ($employer_id == Auth::user()->id) {
            $message = 'You can not apply on your own job ';
            session()->flash('error', $message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }

        // you can not appiled on a job twice

        $jobApplicationCount = JobApplication::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id
        ])->count();

        if ($jobApplicationCount > 0) {
            $message = 'You applied already on this job ';
            session()->flash('error', $message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }
        $employer = User::where('id', $employer_id)->first();
        // dd($employer);
        $application = new JobApplication();
        $application->job_id = $id;
        $application->user_id = Auth::user()->id;
        $application->employer_id = $employer_id;
        $application->applied_date = now();
        $application->save();
        // send notification email to employer
        $employer = User::where('id', $employer_id)->first();
        $mailData = [
            'employer' => $employer,
            'user' => Auth::user(),
            'job' => $job
        ];

        // dd($employer->email);
        Mail::to($employer->email)->send(new JobNotificationEmail($mailData));

        $message = 'You have applied successfully! ';
        session()->flash('success', $message);
        return response()->json([
            'status' => false,
            'message' => $message
        ]);
    }

    public function myJobsApplication()
    {
        $jobApplications = JobApplication::where('user_id', Auth::user()->id)
            ->with(['job', 'job.jobType', 'job.application'])->orderBy('created_at', 'DESC')
            ->paginate(10);
        // dd($myjobs);
        return view('front.account.job.my-jobs-applications', [
            'jobApplications' => $jobApplications
        ]);
    }

    public function removeJob(Request $request)
    {
        $jobApplication = JobApplication::where([
            'id' => $request->id,
            'user_id' => Auth::user()->id
        ])->first();

        // dd($jobApplication);
        if ($jobApplication == null) {
            session()->flash('error', 'Job Application Not Found');
            return response()->json([
                'status' => false
            ]);
        }

        JobApplication::find($request->id)->delete();
        session()->flash('success', 'Job Application remove successfully');
        return response()->json([
            'status' => true
        ]);
    }

    public function saveJob(Request $request)
    {
        $id = $request->id;

        $job = Job::find($id);
        if ($job == null) {
            session()->flash('error', 'Job Not Found');
            return response()->json([
                'status' => false
            ]);
        }

        //Check if user already saved the job

        $count = SavedJob::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id
        ])->count();


        if ($count > 0) {
            session()->flash('error', 'You already applied on this job');
            return response()->json([
                'status' => false
            ]);
        }

        $savedJob = new SavedJob();
        $savedJob->job_id = $id;
        $savedJob->user_id = Auth::user()->id;
        $savedJob->save();
        session()->flash('success', 'You  have saved the job successfully');
        return response()->json([
            'status' => true
        ]);
    }
}
