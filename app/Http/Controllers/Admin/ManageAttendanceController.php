<?php

namespace App\Http\Controllers\Admin;

use App\Attendance;
use App\AttendanceSetting;
use App\AttedanceSetup;
use App\EmployeeDetails;
use App\Exports\LeaveReportExport;
use App\Helper\Reply;
use App\Holiday;
use App\Http\Requests\Attendance\StoreAttendance;
use App\Http\Requests\Attendance\StoreBulkAttendance;
use App\Leave;
use App\Project;
use App\ProjectMember;
use App\Team;
use App\User;
use App\Company;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use DateTime;

/**
 * Class ManageAttendanceController
 * @package App\Http\Controllers\Admin
 */
class ManageAttendanceController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.attendance';
        $this->pageIcon = 'icon-clock';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('attendance', $this->user->modules), 403);
            return $next($request);
        });


        // Getting Attendance setting data

        //Getting Maximum Check-ins in a day
       
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    
    
     function dateDiffInDays($date1, $date2) 
    {
      // Calculating the difference in timestamps
      $diff = strtotime($date2) - strtotime($date1);
    
      // 1 day = 24 hours
      // 24 * 60 * 60 = 86400 seconds
      return abs(round($diff / 86400));
    }
    public function index()
    {

        //  dd('ggg');
        
          // Start date

      
        $company=\App\Company::where('id',\Illuminate\Support\Facades\Auth::user()->company_id)->first();
        $todayDate =  date('Y-m-d h:i:s a');
      
        $tz = $company->timezone;
        $timestamp = strtotime($todayDate);
        $dt = new \DateTime("now", new \DateTimeZone($company->timezone)); //first argument "must" be a string
        $dt->setTimestamp($timestamp);
        
         //dd($dt->format('Y-m-d h:i:s a'));
         $date1 = $todayDate;
         $date2 = $dt->format('Y-m-d h:i:s a');
         $date1= date('Y-m-d',strtotime($date1));
         $date2= date('Y-m-d',strtotime($date2));
       
         if($date1 != $date2){
             $this->days = 1;
         }else{
              $this->days = 0;
         }
         
       
        
       
       // $dateDiff = $this->dateDiffInDays($date1, $date2);
        // $this->days = (int)$dateDiff;
      
        
        
        $yesterday=date('Y-m-d h:i:s a', strtotime('-1 day', strtotime( $date2 )));
     
      
        $chk_yesterday=date('Y-m-d',strtotime($yesterday));
        $todayDate=date('Y-m-d',strtotime($todayDate));
      
        $dateDifff = $this->dateDiffInDays($chk_yesterday, $todayDate);
        $this->yesterDays = (int)$dateDifff;
       
       
         if($this->yesterDays==0){
             
            $this->yesterDays=0;
            
        }
      
        $this->attendanceSettings = AttendanceSetting::first();
        $openDays = json_decode($this->attendanceSettings->office_open_days);
        $this->startDate = Carbon::today()->timezone($this->global->timezone)->startOfMonth();
        $this->endDate = Carbon::now()->timezone($this->global->timezone);
        $this->employees = User::allEmployees();
        $this->userId = User::first()->id;
        $this->departments = Team::all();

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
        
        return view('admin.attendance.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.attendance.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAttendance $request)
    {
        $this->attendanceSettings = AttendanceSetting::first();
        $date = Carbon::createFromFormat($this->global->date_format, $request->date)->format('Y-m-d');
        $clockIn = Carbon::createFromFormat('Y-m-d '.$this->global->time_format, $date.' '.$request->clock_in_time, $this->global->timezone);
        $clockIn->setTimezone('UTC');
        if ($request->clock_out_time != '') {
            $clockOut = Carbon::createFromFormat('Y-m-d '.$this->global->time_format, $date.' '.$request->clock_out_time, $this->global->timezone);
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
            ->where(DB::raw('DATE(`clock_in_time`)'), $date)
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $attendance = Attendance::find($id);
        $this->attendanceSettings = AttendanceSetting::first();
        $this->date = $attendance->clock_in_time->format('Y-m-d');
        $this->row = $attendance;
        $this->clock_in = 1;
        $this->userid = $attendance->user_id;
        $this->total_clock_in  = Attendance::where('user_id', $attendance->user_id)
            ->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $this->date)
            ->whereNull('attendances.clock_out_time')->count();
        $this->type = 'edit';
        return view('admin.attendance.attendance_mark', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->attendanceSettings = AttendanceSetting::first();
        $attendance = Attendance::findOrFail($id);
        $date = Carbon::createFromFormat($this->global->date_format, $request->attendance_date)->format('Y-m-d');

        $clockIn = Carbon::createFromFormat('Y-m-d '.$this->global->time_format, $date.' '.$request->clock_in_time, $this->global->timezone);
        $clockIn->setTimezone('UTC');
        if ($request->clock_out_time != '') {
            $clockOut = Carbon::createFromFormat('Y-m-d '.$this->global->time_format, $date.' '.$request->clock_out_time, $this->global->timezone);
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
        $attendance->half_day = ($request->has('halfday'))? 'yes' : 'no';
        $attendance->save();

        return Reply::success(__('messages.attendanceSaveSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Attendance::destroy($id);
        return Reply::success(__('messages.attendanceDelete'));
    }

    public function data(Request $request)
    {
        $this->attendanceSettings = AttendanceSetting::first();
        $date = Carbon::createFromFormat($this->global->date_format, $request->date)->format('Y-m-d');
        $attendances = Attendance::attendanceByDate($date);
        $this->maxAttandenceInDay = $this->attendanceSettings->clockin_in_day;
        return DataTables::of($attendances)
            ->editColumn('id', function ($row) {
                return view('admin.attendance.attendance_list', ['row' => $row, 'global' => $this->global, 'maxAttandenceInDay' => $this->maxAttandenceInDay])->render();
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
            ->removeColumn('total_clock_in')
            ->removeColumn('clock_in')
            ->make();
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
       
        $this->attendanceSettings = AttendanceSetting::first();
        $ant = []; // Array For attendance Data indexed by similar date
        $dateWiseData = []; // Array For Combine Data

        $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->startOfDay();
        $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->endOfDay();
     
     
        $attendances = Attendance::userAttendanceByDate($startDate, $endDate, $userId); // Getting Attendance Data
       
        
        //  $attendances = $attendances->map(function($name, $key) {
            
        // });
        $attendanceSetup=\App\AttedanceSetup::orderBy('id','asc')->where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->get();
        $company=\App\Company::where('id',\Illuminate\Support\Facades\Auth::user()->company_id)->first();
        $arrData=[];
        if($attendanceSetup->count() > 0){
            $first_start_value = $attendanceSetup->where('reasons_value',1);
            if($first_start_value->count()>0){
                $arrData['s1']=$first_start_value->first()->name;
            }else{
                $arrData['s1']="";
            }
            $first_end_value = $attendanceSetup->where('reasons_value',2);
            if($first_end_value->count()>0){
                $arrData['e1']=$first_end_value->first()->name;
            }else{
                $arrData['e1']="";
            }
            $second_start_value = $attendanceSetup->where('reasons_value',3);
            if($second_start_value->count()>0){
                $arrData['s2']=$second_start_value->first()->name;
            }else{
                $arrData['s2']="";
            }
            $second_end_value = $attendanceSetup->where('reasons_value',4);
            if($second_end_value->count()>0){
                $arrData['e2']=$second_end_value->first()->name;
            }else{
                $arrData['e2']="";
            }
 
            $third_start_value = $attendanceSetup->where('reasons_value',5);
            if($third_start_value->count()>0){
                $arrData['s3']=$third_start_value->first()->name;
            }else{
                $arrData['s3']="";
            }
 
            $third_end_value = $attendanceSetup->where('reasons_value',6);
            if($third_end_value->count()>0){
                $arrData['e3']=$third_end_value->first()->name;
            }else{
                $arrData['e3']="";
            }
           
 
            $fourth_start_value = $attendanceSetup->where('reasons_value',7);
            if($fourth_start_value->count()>0){
                $arrData['s4']=$fourth_start_value->first()->name;
            }else{
                $arrData['s4']="";
            }
 
            $fourth_end_value = $attendanceSetup->where('reasons_value',8);
            if($fourth_end_value->count()>0){
                $arrData['e4']=$fourth_end_value->first()->name;

            }else{
                $arrData['e4']="";
            }
          
            
            $fifth_start_value = $attendanceSetup->where('reasons_value',9);
            if($fifth_start_value->count()>0){
                $arrData['s5']=$fifth_start_value->first()->name;
            }else{
                $arrData['s5']="";
            }
          
            $fifth_end_value = $attendanceSetup->where('reasons_value',10);
            if($fifth_end_value->count()>0){
                $arrData['e5']=$fifth_end_value->first()->name;
            }else{
                $arrData['e5']="";
            }
           
            $sixth_start_value = $attendanceSetup->where('reasons_value',11);
            if($sixth_start_value->count()>0){
                $arrData['s6']=$sixth_start_value->first()->name;
            }else{
                $arrData['s6']="";
            }
 
            $sixth_end_value = $attendanceSetup->where('reasons_value',12);
            if($sixth_end_value->count()>0){
                $arrData['e6']=$sixth_end_value->first()->name;
            }else{
                $arrData['e6']="";
            }
 
            $seventh_start_value = $attendanceSetup->where('reasons_value',13);
            if($seventh_start_value->count()>0){
                $arrData['s7']=$seventh_start_value->first()->name;
            }else{
                $arrData['s7']="";
            }
 
            $seventh_end_value = $attendanceSetup->where('reasons_value',14);
            if($seventh_end_value->count()>0){
                $arrData['e7']=$seventh_end_value->first()->name;
            }else{
                $arrData['e7']="";
            }
 
            $eight_start_value = $attendanceSetup->where('reasons_value',15);
            if($eight_start_value->count()>0){
                $arrData['s8']=$eight_start_value->first()->name;
            }else{
                $arrData['s8']="";
            }
 
            $eight_end_value = $attendanceSetup->where('reasons_value',16);
            if($eight_end_value->count()>0){
                $arrData['e8']=$eight_end_value->first()->name;
            }else{
                $arrData['e8']="";
            }
 
            $nineth_start_value = $attendanceSetup->where('reasons_value',17);
            if($nineth_start_value->count()>0){
                $arrData['s9']=$nineth_start_value->first()->name;
            }else{
                $arrData['s9']="";
            }
 
            $nineth_end_value = $attendanceSetup->where('reasons_value',18);
            if($nineth_end_value->count()>0){
                $arrData['e9']=$nineth_end_value->first()->name;
            }else{
                $arrData['e9']="";
            }
 
         }
           
      
        $attendances = $attendances

        ->map(fn ($attendances) => collect($attendances)
            ->put('attendance_setup',$arrData )
            ->toArray()
        ); 
       
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
           
           
             
           
            $timestamp = strtotime($attand['clock_in_time']);
            $dt = new \DateTime("now", new \DateTimeZone($company->timezone)); 
            $dt->setTimestamp($timestamp);
            
          
            
            
            $ant[$dt->format('Y-m-d')][] = $attand; // Set attendance Data indexed by similar date
         
        }
      
        
    
       
        

        // $endDate = Carbon::parse($request->endDate)->timezone($company->timezone);
        // $startDate = Carbon::parse($request->startDate)->timezone($company->timezone);
//dd($endDate);
        // Set All Data in a single Array
       
      
      
        for($date = $endDate; $date >= $startDate; $date->modify('-1 day')){
             
            
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
      
     
        $view = view('admin.attendance.user_attendance', ['company'=> $company,'dateWiseData' => $dateWiseData, 'global' => $this->global])->render();

        return Reply::dataOnly(['status' => 'success', 'data' => $view]);
    }

    public function attendanceByDate()
    {
        
        return view('admin.attendance.by_date', $this->data);
    }

    public function byDateData(Request $request)
    {
        $this->attendanceSettings = AttendanceSetting::first();
        $date = Carbon::createFromFormat($this->global->date_format, $request->date)->format('Y-m-d');
       // $attendances = Attendance::attendanceDate($date)->get();
        $attendances = Attendance::attendanceDateFilterByTimeZone($date)->get();
        $attendanceRecord= $attendances->each(function ($client) use($date) {
            $client->attendance = $client->attendance->filter(function ($schedule) use($date) {
                return $schedule->time_zone_date == $date;
            });
        });
    


        return DataTables::of($attendanceRecord)
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

    public function dateAttendanceCount(Request $request)
    {
        $date = Carbon::createFromFormat($this->global->date_format, $request->date)->format('Y-m-d');
        $checkHoliday = Holiday::checkHolidayByDate($date);
        $totalPresent = 0;
        $totalAbsent  = 0;
        $holiday  = 0;
        $holidayReason  = '';
        $users=User::allEmployees();
        $totalEmployees = count($users);
        $data=[];
        foreach($users as $u){
         $data[] = $u->id;
        }
        
        if (!$checkHoliday) {
            $totalPresent = Attendance::whereIn('user_id',$data)->get()->toArray();
            $presentEmp = array_filter($totalPresent, function ($u) use($date) {
              return   $u['time_zone_date']==$date;
            });
         
            $totalPresent = count($presentEmp);
            $totalAbsent = ($totalEmployees - $totalPresent);
        } else {
            $holiday = 1;
            $holidayReason = $checkHoliday->occassion;
        }

        return Reply::dataOnly(['status' => 'success', 'totalEmployees' => $totalEmployees, 'totalPresent' => $totalPresent, 'totalAbsent' => $totalAbsent, 'holiday' => $holiday, 'holidayReason' => $holidayReason]);
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
        return view('admin.attendance.attendance-detail', $this->data)->render();
    }

    // Bulk Attendance Store
    public function bulkAttendanceStore(StoreBulkAttendance $request): array
    {
        $this->attendanceSettings = AttendanceSetting::first();
        $groups = $request->group_id;
        $employeeData = $request->user_id;
        $groupEmployeeData = [];
        $employees = [];
        if($groups)
        {
            $groupEmployeeData = User::join('employee_details', 'users.id', '=', 'employee_details.user_id')
                ->whereIn('employee_details.department_id', $groups)
                ->where('users.status', 'active')
                ->select('users.id')->pluck('users.id')->toArray();
        }

        if($employeeData)
        {
            $employees = $request->user_id;
        }

        $date = Carbon::createFromFormat('d-m-Y', '01-' . $request->month . '-' . $request->year)->format('Y-m-d');
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
        }

        $startDate = Carbon::createFromFormat('d-m-Y', '01-' . $request->month . '-' . $request->year)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $period = CarbonPeriod::create($startDate, $endDate);
        $holidays = Holiday::getHolidayByDates($startDate->format('Y-m-d'), $endDate->format('Y-m-d'))->pluck('holiday_date')->toArray();

        if($groupEmployeeData)
        {
            $this->bulkAttendanceMark($groupEmployeeData, $period, $holidays, $request);
        }
        if($employees)
        {
            $this->bulkAttendanceMark($employees, $period, $holidays, $request);
        }

        return Reply::redirect(route('admin.attendances.summary'), __('messages.attendanceSaveSuccess'));
    }

    // Bulk attendance store action.
    public function bulkAttendanceMark($employees,$period, $holidays, $request )
    {
        $this->attendanceSettings = AttendanceSetting::first();
        $currentDate = Carbon::now();
        $insertData = [];
        foreach ($employees as $key => $userId) {
            foreach ($period as $date) {
                $attendance = Attendance::where('user_id', $userId)
                    ->where(DB::raw('DATE(`clock_in_time`)'), $date->format('Y-m-d'))
                    ->first();
                if (is_null($attendance) && $date->lt($currentDate)) { //attendance should not exist for the user for the same date
                    if (!in_array($date->format('Y-m-d'), $holidays)) { // date should not be a holiday
                        $clockIn = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date->format('Y-m-d') . ' ' . $request->clock_in_time, $this->global->timezone);
                        $clockIn->setTimezone('UTC');
                        $clockOut = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date->format('Y-m-d') . ' ' . $request->clock_out_time, $this->global->timezone);
                        $clockOut->setTimezone('UTC');
                        $insertData[] = [
                            'user_id' => $userId,
                            'clock_in_time' => $clockIn,
                            'clock_in_ip' => request()->ip(),
                            'clock_out_time' => $clockOut,
                            'clock_out_ip' => request()->ip(),
                            'working_from' => $request->working_from,
                            'late' => $request->late,
                            'half_day' => $request->half_day,
                            'company_id' => company()->id
                        ];
                    }
                }
            }
        }
        Attendance::insertOrIgnore($insertData);
    }

    // Attendance Detail Show
    public function bulkAttendance(Request $request)
    {
        // Getting Attendance Data By User And Date
        $this->employees = User::join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->where('roles.name', 'employee')
            ->groupBy('users.id')
            ->distinct('users.id')
            ->get();
        $now = Carbon::now();
        $this->year = $now->format('Y');
        $this->month = $now->format('m');

        $this->groups = Team::all();

        return view('admin.attendance.bulk-attendance', $this->data)->render();
    }

    public function export(Request $request, $startDate = null, $endDate = null, $employee = null)
    {
        
      
   

        
        // $startDate ='2022-12-18';
        // $endDate= '2022-12-18';
        // $userId=7;

        // $rows1 =     EmployeeDetails::with(['user' => function ($attend) use ($startDate,$endDate)  {
        //     return $attend->with('company')->with(['rota'=> function ($rota) use ($startDate,$endDate)  {  return $rota->whereBetween('date',[$startDate,$endDate]);},] ) 
            
        //     ;},])->with('department' )->where('id',$userId)->get();
            
            
                  
              
        $data=[];
        // dd( $rows1);
    //                 foreach( $rows1 as $Key=>$empldetail )
    
    //                 {
    // // dd( $empldetail->user->rota[0]);
    //                     $array['staff_id']=$empldetail->id;
                        
    //                     // $array['staff_name']=$empldetail->user->name;
    //                     // $array['staff_branch']=$empldetail->department->team_name;
    
    //                     // $array['R_startTime']=$empldetail->user->rota[0]->start_time;
    //                     // $array['R_endTime']=$empldetail->user->rota[0]->end_time;
    //                     // $array['R_date']=$empldetail->user->rota[0]->date;
    //                     // $array['R_timeCalculate']=$empldetail->user->rota[0]->time_calc;
    //                     // $array['reason']=DB::table('attandance_setup')->where('company_id',$empldetail->company_id)->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)->get()->toArray();
    //                     // $array['clock_in']=DB::table('attendances')->where('company_id',$empldetail->company_id)->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)->get()->pluck('clock_in_time')->toArray();
    //                     // $array['clock_out']=DB::table('attendances')->where('company_id',$empldetail->company_id)->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)->get()->pluck('clock_out_time')->toArray();
    
    
    // $data[]= $array;
    //                 }
    
        // //
      $startDate =date('Y-m-d',strtotime($request->startDate)) ;
        $endDate = date('Y-m-d',strtotime($request->endDate));

             $userId=$request->employee;
         
         
        return Excel::download(new LeaveReportExport($userId,$startDate,$endDate), 'invoices.xlsx');
    
       // return (new LeaveReportExport($request->startDate,$request->endDate,$request->employee))->download('invoices.xlsx');
    }

    public function summary()

    {
       
      
        $this->attendanceSettings = AttendanceSetting::first();
        $this->employees = User::allEmployees();
        //new code add
        $this->departments = Team::all();
        $now = Carbon::now();
        $this->year = $now->format('Y');
        $this->month = $now->format('m');

        return view('admin.attendance.summary', $this->data);
    }

    public function employeeFilter(Request $request )

    { 
    
        if($request->department_id==0){
            $employeedetail=  EmployeeDetails::get()->pluck('user_id')->toArray();
        }else{
            $employeedetail=  EmployeeDetails::where('department_id',$request->department_id)->get()->pluck('user_id')->toArray();
        }
      
       //dd(User::allEmployees()->whereIn('id',9));
    
      return  User::allEmployees()->whereIn('id',$employeedetail); 
    //   dd($employeedetail);
    }

    public function summaryData(Request $request)
    {
    
        $this->attendanceSettings = AttendanceSetting::first();
        $employees = User::with(
            ['attendance' => function ($query) use ($request) {
                $query->whereRaw('MONTH(attendances.clock_in_time) = ?', [$request->month])
                    ->whereRaw('YEAR(attendances.clock_in_time) = ?', [$request->year]);
            },'employeeDetail']
        )->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
              ->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at','employee_details.department_id', 'users.image')
            ->where('roles.name', '<>', 'client')->groupBy('users.id');

        if($request->department_id==0 && $request->userId == 0){
    
        $employees = $employees->get();
        
     }
     elseif($request->department_id!=0 && $request->userId==0){
      
        $employees = $employees->where('employee_details.department_id', $request->department_id)->get();
     }
     else if($request->department_id!=0 && $request->userId!=0){
       
        $employees = $employees->where('users.id', $request->userId)->get();
    }

        $this->holidays = Holiday::whereRaw('MONTH(holidays.date) = ?', [$request->month])->whereRaw('YEAR(holidays.date) = ?', [$request->year])->get();

        $final = [];
        $this->currentMonth = Carbon::parse('01-' . $request->month . '-' . $request->year);
        $this->daysInMonth = Carbon::parse('01-' . $request->month . '-' . $request->year)->daysInMonth;
        $month = Carbon::parse('01-' . $request->month . '-' . $request->year)->lastOfMonth();
        $now = Carbon::now()->timezone($this->global->timezone);
        $requestedDate = Carbon::parse(Carbon::parse('01-' . $request->month . '-' . $request->year))->endOfMonth();

        foreach ($employees as $employee) {
            $dataTillToday = array_fill(1, $this->daysInMonth, 'Absent');
            if($requestedDate->isPast()){
                $dataTillToday = array_fill(1, $this->daysInMonth, 'Absent');
            }
            else{
                $dataTillToday = array_fill(1, $now->copy()->format('d'), 'Absent');
            }
            
            if (($now->copy()->format('d') != $this->daysInMonth) && !$requestedDate->isPast()) {
                $dataFromTomorrow = array_fill($now->copy()->addDay()->format('d'), ($this->daysInMonth - $now->copy()->format('d')), '-');
            } else {
                if($this->daysInMonth < $now->copy()->format('d')){
                    $dataFromTomorrow = array_fill($month->copy()->addDay()->format('d'), (0), 'Absent');
                }
                else{
                    $dataFromTomorrow = array_fill($month->copy()->addDay()->format('d'), ($this->daysInMonth - $now->copy()->format('d')), 'Absent');
                }
            }

            $dataBeforeJoin = [];

            $final[$employee->id . '#' . $employee->name] = array_replace($dataTillToday, $dataFromTomorrow);

            foreach ($employee->attendance as $attendance) {
                if ($final[$employee->id . '#' . $employee->name][Carbon::parse($attendance->clock_in_time)->timezone($this->global->timezone)->day] != '-') {
                    $final[$employee->id . '#' . $employee->name][Carbon::parse($attendance->clock_in_time)->timezone($this->global->timezone)->day] = '<a href="javascript:;" class="view-attendance" data-attendance-id="' . $attendance->id . '"><i class="fa fa-check text-success"></i></a>';
                }
            }

            $image = '<img src="' . $employee->image_url . '" alt="user" class="img-circle" width="30" height="30"> ';
            $final[$employee->id . '#' . $employee->name][] = '<a class="userData" id="userID' . $employee->id . '" data-employee-id="' . $employee->id . '"  href="' . route('admin.employees.show', $employee->id) . '">' . $image . ' ' . ucwords($employee->name) . '</a>';

            if ($employee->employeeDetail->joining_date->greaterThan(Carbon::parse(Carbon::parse('01-' . $request->month . '-' . $request->year)))) {
                if($request->month == $employee->employeeDetail->joining_date->format('m') && $request->year == $employee->employeeDetail->joining_date->format('Y')){
                    $dataBeforeJoin = array_fill(1, $employee->employeeDetail->joining_date->subDay()->format('d'), '-');
                }
                if(($request->month < $employee->employeeDetail->joining_date->format('m') && $request->year == $employee->employeeDetail->joining_date->format('Y')) || $request->year < $employee->employeeDetail->joining_date->format('Y'))
                {
                    $dataBeforeJoin = array_fill(1, $this->daysInMonth, '-');
                }
            }

            if(Carbon::parse('01-' . $request->month . '-' . $request->year)->isFuture()){
                    $dataBeforeJoin = array_fill(1, $this->daysInMonth, '-');
            }

            $final[$employee->id . '#' . $employee->name] = array_replace($final[$employee->id . '#' . $employee->name], $dataBeforeJoin);

            foreach ($this->holidays as $holiday) {
                if ($final[$employee->id . '#' . $employee->name][$holiday->date->day] == 'Absent' || $final[$employee->id . '#' . $employee->name][$holiday->date->day] == '-') {
                    $final[$employee->id . '#' . $employee->name][$holiday->date->day] = 'Holiday';
                }
            }

        }


        $this->employeeAttendence = $final;

        $view = view('admin.attendance.summary_data', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'data' => $view]);
    }

    public function detail($id)
    {
        $this->attendanceSettings = AttendanceSetting::first();
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
        return view('admin.attendance.attendance_info', $this->data);
    }

    public function mark(Request $request, $userid, $day, $month, $year)
    {
        $this->attendanceSettings = AttendanceSetting::first();
                $maxAttandenceInDay=$this->maxAttandenceInDay;

        $this->date = Carbon::createFromFormat('d-m-Y', $day . '-' . $month . '-' . $year)->format('Y-m-d');
        $this->row = Attendance::attendanceByUserDate($userid, $this->date);
        $this->clock_in = 0;
        $this->total_clock_in = Attendance::where('user_id', $userid)
            ->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $this->date)
            ->whereNull('attendances.clock_out_time')->count();

        $this->userid = $userid;
        $this->type = 'add';
        return view('admin.attendance.attendance_mark', $this->data);
    }

    public function storeMark(StoreAttendance $request)
    {
        $this->attendanceSettings = AttendanceSetting::first();
        $date = Carbon::createFromFormat($this->global->date_format, $request->attendance_date)->format('Y-m-d');

        $clockIn = Carbon::createFromFormat('Y-m-d '.$this->global->time_format, $date.' '.$request->clock_in_time, $this->global->timezone);
        $clockIn->setTimezone('UTC');
        if ($request->clock_out_time != '') {
            $clockOut = Carbon::createFromFormat('Y-m-d '.$this->global->time_format, $date.' '.$request->clock_out_time, $this->global->timezone);
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
