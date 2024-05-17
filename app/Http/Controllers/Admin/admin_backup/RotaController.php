<?php

namespace App\Http\Controllers\Admin;

use App\ClockTimes;
use App\CustomClasses\Helper;
use App\CustomClasses\ReturnMessage;
use App\EmployeeDetails;
use App\Helper\Reply;
use App\Http\Controllers\Controller;
use App\Rota;
use App\Team;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use DateInterval;
use DateTime;
use DatePeriod;

class RotaController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Roster';
        $this->pageIcon = 'ti-layout-media-overlay';
//        $this->middleware(function ($request, $next) {
//           // abort_if(!in_array('rota', $this->user->modules), 403);
//            return $next($request);
//        });
    }
    public static function getDatesFromRange1($start, $end, $format = 'Y-m-d')
    {

        // Declare an empty array
        $array = array();

        // Variable that store the date interval
        // of period 1 day
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        // Use loop to store date into array
        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        // Return the array elements
        return $array;
    }

    public function index()
    {


        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+6 days'));

        $staffAll = User::allEmployees();

        $staffs = User::allEmployees1();
        $departments = Team::query()->get();

        $rota = Rota::query()->whereBetween('date', [$startDate, $endDate])->get();
        $days = [];

        //  dd($staffAll);

        $dates = self::getDatesFromRange1($startDate, $endDate);
        foreach ($dates as $value) {
            $days[] = [
                'date' => $value,
                'day' => date('D', strtotime($value)),
            ];
        }
        $superadmin=$this->superadmin;
        $pageTitle=$this->pageTitle;
        $pageIcon=$this->pageIcon;
        $global=$this->global;
        $invoiceSetting=$this->invoiceSetting;
        $pushSetting=$this->pushSetting;
        $companyName=$this->companyName;
        $adminTheme=$this->adminTheme;
        $languageSettings=$this->languageSettings;
        $pusherSettings=$this->pusherSettings;
        $rtl=$this->rtl;
        $unreadNotificationCount=$this->unreadNotificationCount;
        $unreadExpenseCount=$this->unreadExpenseCount;
        $unreadTicketCount=$this->unreadTicketCount;
        $unreadMessageCount=$this->unreadMessageCount;




        $user=$this->user;
        $modules=$this->modules;
        $worksuitePlugins=$this->worksuitePlugins;
        $isClient=$this->isClient;
        $stickyNotes=$this->stickyNotes;
        return view('admin.rota.index', compact('rota', 'staffs', 'staffAll', 'days','superadmin','pageTitle','pageIcon','global','invoiceSetting','pusherSettings','pushSetting','companyName','adminTheme','languageSettings','rtl','unreadNotificationCount','user','modules','worksuitePlugins','isClient','stickyNotes','unreadExpenseCount','unreadTicketCount','unreadMessageCount','departments'));
    }

    public function create()
    {
        $superadmin=$this->superadmin;
        $pageTitle=$this->pageTitle;
        $pageIcon=$this->pageIcon;
        $global=$this->global;
        $invoiceSetting=$this->invoiceSetting;
        $pushSetting=$this->pushSetting;
        $companyName=$this->companyName;
        $adminTheme=$this->adminTheme;
        $languageSettings=$this->languageSettings;
        $pusherSettings=$this->pusherSettings;
        $rtl=$this->rtl;
        $unreadNotificationCount=$this->unreadNotificationCount;
        $user=$this->user;
        $modules=$this->modules;
        $worksuitePlugins=$this->worksuitePlugins;
        $isClient=$this->isClient;
        $stickyNotes=$this->stickyNotes;
        $unreadExpenseCount=$this->unreadExpenseCount;
        $unreadTicketCount=$this->unreadTicketCount;
        $unreadMessageCount=$this->unreadMessageCount;


        $EmployeeData=EmployeeDetails::where('user_id',Auth::user()->id)->first();

        $staffs = User::allEmployees()->pluck('name', 'id');
        //dd($staffs);
        //->pluck(Carbon::parse('c_times')->format('g:i:s a'), 'c_times')
        $times = ClockTimes::query()->get();
        return view('admin.rota.create', compact('staffs', 'times','superadmin','pageTitle','pageIcon','global','invoiceSetting','pusherSettings','pushSetting','companyName','adminTheme','languageSettings','rtl','unreadNotificationCount','user','modules','worksuitePlugins','isClient','stickyNotes','unreadExpenseCount','unreadTicketCount','unreadMessageCount'));
    }

    public function edit($id)
    {
        $rota=Rota::find($id);
        $staffs = User::query()->where('status', 1)->pluck('name', 'id');
        $times = ClockTimes::query()->get();
        return view('admin.rota.edit', compact('staffs', 'times','rota'));
    }

    public function store(Request $request)
    {
        try {
            $fromDate=date('d',strtotime($request->date));
            $toDate=date('d',strtotime($request->to_date));
            $year=date('Y',strtotime($request->date));
            $year=date('m',strtotime($request->date));
            $dateDetails=\App\Helper\Helper::createDateRangeArray($request->date,$request->to_date);
            // dd($dateDetails);
            foreach ($dateDetails as $dateDetailss)
            {
                $dataTime= \App\Helper\Helper::timeDiff($request->start_time,$request->end_time);
                Rota::create([
                    'user_id' => $request->user_id,
                    'date' => $dateDetailss,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'time_calc' => \App\Helper\Helper::timeToSecond($dataTime),
                ]);
            }

            return \App\Helper\ReturnMessage::insertSuccess();
        } catch (\Exception $e) {
            return \App\Helper\ReturnMessage::somethingWrong();
        }
    }

    public function update($id,Request $request)
    {
        try {
            $dataTime= \App\Helper\Helper::timeDiff($request->start_time,$request->end_time);
            $rota=Rota::find($id);
            $rota->update([
                'user_id' => $request->user_id,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'time_calc' => \App\Helper\Helper::timeToSecond($dataTime),
            ]);
            return \App\Helper\ReturnMessage::updateSuccess();
        } catch (\Exception $e) {
            return \App\Helper\ReturnMessage::somethingWrong();
        }
    }

    public function delete($id)
    {
        if (Rota::query()->where('id',$id)->delete()){
            return \App\Helper\ReturnMessage::deleteSuccess();

        }
        return  \App\Helper\ReturnMessage::somethingWrong();
    }

    public function rotaSearch(Request $request)
    {
        $superadmin=$this->superadmin;
        $pageTitle=$this->pageTitle;
        $pageIcon=$this->pageIcon;
        $global=$this->global;
        $invoiceSetting=$this->invoiceSetting;
        $pushSetting=$this->pushSetting;
        $companyName=$this->companyName;
        $adminTheme=$this->adminTheme;
        $languageSettings=$this->languageSettings;
        $pusherSettings=$this->pusherSettings;
        $rtl=$this->rtl;
        $unreadNotificationCount=$this->unreadNotificationCount;
        $user=$this->user;
        $modules=$this->modules;
        $worksuitePlugins=$this->worksuitePlugins;
        $isClient=$this->isClient;
        $stickyNotes=$this->stickyNotes;
        $unreadExpenseCount=$this->unreadExpenseCount;
        $unreadTicketCount=$this->unreadTicketCount;
        $unreadMessageCount=$this->unreadMessageCount;

        $startDate = $request->from;
        $endDate = $request->to;
        $department = $request->department;
        $departments = Team::query()->get();

//dd($endDate);
        if ($request->department!=0) {
            $staffAll = User::query()->where('status', 1)->get();
            $staffs = User::allEmployees5($request->department);
        }
        else
      {
          $staffAll = User::query()->where('status', 1)->get();
          $staffs = User::allEmployees1();
          //dd($staffs);

      }
        $days = [];
        $dates = self::getDatesFromRange1($startDate, $endDate);
        foreach ($dates as $value) {
            $days[] = [
                'date' => $value,
                'day' => date('D', strtotime($value)),
            ];
        }
        $times = ClockTimes::query()->get();
        $c_type=$request->check_type;
        $startDate1=$startDate;
        $endDate1=$endDate;
        if ($request->check_type==2){
            $rota = Rota::query()->whereBetween('date', [$startDate, $endDate])->get();
           // dd($rota);
            return view('admin.rota.all_staff', compact('rota', 'staffs', 'staffAll', 'days','c_type','startDate1','endDate1','superadmin','pageTitle','pageIcon','global','invoiceSetting','pusherSettings','pushSetting','companyName','adminTheme','languageSettings','rtl','unreadNotificationCount','user','modules','worksuitePlugins','isClient','stickyNotes','unreadExpenseCount','unreadTicketCount','unreadMessageCount','departments'));
        }

        $rota = Rota::query()->where('user_id',$request->staff)->whereBetween('date', [$startDate, $endDate])->get();
        $staff_user=$request->staff;
        return view('admin.rota.staff', compact('rota', 'staffs', 'staffAll', 'days','c_type','times','staff_user','startDate1','endDate1','superadmin','pageTitle','pageIcon','global','invoiceSetting','pusherSettings','pushSetting','companyName','adminTheme','languageSettings','rtl','unreadNotificationCount','user','modules','worksuitePlugins','isClient','stickyNotes','unreadExpenseCount','unreadTicketCount','unreadMessageCount','departments'));

    }

    public function myRota(Request $request)
    {
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+6 days'));

        $startDate = $request->from??$startDate;
        $endDate = $request->to??$endDate;

        $staffAll = User::query()->where('status', 1)->get();
        $staffs = User::query()->where('status', 1)->paginate(20);

        $days = [];
        $dates = self::getDatesFromRange1($startDate, $endDate);
        foreach ($dates as $value) {
            $days[] = [
                'date' => $value,
                'day' => date('D', strtotime($value)),
            ];
        }
        $times = ClockTimes::query()->get();
        $rota = Rota::query()->where('user_id',Auth::id())->whereBetween('date', [$startDate, $endDate])->get();
        return view('admin.rota.my_rota', compact('rota', 'days','times','startDate','endDate'));

    }

}