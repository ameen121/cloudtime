@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 bg-title-right">
            <div class="col-md-3 pull-right hidden-xs hidden-sm">

                <select class="selectpicker language-switcher m-t-10  pull-right" data-width="fit">
                    @if($global->timezone == "Europe/London")
                   <option value="en" @if($global->locale == "en") selected @endif data-content='<span class="flag-icon flag-icon-gb"></span>'>En</option>
                   @else
                   <option value="en" @if($global->locale == "en") selected @endif data-content='<span class="flag-icon flag-icon-us"></span>'>En</option>
                   @endif
                    @foreach($languageSettings as $language)
                        <option value="{{ $language->language_code }}"
                                @if($user->locale == $language->language_code) selected
                                @endif  data-content='<span class="flag-icon @if($language->language_code == 'zh-CN') flag-icon-cn @elseif($language->language_code == 'zh-TW') flag-icon-tw @else flag-icon-{{ $language->language_code }} @endif"></span>'>{{ $language->language_code }}</option>
                    @endforeach
                </select>
                
            </div>

            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
    <style>
        .col-in {
            padding: 0 20px !important;

        }

        .fc-event{
            font-size: 10px !important;
        }
        .front-dashboard .white-box{
            margin-bottom: 8px;
         }

        @media (min-width: 769px) {
            #wrapper .panel-wrapper{
                height: 530px;
                overflow-y: auto;
            }
        }

    </style>
@endpush

@section('content')

<div class="white-box">
    <div class="row dashboard-stats front-dashboard">
        @if(in_array('projects',$modules))
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('member.projects.index') }}">
                <div class="white-box">
                    <div class="row">
                        <div class="col-xs-3">
                            <div>
                                <span class="bg-info-gradient"><i class="icon-layers"></i></span>
                            </div>
                        </div>
                        <div class="col-xs-9 text-right">
                            <span class="widget-title"> @lang('modules.dashboard.totalProjects')</span><br>
                            <span class="counter">{{ $totalProjects }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endif

        @if(in_array('timelogs',$modules))
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('member.all-time-logs.index') }}">
                <div class="white-box">
                    <div class="row">
                        <div class="col-xs-3">
                            <div>
                                <span class="bg-warning-gradient"><i class="icon-clock"></i></span>
                            </div>
                        </div>
                        <div class="col-xs-9 text-right">
                            <span class="widget-title"> @lang('modules.dashboard.totalHoursLogged')</span><br>
                            <span class="counter">{{ $counts->totalHoursLogged }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endif

        @if(in_array('tasks',$modules))
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('member.all-tasks.index') }}">
                <div class="white-box">
                    <div class="row">
                        <div class="col-xs-3">
                            <div>
                                <span class="bg-danger-gradient"><i class="ti-alert"></i></span>
                            </div>
                        </div>
                        <div class="col-xs-9 text-right">
                            <span class="widget-title"> @lang('modules.dashboard.totalPendingTasks')</span><br>
                            <span class="counter">{{ $counts->totalPendingTasks }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6">
            <a href="{{ route('member.all-tasks.index') }}">
                <div class="white-box">
                    <div class="row">
                        <div class="col-xs-3">
                            <div>
                                <span class="bg-success-gradient"><i class="ti-check-box"></i></span>
                            </div>
                        </div>
                        <div class="col-xs-9 text-right">
                            <span class="widget-title"> @lang('modules.dashboard.totalCompletedTasks')</span><br>
                            <span class="counter">{{ $counts->totalCompletedTasks }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endif

    </div>
    <!-- .row -->

    <div class="row">

        @if(in_array('attendance',$modules))
       
        <div class="col-md-6">
            <div class="panel panel-inverse">
                <div class="panel-heading">@lang('app.menu.attendance')</div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">

                        <input type="hidden" id="current-latitude">
                        <input type="hidden" id="current-longitude">
                        <!-- !isset($noClockIn) -->
                      
                        @if(!isset($noClockIn))
                          
                            @if(!$checkTodayHoliday)
                          
                            
                                @if($todayTotalClockin < $maxAttandenceInDay)
                                    <div class="col-xs-6">
                                        <h3>@lang('modules.attendance.clock_in')</h3>
                                    </div>
                                    <div class="col-xs-6">
                                        <h3>@lang('modules.attendance.clock_in') IP</h3>
                                    </div>
                                    <div class="col-xs-6">
                                        @if(is_null($currenntClockIn))
                                            {{ \Carbon\Carbon::now()->timezone($global->timezone)->format($global->time_format) }}
                                        @else
                                            {{ $currenntClockIn->clock_in_time->timezone($global->timezone)->format($global->time_format) }}
                                        @endif
                                    </div>
                                    <div class="col-xs-6">
                                        {{ $currenntClockIn->clock_in_ip ?? request()->ip() }}
                                    </div>
                                  
                                    @if(!is_null($currenntClockIn) && !is_null($currenntClockIn->clock_out_time))
                                        <div class="col-xs-6 m-t-20">
                                            <label for="">@lang('modules.attendance.clock_out')</label>
                                            <br>{{ $currenntClockIn->clock_out_time->timezone($global->timezone)->format($global->time_format) }}
                                        </div>
                                        <div class="col-xs-6 m-t-20">
                                            <label for="">@lang('modules.attendance.clock_out') IP</label>
                                            <br>{{ $currenntClockIn->clock_out_ip }}
                                        </div>
                                    @endif

                                    <div class="col-xs-8 m-t-20 truncate">
                                        <label for="">@lang('modules.attendance.working_from')</label>
                                        @if(is_null($currenntClockIn))
                                            <input type="text" class="form-control" id="working_from" name="working_from">
                                        @else
                                            <br> {{ $currenntClockIn->working_from }}
                                        @endif
   
                                  </div>
<?php
   ;
    $attendanceSetup=\App\AttedanceSetup::query()->where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->get();
     
        foreach ($attendanceSetup as $att)
        {
            $dataAttendanceArray[]=$att->name;
        }
        
            $curr_date=date("Y.m.d");   
            $status=   \DB::table('user_attendance_login_status')
            ->where('user_id',\Illuminate\Support\Facades\Auth::user()->id) 
            ->where('date', $curr_date)
            ->first();
            if($status){
                    $statusClockIn= $status->clock_in;
                    $first_break_end= $status->first_break_end;
                    $second_break_end= $status->second_break_end;
                    $third_break_end= $status->third_break_end;
                    $four_break_end= $status->four_break_end;
                    $five_break_end= $status->five_break_end;
                    $six_break_end= $status->six_break_end;
                    $seven_break_end= $status->seven_break_end;
                    $eight_break_end= $status->eight_break_end;
                    $nine_break_end= $status->nine_break_end;
            }else{
                    $statusClockIn= 0;
                    $first_break_end= 0;
                    $second_break_end= 0;
                    $third_break_end= 0;
                    $four_break_end= 0;
                    $five_break_end=0;
                    $six_break_end= 0;
                    $seven_break_end= 0;
                    $eight_break_end= 0;
                    $nine_break_end= 0;
            }
     
    ?>
       
   
    
                                
                                    <div class="col-xs-4 m-t-20">
                                        <label class="m-t-30">&nbsp;</label>
                                      
                                          
                                        @if(is_null($currenntClockIn))
                                            <button class="btn btn-success btn-sm" id="clock-in">
                                              Clock In</button>
                                        @endif
                                       
                                        @if(!is_null($currenntClockIn) &&  $statusClockIn && $currenntClockIn->user_id==\auth()->user()->id )
                                        <!-- clock_in -->
                                            <a href="javascript:showAttendanceModal({{@$currenntClockIn->id}});" class="btn btn-danger btn-sm" id=""> Clock Out</a>
                                        @endif
                                        @if(!is_null($currenntClockIn))
                                        @if($five_break_end && $currenntClockIn->user_id==\auth()->user()->id )
                                            <a href="javascript:showAttendanceModal1({{@$currenntClockIn->id}},10);" class="btn btn-success btn-sm" id=""> Clock In</a>
                                        @endif
                                        @if($first_break_end && $currenntClockIn->user_id==\auth()->user()->id)
                                            <a href="javascript:showAttendanceModal1({{@$currenntClockIn->id}},2);" class="btn btn-success btn-sm" id=""> Clock In</a>
                                        @endif
                                        @if($second_break_end && $currenntClockIn->user_id==\auth()->user()->id)
                                            <a href="javascript:showAttendanceModal1({{@$currenntClockIn->id}},4);" class="btn btn-success btn-sm" id=""> Clock In</a>
                                        @endif
                                        @if($third_break_end && $currenntClockIn->user_id==\auth()->user()->id)
                                            <a href="javascript:showAttendanceModal1({{@$currenntClockIn->id}},6);" class="btn btn-success btn-sm" id=""> Clock In</a>
                                        @endif
                                        @if($four_break_end && $currenntClockIn->user_id==\auth()->user()->id)
                                            <a href="javascript:showAttendanceModal1({{@$currenntClockIn->id}},8);" class="btn btn-success btn-sm" id=""> Clock In</a>
                                        @endif
                                        @if($six_break_end && $currenntClockIn->user_id==\auth()->user()->id)
                                            <a href="javascript:showAttendanceModal1({{@$currenntClockIn->id}},12);" class="btn btn-success btn-sm" id=""> Clock In</a>
                                        @endif
                                        @if($seven_break_end && $currenntClockIn->user_id==\auth()->user()->id)
                                            <a href="javascript:showAttendanceModal1({{@$currenntClockIn->id}},14);" class="btn btn-success btn-sm" id=""> Clock In</a>
                                        @endif
                                        @if($eight_break_end && $currenntClockIn->user_id==\auth()->user()->id)
                                            <a href="javascript:showAttendanceModal1({{@$currenntClockIn->id}},16);" class="btn btn-success btn-sm" id=""> Clock In</a>
                                        @endif
                                        @if($nine_break_end  && $currenntClockIn->user_id==\auth()->user()->id)
                                            <a href="javascript:showAttendanceModal1({{@$currenntClockIn->id}},18);" class="btn btn-success btn-sm" id=""> Clock In</a>
                                        @endif
                                        @endif



                                
                                        
                                   
                         
                                       
                               
                                    
                                   



                                 
                                   
                                 
                                    


                       
                                      
                                      
                                   

                                        
                                       
                                    </div>
                                @else
                                    <div class="col-xs-12">
                                        <div class="alert alert-info">@lang('modules.attendance.maxColckIn')</div>
                                    </div>
                                @endif
                            @else
                                <div class="col-xs-12">
                                    <div class="alert alert-info alert-dismissable">
                                        <b>@lang('modules.dashboard.holidayCheck') {{ ucwords($checkTodayHoliday->occassion) }}.</b> </div>
                                </div>
                            @endif
                        @else
                            <div class="col-xs-12 text-center">
                                <h4><i class="ti-alert text-danger"></i></h4>
                                <h4>@lang('messages.officeTimeOver')</h4>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('tasks',$modules))
        <div class="col-md-6">
            <div class="panel panel-inverse">
                <div class="panel-heading">@lang('modules.dashboard.overdueTasks')</div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <ul class="list-task list-group" data-role="tasklist">
                            <li class="list-group-item" data-role="task">
                                <strong>@lang('app.title')</strong> <span
                                        class="pull-right"><strong>@lang('app.dueDate')</strong></span>
                            </li>
                            @forelse($pendingTasks as $key=>$task)
                                @if((!is_null($task->project_id) && !is_null($task->project) ) || is_null($task->project_id))
                                <li class="list-group-item row" data-role="task">
                                    <div class="col-xs-8">
                                        {!! ($key+1).'. <a href="javascript:;" data-task-id="'.$task->id.'" class="show-task-detail">'.ucfirst($task->heading).'</a>' !!}
                                        @if(!is_null($task->project_id) && !is_null($task->project))
                                            <a href="{{ route('member.projects.show', $task->project_id) }}"
                                                class="text-danger">{{ ucwords($task->project->project_name) }}</a>
                                        @endif
                                    </div>
                                    <label class="label label-danger pull-right col-xs-4">{{ $task->due_date->format($global->date_format) }}</label>
                                </li>
                                @endif
                            @empty
                                <li class="list-group-item" data-role="task">
                                    <div  class="text-center">
                                        <div class="empty-space" style="height: 200px;">
                                            <div class="empty-space-inner">
                                                <div class="icon" style="font-size:20px"><i
                                                            class="fa fa-tasks"></i>
                                                </div>
                                                <div class="title m-b-15">@lang("messages.noOpenTasks")
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

    <div class="row" >

        @if(in_array('projects',$modules))
        <div class="col-md-6" id="project-timeline">
            <div class="panel panel-inverse">
                <div class="panel-heading">@lang('modules.dashboard.projectActivityTimeline')</div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="steamline">
                            @forelse($projectActivities as $activity)
                                <div class="sl-item">
                                    <div class="sl-left"><i class="fa fa-circle text-info"></i>
                                    </div>
                                    <div class="sl-right">
                                        <div><h6><a href="{{ route('member.projects.show', $activity->project_id) }}" class="font-bold">{{ ucwords($activity->project_name) }}:</a> {{ $activity->activity }}</h6> <span class="sl-date">{{ $activity->created_at->timezone($global->timezone)->diffForHumans() }}</span></div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center">
                                    <div class="empty-space" style="height: 200px;">
                                        <div class="empty-space-inner">
                                            <div class="icon" style="font-size:20px"><i
                                                        class="fa fa-history"></i>
                                            </div>
                                            <div class="title m-b-15">@lang("messages.noProjectActivity")
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('notices',$modules) && $user->cans('view_notice'))
        <div class="col-md-6" id="notices-timeline">
            <div class="panel panel-inverse">
                <div class="panel-heading">@lang('modules.module.noticeBoard')</div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="steamline">
                            @foreach($notices as $notice)
                                <div class="sl-item">
                                    <div class="sl-left"><i class="fa fa-circle text-info"></i>
                                    </div>
                                    <div class="sl-right">
                                        <div>
                                            <h6>
                                                <a href="javascript:showNoticeModal({{ $notice->id }});" class="text-danger">
                                                    {{ ucwords($notice->heading) }}
                                                </a>
                                            </h6>
                                            <span class="sl-date">
                                                {{ $notice->created_at->timezone($global->timezone)->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('employees',$modules))
        <div class="col-md-6">
            <div class="panel panel-inverse">
                <div class="panel-heading">@lang('modules.dashboard.userActivityTimeline')</div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="steamline">
                            @forelse($userActivities as $key=>$activity)
                                <div class="sl-item">
                                    <div class="sl-left">
                                        <img src="{{ $activity->user->image_url}}" alt="user" class="img-circle">'
                                    </div>
                                    <div class="sl-right">
                                        <div class="m-l-40">
                                            @if($user->cans('view_employees'))
                                                <a href="{{ route('member.employees.show', $activity->user_id) }}" class="text-success">{{ ucwords($activity->user->name) }}</a>
                                            @else
                                                {{ ucwords($activity->user->name) }}
                                            @endif
                                            <span  class="sl-date">{{ $activity->created_at->timezone($global->timezone)->diffForHumans() }}</span>
                                            <p>{!! ucfirst($activity->activity) !!}</p>
                                        </div>
                                    </div>
                                </div>
                                @if(count($userActivities) > ($key+1))
                                    <hr>
                                @endif
                            @empty
                                <div class="text-center">
                                    <div class="empty-space" style="height: 200px;">
                                        <div class="empty-space-inner">
                                            <div class="icon" style="font-size:20px"><i
                                                        class="fa fa-history"></i>
                                            </div>
                                            <div class="title m-b-15">@lang("messages.noActivityByThisUser")
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif



    </div>
</div>

{{--Timer Modal--}}
<div class="modal fade bs-modal-lg in" id="projectTimerModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
            </div>
            <div class="modal-body">
           
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
{{--Timer Modal Ends--}}

{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in"  id="subTaskModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="subTaskModelHeading">Sub Task e</span>
            </div>
            <div class="modal-body">
                Loading...
             
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->.
</div>
{{--Ajax Modal Ends--}}

<div class="modal fade bs-modal-md in"  id="attendanceModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="subTaskModelHeading">Sub Task e</span>
            </div>
            <div class="modal-body">
                <!-- <h1>11</h1> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes66</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->.
</div>
<div class="modal fade bs-modal-md in"  id="attendanceModal1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="subTaskModelHeading">Sub Task e</span>
            </div>
            <div class="modal-body">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->.
</div>

@endsection

@push('footer-script')
<script>
    $('#clock-in').click(function () {
        var workingFrom = $('#working_from').val();

        var currentLatitude = document.getElementById("current-latitude").value;
        var currentLongitude = document.getElementById("current-longitude").value;

        var token = "{{ csrf_token() }}";

        $.easyAjax({
            url: '{{route('member.attendances.store')}}',
            type: "POST",
            data: {
                working_from: workingFrom,
                currentLatitude: currentLatitude,
                currentLongitude: currentLongitude,
                _token: token
            },
            success: function (response) {

                console.log(response);
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    })

    @if(!is_null($currenntClockIn))
    $('#clock-out').click(function () {

        var token = "{{ csrf_token() }}";
        var currentLatitude = document.getElementById("current-latitude").value;
        var currentLongitude = document.getElementById("current-longitude").value;

        $.easyAjax({
            url: '{{route('member.attendances.update', $currenntClockIn->id)}}',
            type: "PUT",
            data: {
                currentLatitude: currentLatitude,
                currentLongitude: currentLongitude,
                _token: token
            },
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    })

    $('#first-open-break').click(function () {

        var token = "{{ csrf_token() }}";
        var currentLatitude = document.getElementById("current-latitude").value;
        var currentLongitude = document.getElementById("current-longitude").value;
        var firstBreakOpen = 1;

        $.easyAjax({
            url: '{{route('member.attendances.update', $currenntClockIn->id)}}',
            type: "PUT",
            data: {
                currentLatitude: currentLatitude,
                currentLongitude: currentLongitude,
                firstBreakOpen: firstBreakOpen,
                _token: token
            },
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    })

    $('#first-close-break').click(function () {

        var token = "{{ csrf_token() }}";
        var currentLatitude = document.getElementById("current-latitude").value;
        var currentLongitude = document.getElementById("current-longitude").value;
        var firstBreakClose = 1;

        $.easyAjax({
            url: '{{route('member.attendances.update', $currenntClockIn->id)}}',
            type: "PUT",
            data: {
                currentLatitude: currentLatitude,
                currentLongitude: currentLongitude,
                firstBreakClose: firstBreakClose,
                _token: token
            },
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    })
    $('#lunch-open-break').click(function () {

        var token = "{{ csrf_token() }}";
        var currentLatitude = document.getElementById("current-latitude").value;
        var currentLongitude = document.getElementById("current-longitude").value;
        var lunchBreakOpen = 1;

        $.easyAjax({
            url: '{{route('member.attendances.update', $currenntClockIn->id)}}',
            type: "PUT",
            data: {
                currentLatitude: currentLatitude,
                currentLongitude: currentLongitude,
                lunchBreakOpen: lunchBreakOpen,
                _token: token
            },
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    })
    $('#lunch-close-break').click(function () {

        var token = "{{ csrf_token() }}";
        var currentLatitude = document.getElementById("current-latitude").value;
        var currentLongitude = document.getElementById("current-longitude").value;
        var lunchBreakClose = 1;

        $.easyAjax({
            url: '{{route('member.attendances.update', $currenntClockIn->id)}}',
            type: "PUT",
            data: {
                currentLatitude: currentLatitude,
                currentLongitude: currentLongitude,
                lunchBreakClose: lunchBreakClose,
                _token: token
            },
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    })
    @endif

    function showNoticeModal(id) {
        var url = '{{ route('member.notices.show', ':id') }}';
        url = url.replace(':id', id);
        $.ajaxModal('#projectTimerModal', url);
    }

    function showAttendanceModal(id) {
        var url = '{{ route('member.attendanceData.editData',':id') }}';
        url = url.replace(':id', id);
        $.ajaxModal('#attendanceModal', url);
    }
    function showAttendanceModal1(id,idd=null) {
        var url = '{{ route('member.attendanceData.editData1',[':id',':idd']) }}';
        url = url.replace(':id', id);
        url = url.replace(':idd', idd);
        $.ajaxModal('#attendanceModal1', url);
    }

    $('.show-task-detail').click(function () {
            $(".right-sidebar").slideDown(50).addClass("shw-rside");

            var id = $(this).data('task-id');
            var url = "{{ route('member.all-tasks.show',':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
                type: 'GET',
                url: url,
                success: function (response) {
                    if (response.status == "success") {
                        $('#right-sidebar-content').html(response.view);
                    }
                }
            });
        })

    $(function () {
        $('.selectpicker').selectpicker();
    });

    $('.language-switcher').change(function () {
        var lang = $(this).val();
        $.easyAjax({
            url: '{{ route("member.language.change-language") }}',
            data: {'lang': lang},
            success: function (data) {
                if (data.status == 'success') {
                    window.location.reload();
                }
            }
        });
    });

</script>

@if ($attendanceSettings->radius_check == 'yes')
<script>
    var currentLatitude = document.getElementById("current-latitude");
    var currentLongitude = document.getElementById("current-longitude");
    var x = document.getElementById("current-latitude");
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
           // x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }

    function showPosition(position) {
        // x.innerHTML = "Latitude: " + position.coords.latitude +
        // "<br>Longitude: " + position.coords.longitude;

        currentLatitude.value = position.coords.latitude;
        currentLongitude.value = position.coords.longitude;
    }
    getLocation();
</script>
@endif

@endpush
