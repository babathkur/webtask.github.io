@extends('front.layouts.app')
@section('main')
<section class="section-4 bg-2">
    <div class="container pt-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{route('jobs')}}"><i class="fa fa-arrow-left"
                                    aria-hidden="true"></i> &nbsp;Back to Jobs</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="container job_details_area">
        <div class="row pb-5">
            <div class="col-md-8">
                @include('front.message')
                <div class="card shadow border-0">
                    <div class="job_details_header">
                        <div class="single_jobs white-bg d-flex justify-content-between">
                            <div class="jobs_left d-flex align-items-center">

                                <div class="jobs_conetent">
                                    <a href="#">
                                        <h4>{{$jobDetail->title}}</h4>
                                    </a>
                                    <div class="links_locat d-flex align-items-center">
                                        <div class="location">
                                            <p> <i class="fa fa-map-marker"></i> {{$jobDetail->location}}</p>
                                        </div>
                                        <div class="location">
                                            <p> <i class="fa fa-clock-o"></i>{{$jobDetail->jobType->name}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="jobs_right">
                                <div class="apply_now">
                                    <a class="heart_mark {{($count ==1)?'save-job':''}}" href="javascript:void(0)"
                                        onclick="saveJob({{$jobDetail->id}})"> <i class="fa fa-heart-o"
                                            aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="descript_wrap white-bg">
                        <div class="single_wrap">
                            <h4>Job description</h4>
                            <p>{!! nl2br($jobDetail->description)!!}</p>
                        </div>
                        <div class="single_wrap">
                            @if(!empty($jobDetail->responsibility))
                            <h4>Responsibility</h4>
                            {!! nl2br($jobDetail->responsibility)!!}
                            @endif

                        </div>
                        <div class="single_wrap">

                            @if(!empty($jobDetail->qualifications))

                            <h4>Qualifications</h4>
                            {!! nl2br($jobDetail->qualifications)!!}


                            @endif

                        </div>
                        <div class="single_wrap">
                            @if(!empty($jobDetail->benefits))
                            <h4>Benefits</h4>
                            {!! nl2br($jobDetail->benefits)!!}
                            @endif

                        </div>
                        <div class="border-bottom"></div>
                        <div class="pt-3 text-end">

                            @if(Auth::check())
                            <a href="#" onclick="saveJob({{$jobDetail->id}})" class="btn btn-secondary">Save</a>
                            @else
                            <a href="javascript:void(0)" class="btn btn-secondary disabled">Login to Save</a>

                            @endif

                            @if(Auth::check())
                            <a href="#" class="btn btn-primary" onclick="apply({{$jobDetail->id}})">Apply</a>
                            @else
                            <a href="javascript:void(0)" class="btn btn-primary disabled">Login to Apply</a>

                            @endif
                        </div>
                    </div>
                </div>
                @if(Auth::user())
                @if(Auth::user()->id == $jobDetail->user_id)
                <div class="card shadow border-0 mt-4">
                    <div class="job_details_header">
                        <div class="single_jobs white-bg d-flex justify-content-between">
                            <div class="jobs_left d-flex align-items-center">

                                <div class="jobs_conetent">

                                    <h4>Applicants</h4>
                                </div>
                            </div>
                            <div class="jobs_right">

                            </div>
                        </div>
                    </div>
                    <div class="descript_wrap white-bg">
                        <table class="table table-striped">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>Applied Date</th>
                            </tr>
                            @if($applicants->isNotEmpty())
                            @foreach($applicants as $applicant)


                            <tr>
                                <td>{{$applicant->user->name}}</td>
                                <td>{{$applicant->user->email}}</td>
                                <td>{{$applicant->user->mobile}}</td>
                                <td>{{\Carbon\Carbon::parse($applicant->applied_date)->format('d M,
                                    Y')}}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="3"> Applicants Not Found</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                @endif
                @endif
            </div>
            <div class="col-md-4">
                <div class="card shadow border-0">
                    <div class="job_sumary">
                        <div class="summery_header pb-1 pt-4">
                            <h3>Job Summery</h3>
                        </div>
                        <div class="job_content pt-3">
                            <ul>
                                <li>Published on: <span>{{\Carbon\Carbon::parse($jobDetail->created_at)->format('d M,
                                        Y')}}</span></li>
                                <li>Vacancy: <span>{{$jobDetail->vacancy}}</span></li>
                                @if(!empty($jobDetail->salary))
                                <li>Salary: <span>{{$jobDetail->salary}}</span></li>
                                @endif

                                <li>Location: <span>{{$jobDetail->location}}</span></li>
                                <li>Job Nature: <span> {{$jobDetail->jobType->name}}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card shadow border-0 my-4">
                    <div class="job_sumary">
                        <div class="summery_header pb-1 pt-4">
                            <h3>Company Details</h3>
                        </div>
                        <div class="job_content pt-3">
                            <ul>
                                <li>Name: <span>{{$jobDetail->company_name}}</span></li>
                                @if(!empty($jobDetail->company_location))
                                <li>Locaion: <span>{{$jobDetail->company_location}}</span></li>
                                @endif

                                @if(!empty($jobDetail->company_website))
                                <li>Webite: <span>{{$jobDetail->company_website}}</span></li>
                                @endif

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('customJs')
<script>
    function apply(id){
      if(confirm("Are you sure you want to apply on this jobs!"))
      {
        $.ajax({
            url: "{{route('applyJob')}}",
            type: "post",
            data: {id:id},
            dataType: "json",
            success: function (response) {
                window.location.href="{{url()->current()}}";
            }
        });
      }
    }

    function saveJob(id){
        $.ajax({
            url: "{{route('saveJob')}}",
            type: "post",
            data: {id:id},
            dataType: "json",
            success: function (response) {
                window.location.href="{{url()->current()}}";
            }
        });
      }
</script>
@endsection
