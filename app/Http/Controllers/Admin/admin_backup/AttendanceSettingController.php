<?php

namespace App\Http\Controllers\Admin;

use App\AttedanceSetup;
use App\AttendanceSetting;
use App\Helper\Reply;
use App\Http\Requests\AttendanceSetting\UpdateAttendanceSetting;
use Carbon\Carbon;
use GPBMetadata\Google\Api\Auth;
use Illuminate\Http\Request;

class AttendanceSettingController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.attendanceSettings';
        $this->pageIcon = 'icon-settings';
        $this->middleware(function ($request, $next) {
            if(!in_array('attendance', $this->user->modules)){
                abort(403);
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->ipAddresses = [];
        $this->attendanceSetting = AttendanceSetting::where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->first();
        $this->attendanceSetup = AttedanceSetup::where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->orderBy('order_by','asc')->get();
        $this->openDays = json_decode($this->attendanceSetting->office_open_days);
        if (json_decode($this->attendanceSetting->ip_address)) {
            $this->ipAddresses = json_decode($this->attendanceSetting->ip_address, true);
        }
        return view('admin.attendance-settings.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAttendanceSetting $request, $id)
    {

        $attendanceSetupCheck=AttedanceSetup::query()->where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->count();
if($attendanceSetupCheck!=0)
{
    AttedanceSetup::query()->where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->truncate();

}
//dd($request->type);

        for($i=0;$i<sizeof($request->check_setup);$i++)
        {
            if($i==0)
            {
                $j=0;
            }
            elseif($i==2)
            {
                $j=1;
            }
            elseif($i==4)
            {
                $j=2;
            }
            elseif($i==6)
            {
                $j=3;
            }
            elseif($i==8)
            {
                $j=4;
            }
            elseif($i==10)
            {
                $j=5;
            }
            $wordsStart = array("start","ddd");
            $wordsEnd = array("end","ddd");

            $newalt=$request->check_setup[$i];

            $url_string = explode(" ", strtolower($newalt));
            //dd(count(array_diff($wordsStart, $url_string)));
            $dataLast=AttedanceSetup::where('company_id',Company()->id)->count();
            if(count(array_diff($wordsStart, $url_string)) ==1)
            {
                //dd($url_string);

                $num = (int) $url_string[1];
                   //dd($dataLast);

                if($dataLast!=0)
                {
                    //dd($dataLast);
                    $counter=$dataLast+1;

                }
                else
                {
                    $counter=$num;
                }
            }
            elseif(count(array_diff($wordsEnd, $url_string)) == 1)
            {
                $num = (int) $url_string[1];
//                $num=$num+1;
                $dataChecker=AttedanceSetup::where('company_id',Company()->id)->where('order_by',$num)->count();
                if($dataChecker==1)
                {
                    $counter=$dataLast+1;
                }

               //dd($url_string);
            }
$attendanceSetup=new AttedanceSetup();
            $attendanceSetup->name=$request->check_setup[$i];
            $attendanceSetup->status=@$request->type[$j];
            $attendanceSetup->order_by=$num;
            $attendanceSetup->counter=$counter;
            $attendanceSetup->company_id=\Illuminate\Support\Facades\Auth::user()->company_id;
            $attendanceSetup->save();
        }
        $setting = AttendanceSetting::where('company_id', company()->id)->first();
        $setting->office_start_time = Carbon::createFromFormat($this->global->time_format, $request->office_start_time);
        $setting->office_end_time = Carbon::createFromFormat($this->global->time_format, $request->office_end_time);
        $setting->halfday_mark_time = Carbon::createFromFormat($this->global->time_format, $request->halfday_mark_time);
        $setting->late_mark_duration = $request->late_mark_duration;
        $setting->clockin_in_day = $request->clockin_in_day;
        ($request->employee_clock_in_out == 'yes') ? $setting->employee_clock_in_out = 'yes' : $setting->employee_clock_in_out = 'no';
//        ($request->first_break == 'yes') ? $setting->first_break = 'yes' : $setting->first_break = 'no';
//        ($request->lunch_break == 'yes') ? $setting->lunch_break = 'yes' : $setting->lunch_break = 'no';
        $setting->office_open_days = json_encode($request->office_open_days);
        ($request->radius_check == 'yes') ? $setting->radius_check = 'yes' : $setting->radius_check = 'no';
        ($request->ip_check == 'yes') ? $setting->ip_check = 'yes' : $setting->ip_check = 'no';
        $setting->radius = $request->radius;
        $setting->alert_after = $request->alert_after;
        $setting->alert_after_status = ($request->alert_after_status == 'on') ? 1 : 0;
        $ip_address = [];
        if ($request->ip) {
            foreach ($request->ip as $key => $value) {
                if (!empty($value)) {
                    $ip_address[] = $value;
                }
            }

        }
        $setting->ip_address = $ip_address ? json_encode($ip_address) : null;
        $setting->save();

        return Reply::success(__('messages.settingsUpdated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
