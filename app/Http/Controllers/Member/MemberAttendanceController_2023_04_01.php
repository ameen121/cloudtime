<?php

namespace App\Http\Controllers\Member;

use App\AttedanceSetup;
use App\Attendance;
use App\AttendanceSetting;
use App\EmployeeDetails;
use App\Helper\Reply;
use App\Holiday;
use App\Http\Requests\Attendance\StoreAttendance;
use App\Leave;
use App\Rota;
use App\Team;
use App\Company;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\RestAPI\Entities\Department;
use Yajra\DataTables\Facades\DataTables;

class MemberAttendanceController extends MemberBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = 'icon-clock';
        $this->pageTitle = 'app.menu.attendance';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('attendance', $this->user->modules), 403);
            return $next($request);
        });


        // Getting Attendance setting data
        $this->attendanceSettings = AttendanceSetting::first();

        //Getting Maximum Check-ins in a day
        $this->maxAttandenceInDay = $this->attendanceSettings->clockin_in_day;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

      
     

        $openDays = json_decode($this->attendanceSettings->office_open_days);
        $this->startDate = Carbon::now()->timezone($this->global->timezone)->startOfMonth();
        $this->endDate = Carbon::now()->timezone($this->global->timezone);

 session('logged_session_data.type')=="Branch Manager" ?  $this->employees = User::whereHas('employeeDetail' ,function ($attend)  {
            return $attend->where('department_id',session('logged_session_data.department_id'));;}
     )->get(): $this->employees = User::where('id',session('logged_session_data.user_id'))->get();
        
  
        
        $this->userId = $this->user->id;
        $this->totalWorkingDays = $this->startDate->diffInDaysFiltered(function (Carbon $date) use ($openDays) {
            foreach ($openDays as $day) {
                if ($date->dayOfWeek == $day) {
                    return $date;
                }
            }
        }, $this->endDate);
        $this->daysPresent = Attendance::countDaysPresentByUser($this->startDate, $this->endDate, $this->userId);
        $this->daysLate = Attendance::countDaysLateByUser($this->startDate, $this->endDate, $this->userId);
        $this->halfDays = Attendance::countHalfDaysByUser($this->startDate, $this->endDate, $this->userId);
        $this->holidays = Count(Holiday::getHolidayByDates($this->startDate->format('Y-m-d'), $this->endDate->format('Y-m-d')));
        $this->checkHoliday = Holiday::checkHolidayByDate(Carbon::now()->format('Y-m-d'));

        // Getting Current Clock-in if exist
        $this->currenntClockIn = Attendance::where(DB::raw('DATE(clock_in_time)'), Carbon::today()->format('Y-m-d'))
            ->where('user_id', $this->user->id)->whereNotNull('clock_out_time')->first();
       
       
        // Getting Today's Total Check-ins
        $this->todayTotalClockin = Attendance::where(DB::raw('DATE(clock_in_time)'), Carbon::today()->format('Y-m-d'))
            ->where('user_id', $this->user->id)->where(DB::raw('DATE(clock_out_time)'), Carbon::today()->format('Y-m-d'))->count();

        return view('member.attendance.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->cans('add_attendance'), 403);
        return view('member.attendance.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        
        $now = Carbon::now()->timezone($this->global->timezone);
        //dd($now);
        $clockInCount = Attendance::getTotalUserClockIn($now->format('Y-m-d'), $this->user->id);
        $attendanceSetting = AttendanceSetting::first();
        // Check user by ip
        if ($attendanceSetting->ip_check == 'yes') {
            $ips = (array)json_decode($attendanceSetting->ip_address);
            if (!in_array($request->ip(), $ips)) {
                return Reply::error(__('messages.notAnAuthorisedDevice'));
            }
        }

        // Check user by location
        if ($attendanceSetting->radius_check == 'yes') {
            $checkRadius = $this->isWithinRadius($request);
            if (!$checkRadius) {
                return Reply::error(__('messages.notAnValidLocation'));
            }
        }

        $attendanceSetting = AttendanceSetting::first();

        // Check maximum attendance in a day
        if ($clockInCount < $attendanceSetting->clockin_in_day) {

            // Set TimeZone And Convert into timestamp
            $currentTimestamp = $now->setTimezone('UTC');
            $currentTimestamp = $currentTimestamp->timestamp;

            // Set TimeZone And Convert into timestamp in halfday time
            if ($attendanceSetting->halfday_mark_time) {
                $halfDayTimestamp = $now->format('Y-m-d') . ' ' . $attendanceSetting->halfday_mark_time;
                $halfDayTimestamp = Carbon::createFromFormat('Y-m-d H:i:s', $halfDayTimestamp, $this->global->timezone);
                $halfDayTimestamp = $halfDayTimestamp->setTimezone('UTC');
                $halfDayTimestamp = $halfDayTimestamp->timestamp;
            }

            $rotaAttendance=Rota::where('date',Carbon::now()->timezone($this->global->timezone)->format('Y-m-d'))->where('user_id',Auth::user()->id)->first();
           
        
            // $rotaAttendance=Rota::where('date',Carbon::now()->timezone($this->global->timezone)->format('Y-m-d'))->where('user_id',Auth::user()->id)->first();
           
        
            // $start_time="00:00:00";
            // $start_time= ($rotaAttendance==null)?  $start_time :  $rotaAttendance->start_time;
           
            $timestamp = $now->format('Y-m-d') .' '.$rotaAttendance->start_time;
            // $timestamp = $now->format('Y-m-d') . ' ' . $start_time;

            $officeStartTime = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, $this->global->timezone);
            $officeStartTime = $officeStartTime->setTimezone('UTC');

            $lateTime = $officeStartTime->addMinutes($attendanceSetting->late_mark_duration);

            $checkTodayAttendance = Attendance::where('user_id', $this->user->id)
                ->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $now->format('Y-m-d'))->first();

            $attendance = new Attendance();
            $attendance->user_id = $this->user->id;
            $attendance->clock_in_time = $now;
            $attendance->clock_in_ip = request()->ip();

            if (is_null($request->working_from)) {
                $attendance->working_from = 'office';
            } else {
                $attendance->working_from = $request->working_from;
            }
            \Session::put('clock_in',true);

            if ($lateTime->gt($now) && is_null($checkTodayAttendance)) {
                $attendance->late = 'yes';
            }

            $attendance->half_day = 'no'; // default halfday

            // Check day's first record and half day time
            if (!is_null($attendanceSetting->halfday_mark_time) && is_null($checkTodayAttendance) && $currentTimestamp > $halfDayTimestamp) {
                $attendance->half_day = 'yes';
            }

            $attendance->save();

            return Reply::successWithData(__('messages.attendanceSaveSuccess'), ['time' => $now->format('h:i A'), 'ip' => $attendance->clock_in_ip, 'working_from' => $attendance->working_from]);
        }

        return Reply::error(__('messages.maxColckIn'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
        abort_if(!$this->user->cans('add_attendance'), 403);
        $attendance = Attendance::find($id);

        $this->date = $attendance->clock_in_time->format('Y-m-d');
        $this->row = $attendance;
        $this->clock_in = 1;
        $this->userid = $attendance->user_id;
        $this->total_clock_in = Attendance::where('user_id', $attendance->user_id)
            ->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $this->date)
            ->whereNull('attendances.clock_out_time')->count();
        $this->type = 'edit';
        return view('member.attendance.attendance_mark', $this->data);
    }

    public function editData($id)
    {
       
    
        $attendanceId=$id;
        $data1=Attendance::query()->where('id',$attendanceId)->first();
       
        $oddIds=AttedanceSetup::query()->whereRaw('(id % 2) = 0')->where('company_id',company()->id)->pluck('id')->toArray();
        $data=AttedanceSetup::query()->where('company_id',company()->id)->whereNotIn('id',$oddIds)->where('flag',0)->get();
      
        

      
     
        return view('member.dashboard.edit', compact('data','attendanceId','data1'));
    }

    public function editData1($id,$idd=null)
    {
        
      
        $attendanceId=$id;
        $data1=Attendance::query()->where('id',$attendanceId)->first();
        $data=AttedanceSetup::query()->where('company_id',company()->id)->where('reasons_value',$idd)->get();

        return view('member.dashboard.check_in_edit', compact('data','attendanceId','data1'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateDetails(Request $request, $id)
    {
       
       
       // dd($request->all());
        abort_if(!$this->user->cans('add_attendance'), 403);
        $attendance = Attendance::findOrFail($id);
        $date = Carbon::createFromFormat($this->global->date_format, $request->attendance_date)->format('Y-m-d');
        $clockIn = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date . ' ' . $request->clock_in_time, $this->global->timezone);
        $clockIn->setTimezone('UTC');
        if ($request->clock_out_time != '') {
            $clockOut = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date . ' ' . $request->clock_out_time, $this->global->timezone);
            $clockOut->setTimezone('UTC');

            if ($clockIn->gt($clockOut) && !is_null($clockOut)) {
                return Reply::error(__('messages.clockOutTimeError'));
            }

            $clockIn = $clockIn->toDateTimeString();
            $clockOut = $clockOut->toDateTimeString();
        } else {
            $clockOut = null;
        }

        $attendance->user_id = $request->user_id;
        $attendance->clock_in_time = $clockIn;
        $attendance->clock_in_ip = $request->clock_in_ip;
        $attendance->clock_out_time = $clockOut;
        $attendance->clock_out_ip = $request->clock_out_ip;
        $attendance->working_from = $request->working_from;
        $attendance->late = ($request->has('late')) ? 'yes' : 'no';
        $attendance->half_day = ($request->has('half_day')) ? 'yes' : 'no';
        $attendance->save();

        return Reply::success(__('messages.attendanceSaveSuccess'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       
        // dd($request->all());
      
        $now = Carbon::now();
        $attendance = Attendance::findOrFail($id);
        if ($attendance) {
            if ($this->attendanceSettings->ip_check == 'yes') {
                $ips = (array)json_decode($this->attendanceSettings->ip_address);
                if (!in_array($request->ip(), $ips)) {
                    return Reply::error(__('messages.notAnAuthorisedDevice'));
                }
            }
            if ($this->attendanceSettings->radius_check == 'yes') {
                $checkRadius = $this->isWithinRadius($request);
                if (!$checkRadius) {
                    return Reply::error(__('messages.notAnValidLocation'));
                }
            }
            if ($request->reason==1) {
                $attendance->first_break_start = $now;
               
               
                \Session::put('clock_in',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);

                \Session::put('first_break_end',true);
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
            } elseif ($request->reason==2) {
                $attendance->first_break_end = $now;
                $atten_setup=AttedanceSetup::where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->where('reasons_value',1)->first();
                $atten_setup->flag=1;
                $atten_setup->save();
                \Session::put('clock_in',true);
               
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
            }elseif ($request->reason==3) {
                $attendance->second_break_start = $now;
                \Session::put('clock_in',false);
                \Session::put('second_break_end',true);
                \Session::put('first_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
                
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
            }elseif ($request->reason==4) {
                \Session::put('clock_in',true);
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
                $atten_setup=AttedanceSetup::where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->where('reasons_value',3)->first();
                $atten_setup->flag=1;
                $atten_setup->save();
               
                
                $attendance->second_break_end = $now;
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
            }elseif ($request->reason==5) {
                \Session::put('clock_in',false);
              
                \Session::put('third_break_end',true);
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
                $attendance->third_break_start = $now;
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
            }elseif ($request->reason==6) {
                $attendance->third_break_end = $now;
                $atten_setup=AttedanceSetup::where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->where('reasons_value',5)->first();
                $atten_setup->flag=1;
                $atten_setup->save();
                \Session::put('clock_in',true);
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
                $attendance->third_break_start = $now;
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
            }elseif ($request->reason==7) {
                $attendance->four_break_start = $now;
                \Session::put('clock_in',false);
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('four_break_end',true);
                \Session::put('third_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
                
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
            }elseif ($request->reason==8) {
                \Session::put('clock_in',true);
                $atten_setup=AttedanceSetup::where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->where('reasons_value',7)->first();
                $atten_setup->flag=1;
                $atten_setup->save();
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
                $attendance->four_break_end = $now;
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
            }elseif ($request->reason==9) {

                \Session::put('clock_in',false);
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
                \Session::put('five_break_end',true);
                $attendance->five_break_start = $now;
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
            }elseif($request->reason==10) {
                
                $atten_setup=AttedanceSetup::where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->where('reasons_value',9)->first();
                $atten_setup->flag=1;
                $atten_setup->save();
                $attendance->five_break_end = $now;
                \Session::put('clock_in',true);
            
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
                \Session::put('five_break_end',false);
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
              
            }elseif($request->reason==11) {
                \Session::put('clock_in',false);
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',true);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
          

                
                
                $attendance->six_break_start = $now;
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
               
            }elseif($request->reason==12) {
                $atten_setup=AttedanceSetup::where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->where('reasons_value',11)->first();
                $atten_setup->flag=1;
                $atten_setup->save();
                \Session::put('clock_in',true);
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
                $attendance->six_break_end = $now;
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
               
            }elseif($request->reason==13) {
                $attendance->seven_break_start = $now;
                \Session::put('clock_in',false);
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',true);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
               
            }elseif($request->reason==14) {
                $atten_setup=AttedanceSetup::where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->where('reasons_value',13)->first();
                $atten_setup->flag=1;
                $atten_setup->save();
                $attendance->seven_break_end = $now;
                \Session::put('clock_in',true);
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
               
            }elseif($request->reason==15) {
                $attendance->eight_break_start = $now;
                \Session::put('clock_in',false);
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',true);
                \Session::put('nine_break_end',false);
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
               
            }elseif($request->reason==16) {
                $atten_setup=AttedanceSetup::where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->where('reasons_value',15)->first();
                $atten_setup->flag=1;
                $atten_setup->save();
                $attendance->eight_break_end = $now;
                \Session::put('clock_in',true);
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
               
            }elseif($request->reason==17) {
                $attendance->nine_break_start = $now;
                \Session::put('clock_in',false);
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',true);
                
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
               
            }elseif($request->reason==18) {
                $atten_setup=AttedanceSetup::where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->where('reasons_value',17)->first();
                $atten_setup->flag=1;
                $atten_setup->save();
                $attendance->nine_break_end = $now;
                \Session::put('clock_in',true);
                \Session::put('first_break_end',false);
                \Session::put('second_break_end',false);
                \Session::put('third_break_end',false);
                \Session::put('four_break_end',false);
                \Session::put('five_break_end',false);
                \Session::put('six_break_end',false);
                \Session::put('seven_break_end',false);
                \Session::put('eight_break_end',false);
                \Session::put('nine_break_end',false);
                
                //$attendance->clock_out_ip = request()->ip();
                $attendance->save();
               
            }else {

              $setup= AttedanceSetup::where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->where('flag', 1)->update(['flag' => 0]);
              $attendance->clock_out_time = $now;
              $attendance->clock_out_ip = request()->ip();
              \Session::put('clock_in',false);
              \Session::put('first_break_end',false);
              \Session::put('second_break_end',false);
              \Session::put('third_break_end',false);
              \Session::put('four_break_end',false);
              \Session::put('five_break_end',false);
              \Session::put('six_break_end',false);
              \Session::put('seven_break_end',false);
              \Session::put('eight_break_end',false);
              \Session::put('nine_break_end',false);
              $attendance->save();
            }

        }

        return back()->with('success','Attendance successfully!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Attendance::destroy($id);
        return Reply::success(__('messages.attendanceDelete'));
    }

    public function refreshCount(Request $request, $startDate = null, $endDate = null, $userId = null)
    {
        $this->attendanceSettings = AttendanceSetting::first();
        $openDays = json_decode($this->attendanceSettings->office_open_days);
        // $startDate = Carbon::createFromFormat('!Y-m-d', $startDate);
        // $endDate = Carbon::createFromFormat('!Y-m-d', $endDate)->addDay(1); //addDay(1) is hack to include end date
        $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate);
        $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->addDay(1); //addDay(1) is hack to include end date
        $userId = $request->userId;

        $totalWorkingDays = $startDate->diffInDaysFiltered(function (Carbon $date) use ($openDays) {
            foreach ($openDays as $day) {
                if ($date->dayOfWeek == $day) {
                    return $date;
                }
            }
        }, $endDate);
        $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate);
        $daysPresent = Attendance::countDaysPresentByUser($startDate, $endDate, $userId);
        $daysLate = Attendance::countDaysLateByUser($startDate, $endDate, $userId);
        $halfDays = Attendance::countHalfDaysByUser($startDate, $endDate, $userId);
        $daysAbsent = (($totalWorkingDays - $daysPresent) < 0) ? '0' : ($totalWorkingDays - $daysPresent);
        $holidays = Count(Holiday::getHolidayByDates($startDate->format('Y-m-d'), $endDate->format('Y-m-d')));

        return Reply::dataOnly(['daysPresent' => $daysPresent, 'daysLate' => $daysLate, 'halfDays' => $halfDays, 'totalWorkingDays' => $totalWorkingDays, 'absentDays' => $daysAbsent, 'holidays' => $holidays]);
    }
    public function employeeData(Request $request, $startDate = null, $endDate = null, $userId = null)
    {

        // dd();
        $this->attendanceSettings = AttendanceSetting::first();
        $ant = []; // Array For attendance Data indexed by similar date
        $dateWiseData = []; // Array For Combine Data

        $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->startOfDay();
        $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->endOfDay()->addDay(1);

        $attendances = Attendance::userAttendanceByDate($request->startDate, $request->endDate, $userId); // Getting Attendance Data
    //    dd(  $attendances );
        
        $holidays = Holiday::getHolidayByDates($startDate, $endDate); // Getting Holiday Data

        // Getting Leaves Data
        $leavesDates = Leave::where('user_id', $userId)
            ->where('leave_date', '>=', $startDate)
            ->where('leave_date', '<=', $endDate)
            ->where('status', 'approved')
            ->select('leave_date', 'reason')
            ->get()->keyBy('date')->toArray();

        $holidayData = $holidays->keyBy('holiday_date');
        $holidayArray = $holidayData->toArray();

        // Set Date as index for same date clock-ins
        foreach ($attendances as $attand) {
            $ant[$attand->clock_in_date][] = $attand; // Set attendance Data indexed by similar date
        }

        $endDate = Carbon::parse($request->endDate)->timezone($this->global->timezone);
        $startDate = Carbon::parse($request->startDate)->timezone($this->global->timezone)->subDay();
//dd($endDate);
        // Set All Data in a single Array
        for ($date = $endDate; $date->diffInDays($startDate) > 0; $date->subDay()) {

            // Set default array for record
            $dateWiseData[$date->toDateString()] = [
                'holiday' => false,
                'attendance' => false,
                'leave' => false
            ];

            // Set Holiday Data
            if (array_key_exists($date->toDateString(), $holidayArray)) {
                $dateWiseData[$date->toDateString()]['holiday'] = $holidayData[$date->toDateString()];
            }

            // Set Attendance Data
            if (array_key_exists($date->toDateString(), $ant)) {
                $dateWiseData[$date->toDateString()]['attendance'] = $ant[$date->toDateString()];
            }

            // Set Leave Data
            if (array_key_exists($date->toDateString(), $leavesDates)) {
                $dateWiseData[$date->toDateString()]['leave'] = $leavesDates[$date->toDateString()];
            }
        }
         $company= Company::where('id',\Illuminate\Support\Facades\Auth::user()->company_id)->first();
        // Getting View data
        $view = view('member.attendance.user_attendance', ['company'=> $company, 'dateWiseData' => $dateWiseData, 'global' => $this->global])->render();

        return Reply::dataOnly(['status' => 'success', 'data' => $view]);
    }
    public function dateAttendanceCount(Request $request)
    {
        $date = Carbon::createFromFormat($this->global->date_format, $request->date)->format('Y-m-d');
        $checkHoliday = Holiday::checkHolidayByDate($date);
        $totalPresent = 0;
        $totalAbsent  = 0;
        $holiday  = 0;
        $holidayReason  = '';
        $totalEmployees = count(User::allEmployees());

        if (!$checkHoliday) {
            $totalPresent = Attendance::where(DB::raw('DATE(`clock_in_time`)'), '=', $date)->count();
            $totalAbsent = ($totalEmployees - $totalPresent);
        } else {
            $holiday = 1;
            $holidayReason = $checkHoliday->occassion;
        }

        return Reply::dataOnly(['status' => 'success', 'totalEmployees' => $totalEmployees, 'totalPresent' => $totalPresent, 'totalAbsent' => $totalAbsent, 'holiday' => $holiday, 'holidayReason' => $holidayReason]);
    }

    public function byDateData(Request $request)
    {
        
       
        $this->attendanceSettings = AttendanceSetting::first();
        $date = Carbon::createFromFormat($this->global->date_format, $request->date)->format('Y-m-d');
       session('logged_session_data.type')=="Branch Manager" ?
        $attendances= User::with(['attendance' => function ($q) use ($date) {
            $q->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $date);
        }])
       
        ->withoutGlobalScope('active')
        ->join('role_user', 'role_user.user_id', '=', 'users.id')
        ->join('roles', 'roles.id', '=', 'role_user.role_id')
        ->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
        ->leftJoin('designations', 'designations.id', '=', 'employee_details.designation_id')
        ->where('roles.name', '<>', 'client')
        ->where('employee_details.department_id',session('logged_session_data.department_id'))
        ->select(
            'users.id',
            'users.name',
            'users.image',
            'designations.name as designation_name'
        )
        ->groupBy('users.id')
        ->orderBy('users.name', 'asc')
        :
       
        $attendances= User::with(['attendance' => function ($q) use ($date) {
            $q->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $date);
        }])
       
        ->withoutGlobalScope('active')
        ->join('role_user', 'role_user.user_id', '=', 'users.id')
        ->join('roles', 'roles.id', '=', 'role_user.role_id')
        ->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
        ->leftJoin('designations', 'designations.id', '=', 'employee_details.designation_id')
        ->where('roles.name', '<>', 'client')
        ->where('users.id',session('logged_session_data.user_id'))
        ->select(
            'users.id',
            'users.name',
            'users.image',
            'designations.name as designation_name'
        )
        ->groupBy('users.id')
        ->orderBy('users.name', 'asc')
       
       
       
       
        ;

        return DataTables::of($attendances)
            ->editColumn('id', function ($row) {
                return view('admin.attendance.attendance_date_list', ['row' => $row, 'global' => $this->global])->render();
            })
            ->rawColumns(['id'])
            ->removeColumn('name')
            ->removeColumn('clock_in_time')
            ->removeColumn('clock_out_time')
            ->removeColumn('image')
            ->removeColumn('attendance_id')
            ->removeColumn('working_from')
            ->removeColumn('late')
            ->removeColumn('half_day')
            ->removeColumn('clock_in_ip')
            ->removeColumn('designation_name')
            ->make();
    }
public function attendanceByDate(Request $request)
    {

//  $date = Carbon::createFromFormat($this->global->date_format, '2022-12-18')->format('Y-m-d');
       
      
        return view('member.attendance.by_date', $this->data);
    }


    public function data(Request $request)
    {

        $date = Carbon::createFromFormat($this->global->date_format, $request->date)->format('Y-m-d');
        $attendances = Attendance::attendanceByDate($date);

        return DataTables::of($attendances)
            ->editColumn('id', function ($row) {
                return view('member.attendance.attendance_list', ['row' => $row, 'global' => $this->global, 'maxAttandenceInDay' => $this->maxAttandenceInDay])->render();
            })
            ->rawColumns(['id'])
            ->removeColumn('name')
            ->removeColumn('clock_in_time')
            ->removeColumn('clock_out_time')
            ->removeColumn('image')
            ->removeColumn('attendance_id')
            ->removeColumn('working_from')
            ->removeColumn('late')
            ->removeColumn('half_day')
            ->removeColumn('clock_in_ip')
            ->removeColumn('designation_name')
            ->removeColumn('clock_in')
            ->removeColumn('total_clock_in')
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeAttendance(StoreAttendance $request)
    {
    
       
        $date = Carbon::createFromFormat($this->global->date_format, $request->date)->format('Y-m-d');
        $clockIn = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date . ' ' . $request->clock_in_time, $this->global->timezone);
        $clockIn->setTimezone('UTC');
        if ($request->clock_out_time != '') {
            $clockOut = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date . ' ' . $request->clock_out_time, $this->global->timezone);
            $clockOut->setTimezone('UTC');

            if ($clockIn->gt($clockOut) && !is_null($clockOut)) {
                return Reply::error(__('messages.clockOutTimeError'));
            }

            $clockIn = $clockIn->toDateTimeString();
            $clockOut = $clockOut->toDateTimeString();
        } else {
            $clockOut = null;
        }

        $clockInCount = Attendance::getTotalUserClockIn($date, $request->user_id);

        $attendance = Attendance::where('user_id', $request->user_id)
            ->where(DB::raw('DATE(`clock_in_time`)'), $date)
            ->whereNull('clock_out_time')
            ->first();

        if (!is_null($attendance)) {
            $attendance->update([
                'user_id' => $request->user_id,
                'clock_in_time' => $clockIn,
                'clock_in_ip' => $request->clock_in_ip,
                'clock_out_time' => $clockOut,
                'clock_out_ip' => $request->clock_out_ip,
                'working_from' => $request->working_from,
                'late' => $request->late,
                'half_day' => $request->half_day
            ]);
        } else {

            // Check maximum attendance in a day
            if ($clockInCount < $this->attendanceSettings->clockin_in_day) {
                Attendance::create([
                    'user_id' => $request->user_id,
                    'clock_in_time' => $clockIn,
                    'clock_in_ip' => $request->clock_in_ip,
                    'clock_out_time' => $clockOut,
                    'clock_out_ip' => $request->clock_out_ip,
                    'working_from' => $request->working_from,
                    'late' => $request->late,
                    'half_day' => $request->half_day
                ]);
            } else {
                return Reply::error(__('messages.maxColckIn'));
            }
        }

        return Reply::success(__('messages.attendanceSaveSuccess'));
    }

    public function checkHoliday(Request $request)
    {

        $date = Carbon::createFromFormat($this->global->date_format, $request->date)->format('Y-m-d');
        $checkHoliday = Holiday::checkHolidayByDate($date);
        return Reply::dataOnly(['status' => 'success', 'holiday' => $checkHoliday]);
    }

    // Attendance Detail Show
    public function attendanceDetail(Request $request)
    {

        // Getting Attendance Data By User And Date
        $this->attendances = Attendance::attedanceByUserAndDate($request->date, $request->userID);
        return view('member.attendance.attendance-detail', $this->data)->render();
    }

    /**
     * Calculate distance between two geo coordinates using Haversine formula and then compare
     * it with $radius.
     *
     * If distance is less than the radius means two points are close enough hence return true.
     * Else return false.
     *
     * @param array $userCoordinates
     * @param array $requestCoordinates
     * @param int $radius
     *
     * @return boolean
     */
    private function isWithinRadius($request)
    {
        $attendanceSetting = AttendanceSetting::first();
        $radius = $attendanceSetting->radius;
        $currentLatitude = $request->currentLatitude;
        $currentLongitude = $request->currentLongitude;
        // $requestCoordinates = $this->checkFromIp($request);

        $latFrom = deg2rad($this->global->latitude);
        $latTo = deg2rad($currentLatitude);

        $lonFrom = deg2rad($this->global->longitude);
        $lonTo = deg2rad($currentLongitude);

        $theta = $lonFrom - $lonTo;

        $dist = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($theta);
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $distance = $dist * 60 * 1.1515 * 1609.344;
        return $distance <= $radius;
    }

    private function checkFromIp($request)
    {
        $userIp = $request->ip();
        // If app env is local $userIp will evaluate to local server address i.e. 127.0.0.1,
        // for which we can not check ip based location track, hence I am presuming that if
        // env is local then we are by default getting requests from following ip address.
        if (config('app.env') == 'local') {
            $userIp = '112.133.241.11';
        }

        // Try fetching geo location from header request ip address.
        $ipData = (array)json_decode(file_get_contents("http://ipinfo.io/{$userIp}/json"));
        if ($ipData) {

            if (!empty($ipData) && !empty($ipData['loc'])) {
                $requestCoordinates = explode(',', $ipData['loc']);
                return $requestCoordinates;
            }

            return false;
        } else {

            return false;
        }
    }

    public function summary()


    {
         session('logged_session_data.type')=="Branch Manager" ?  $this->employees = User::whereHas('employeeDetail' ,function ($attend)  {
            return $attend->where('department_id',session('logged_session_data.department_id'));;}
     )->get(): $this->employees = User::where('id',session('logged_session_data.user_id'))->get();
        
        

    //  $this->employees = User::allEmployees()->where('id',session('logged_session_data.user_id'));
        // $this->employees =   EmployeeDetails::where('user_id',session('logged_session_data.user_id'))->get();
        //  session('logged_session_data.type')=="Branch Manager" ?  $this->employees = User::where('company_id',session('logged_session_data.company_id'))->get(): $this->employees = User::where('id',session('logged_session_data.user_id'))->get();
    //  dd($this->employees);
        
        
        $now = Carbon::now();
        $this->year = $now->format('Y');
        $this->month = $now->format('m');
        $this->departments = Team::all();

        return view('member.attendance.summary', $this->data);
    }

    public function summaryData(Request $request)
    {

        //  abort_if(!$this->user->cans('add_attendance'), 403);
        $employees = User::with(
            ['attendance' => function ($query) use ($request) {
                $query->whereRaw('MONTH(attendances.clock_in_time) = ?', [$request->month])
                    ->whereRaw('YEAR(attendances.clock_in_time) = ?', [$request->year]);
            }]
        )->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at', 'users.company_id', 'employee_details.department_id','users.image')
            ->where('roles.name', '<>', 'client')
            ->groupBy('users.id');


        // if ($request->department == 0 && $request->userId == 0 ) {
        //     $employees = $employees->where('employee_details.department_id', session('logged_session_data.department_id'));
        // }
        session('logged_session_data.type')=="Branch Manager" ?
       $employees = $employees->where('employee_details.department_id', session('logged_session_data.department_id')):
          
        $employees = $employees->where('users.id' ,session('logged_session_data.user_id'));
        
        

        if ($request->userId !=0) {
            $employees = $employees->where('users.id', $request->userId);
        }

        $employees = $employees->get();
        $this->holidays = Holiday::whereRaw('MONTH(holidays.date) = ?', [$request->month])->whereRaw('YEAR(holidays.date) = ?', [$request->year])->get();

        $final = [];

        $this->daysInMonth = Carbon::parse('01-' . $request->month . '-' . $request->year)->daysInMonth;
        $now = Carbon::now()->timezone($this->global->timezone);
        $requestedDate = Carbon::parse(Carbon::parse('01-' . $request->month . '-' . $request->year))->endOfMonth();

        foreach ($employees as $employee) {


            $dataTillToday = array_fill(1, $now->copy()->format('d'), 'Absent');

            $dataFromTomorrow = [];
            if (($now->copy()->format('d') != $this->daysInMonth) && !$requestedDate->isPast()) {
                $dataFromTomorrow = array_fill($now->copy()->addDay()->format('d'), ($this->daysInMonth - $now->copy()->format('d')), '-');
            } else {
                $dataFromTomorrow = array_fill($now->copy()->addDay()->format('d'), ($this->daysInMonth - $now->copy()->format('d')), 'Absent');
            }
            $final[$employee->id . '#' . $employee->name] = array_replace($dataTillToday, $dataFromTomorrow);

            foreach ($employee->attendance as $attendance) {
                $final[$employee->id . '#' . $employee->name][Carbon::parse($attendance->clock_in_time)->timezone($this->global->timezone)->day] = '<a href="javascript:;" class="view-attendance" data-attendance-id="' . $attendance->id . '"><i class="fa fa-check text-success"></i></a>';
            }

            $image = '<img src="' . $employee->image_url . '" alt="user" class="img-circle" width="30" height="30"> ';
            $final[$employee->id . '#' . $employee->name][] = '<a class="userData" id="userID' . $employee->id . '" data-employee-id="' . $employee->id . '"  href="' . route('admin.employees.show', $employee->id) . '">' . $image . ' ' . ucwords($employee->name) . '</a>';

            foreach ($this->holidays as $holiday) {
                if ($final[$employee->id . '#' . $employee->name][$holiday->date->day] == 'Absent') {
                    $final[$employee->id . '#' . $employee->name][$holiday->date->day] = 'Holiday';
                }
            }

            if ($employee->employeeDetail->joining_date->greaterThan(Carbon::parse(Carbon::parse('01-' . $request->month . '-' . $request->year)))) {
                if ($request->month == $employee->employeeDetail->joining_date->format('m') && $request->year == $employee->employeeDetail->joining_date->format('Y')) {
                    $dataBeforeJoin = array_fill(1, $employee->employeeDetail->joining_date->subDay()->format('d'), '-');
                }
                if (($request->month < $employee->employeeDetail->joining_date->format('m') && $request->year == $employee->employeeDetail->joining_date->format('Y')) || $request->year < $employee->employeeDetail->joining_date->format('Y')) {
                    $dataBeforeJoin = array_fill(1, $this->daysInMonth, '-');
                }
            }

            if (Carbon::parse('01-' . $request->month . '-' . $request->year)->isFuture()) {

                $dataBeforeJoin = array_fill(1, $this->daysInMonth, '-');
            }
            if (isset($dataBeforeJoin)) {
                $final[$employee->id . '#' . $employee->name] = array_replace($final[$employee->id . '#' . $employee->name], $dataBeforeJoin);
            }
            foreach ($this->holidays as $holiday) {
                if ($final[$employee->id . '#' . $employee->name][$holiday->date->day] == 'Absent') {
                    $final[$employee->id . '#' . $employee->name][$holiday->date->day] = 'Holiday';
                }
            }
        }
        //
        $this->employeeAttendence = $final;

        $view = view('member.attendance.summary_data', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'data' => $view]);
    }

    public function detail($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        $this->attendanceActivity = Attendance::userAttendanceByDate($attendance->clock_in_time->format('Y-m-d'), $attendance->clock_in_time->format('Y-m-d'), $attendance->user_id);

        $attendanceActivity = clone $this->attendanceActivity;
        $attendanceActivity = $attendanceActivity->reverse()->values();

        $defaultEndTime = $settingEndTime = Carbon::createFromFormat('H:i:s', $this->attendanceSettings->office_end_time, $this->global->timezone);

        if ($settingEndTime->greaterThan(now()->timezone($this->global->timezone))) {
            $defaultEndTime = now()->timezone($this->global->timezone);
        }

        $this->totalTime = 0;

        foreach ($attendanceActivity as $key => $activity) {
            if ($key == 0) {
                $this->firstClockIn = $activity;
                $this->startTime = Carbon::parse($this->firstClockIn->clock_in_time)->timezone($this->global->timezone);
            }

            $this->lastClockOut = $activity;

            if (!is_null($this->lastClockOut->clock_out_time)) {
                $this->endTime = Carbon::parse($this->lastClockOut->clock_out_time)->timezone($this->global->timezone);

            } elseif (($this->lastClockOut->clock_in_time->timezone($this->global->timezone)->format('Y-m-d') != Carbon::now()->timezone($this->global->timezone)->format('Y-m-d')) && is_null($this->lastClockOut->clock_out_time)) {
                $this->endTime = Carbon::parse($this->startTime->format('Y-m-d') . ' ' . $this->attendanceSettings->office_end_time, $this->global->timezone);
                $this->notClockedOut = true;

            } else {
                $this->endTime = $defaultEndTime;
                $this->notClockedOut = true;
            }

            $this->totalTime = $this->totalTime + $this->endTime->timezone($this->global->timezone)->diffInMinutes($activity->clock_in_time->timezone($this->global->timezone), true);
        }

        $totalTime = intdiv($this->totalTime, 60) . ' ' . __('app.hrs') . ' ';

        if (($this->totalTime % 60) > 0) {
            $totalTime .= ($this->totalTime % 60) . ' ' . __('app.mins');
        }

        $this->totalTime = $totalTime;

        $this->user_attendance = $attendance;
        $this->attendance = $attendance;


        return view('member.attendance.attendance_info', $this->data);
    }

    public function mark(Request $request, $userid, $day, $month, $year)
    {
        abort_if(!$this->user->cans('add_attendance'), 403);

        $this->date = Carbon::createFromFormat('d-m-Y', $day . '-' . $month . '-' . $year)->format('Y-m-d');
        $this->row = Attendance::attendanceByUserDate($userid, $this->date);
        $this->clock_in = 0;
        $this->total_clock_in = Attendance::where('user_id', $userid)
            ->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $this->date)
            ->whereNull('attendances.clock_out_time')->count();

        $this->userid = $userid;
        $this->type = 'add';
        return view('member.attendance.attendance_mark', $this->data);
    }

    public function storeMark(StoreAttendance $request)
    {
        abort_if(!$this->user->cans('add_attendance'), 403);

        $date = Carbon::createFromFormat($this->global->date_format, $request->attendance_date)->format('Y-m-d');

        $clockIn = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date . ' ' . $request->clock_in_time, $this->global->timezone);
        $clockIn->setTimezone('UTC');
        if ($request->clock_out_time != '') {
            $clockOut = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date . ' ' . $request->clock_out_time, $this->global->timezone);
            $clockOut->setTimezone('UTC');

            if ($clockIn->gt($clockOut) && !is_null($clockOut)) {
                return Reply::error(__('messages.clockOutTimeError'));
            }

            $clockIn = $clockIn->toDateTimeString();
            $clockOut = $clockOut->toDateTimeString();
        } else {
            $clockOut = null;
        }

        $attendance = Attendance::where('user_id', $request->user_id)
            ->where(DB::raw('DATE(`clock_in_time`)'), "$date")
            ->whereNull('clock_out_time')
            ->first();

        $clockInCount = Attendance::getTotalUserClockIn($date, $request->user_id);

        if (!is_null($attendance)) {
            $attendance->update([
                'user_id' => $request->user_id,
                'clock_in_time' => $clockIn,
                'clock_in_ip' => $request->clock_in_ip,
                'clock_out_time' => $clockOut,
                'clock_out_ip' => $request->clock_out_ip,
                'working_from' => $request->working_from,
                'late' => ($request->has('late')) ? 'yes' : 'no',
                'half_day' => ($request->has('half_day')) ? 'yes' : 'no'
            ]);
        } else {

            // Check maximum attendance in a day
            if ($clockInCount < $this->attendanceSettings->clockin_in_day) {
                Attendance::create([
                    'user_id' => $request->user_id,
                    'clock_in_time' => $clockIn,
                    'clock_in_ip' => $request->clock_in_ip,
                    'clock_out_time' => $clockOut,
                    'clock_out_ip' => $request->clock_out_ip,
                    'working_from' => $request->working_from,
                    'late' => ($request->has('late')) ? 'yes' : 'no',
                    'half_day' => ($request->has('half_day')) ? 'yes' : 'no'
                ]);
            } else {
                return Reply::error(__('messages.maxColckIn'));
            }
        }

        return Reply::success(__('messages.attendanceSaveSuccess'));
    }

}
