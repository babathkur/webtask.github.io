<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function index()
    {
        $jobApplications = JobApplication::orderBy('created_at', 'DESC')
            ->with('job', 'user', 'employer')
            ->paginate(10);

        return view(
            'admin.job-application.list',
            [
                'jobApplications' => $jobApplications
            ]
        );
    }

    public function destroy(Request $request)
    {

        $id = $request->id;
        // dd($id);
        $user = JobApplication::find($id);
        if ($user == Null) {
            session()->flash('error', 'Job Application not found');
            return response()->json([
                'status' => false
            ]);
        }
        $user->delete($id);
        session()->flash('success', 'Job Application deleted successfully');
        return response()->json([
            'status' => true
        ]);
    }
}
