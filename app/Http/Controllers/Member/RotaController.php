<?php

namespace App\Http\Controllers\Member;

use App\ClockTimes;
use App\CustomClasses\Helper;
use App\CustomClasses\ReturnMessage;
use App\EmployeeDetails;
use App\Helper\Reply;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Member\MemberBaseController;
use App\Rota;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use DateInterval;
use DateTime;
use DatePeriod;

class RotaController extends MemberBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Roster';
        $this->pageIcon = 'ti-layout-media-overlay';
        $this->middleware(function ($request, $next) {
            //abort_if(!in_array('rota', $this->user->modules), 403);
            return $next($request);
        });
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
        $EmployeeData=EmployeeDetails::where('user_id',Auth::user()->id)->first();


        $staffAll = User::allEmployees3($EmployeeData->department_id);

        $staffs = User::allEmployees2($EmployeeData->department_id)->paginate(20);

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
        @$adminTheme=$this->adminTheme;
        @$languageSettings=$this->languageSettings;
        @$pusherSettings=$this->pusherSettings;
        @$rtl=$this->rtl;
        @$unreadNotificationCount=$this->unreadNotificationCount;
        @$unreadExpenseCount=$this->unreadExpenseCount;
        @$unreadTicketCount=$this->unreadTicketCount;
        @$unreadMessageCount=$this->unreadMessageCount;
        @$unreadProjectCount =$this->unreadProjectCount;
        @$faqs =$this->faqs;
        @$timer =$this->timer;




        $user=$this->user;
        $userRole=$this->userRole;
        $modules=$this->modules;
        $worksuitePlugins=$this->worksuitePlugins;
        @$isClient=$this->isClient;
        $stickyNotes=$this->stickyNotes;
        return view('member.rota.index', compact('rota', 'staffs', 'staffAll', 'days','superadmin','pageTitle','pageIcon','global','invoiceSetting','pusherSettings','pushSetting','companyName','adminTheme','languageSettings','rtl','unreadNotificationCount','user','modules','worksuitePlugins','isClient','stickyNotes','unreadExpenseCount','unreadTicketCount','unreadMessageCount','userRole','unreadProjectCount','faqs','timer'));
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
       @ $adminTheme=$this->adminTheme;
        @$languageSettings=$this->languageSettings;
        @$pusherSettings=$this->pusherSettings;
        @$rtl=$this->rtl;
        @$unreadNotificationCount=$this->unreadNotificationCount;
        $user=$this->user;
        $modules=$this->modules;
        $worksuitePlugins=$this->worksuitePlugins;
       @ $isClient=$this->isClient;
        $stickyNotes=$this->stickyNotes;
       @ $unreadExpenseCount=$this->unreadExpenseCount;
        @$unreadTicketCount=$this->unreadTicketCount;
       @ $unreadMessageCount=$this->unreadMessageCount;
       @ $userRole=$this->userRole;
       @ $unreadProjectCount=$this->unreadProjectCount;
       @ $faqs=$this->faqs;
       @ $timer=$this->timer;

        //'userRole','unreadProjectCount','faqs','timer'

        $EmployeeData=EmployeeDetails::where('user_id',Auth::user()->id)->first();

        $staffs = EmployeeDetails::with('user')->where('company_id',$EmployeeData->company_id)->where('department_id',$EmployeeData->department_id)->get()->pluck('user.name', 'user_id');
        $times = ClockTimes::query()->get();
        return view('member.rota.create', compact('staffs', 'times','superadmin','pageTitle','pageIcon','global','invoiceSetting','pusherSettings','pushSetting','companyName','adminTheme','languageSettings','rtl','unreadNotificationCount','user','modules','worksuitePlugins','isClient','stickyNotes','unreadExpenseCount','unreadTicketCount','unreadMessageCount','userRole','unreadProjectCount','faqs','timer'));
    }

    public function edit($id)
    {
        $rota=Rota::find($id);
        $EmployeeData=EmployeeDetails::where('user_id',Auth::user()->id)->first();

        $staffs = EmployeeDetails::with('user')->where('company_id',$EmployeeData->company_id)->where('department_id',$EmployeeData->department_id)->get()->pluck('user.name', 'user_id');
        $times = ClockTimes::query()->get();
        return view('member.rota.edit', compact('staffs', 'times','rota'));
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
        @$adminTheme=$this->adminTheme;
        @$languageSettings=$this->languageSettings;
        @$pusherSettings=$this->pusherSettings;
       @ $rtl=$this->rtl;
       @ $unreadNotificationCount=$this->unreadNotificationCount;
        @$user=$this->user;
       @ $modules=$this->modules;
       @ $worksuitePlugins=$this->worksuitePlugins;
       @ $isClient=$this->isClient;
       @ $stickyNotes=$this->stickyNotes;
       @ $unreadExpenseCount=$this->unreadExpenseCount;
       @ $unreadTicketCount=$this->unreadTicketCount;
        @$unreadMessageCount=$this->unreadMessageCount;
        @$userRole=$this->userRole;
        @$unreadProjectCount=$this->unreadProjectCount;
        @$faqs=$this->faqs;
        @$timer=$this->timer;

        $startDate = $request->from;
        $endDate = $request->to;
 $EmployeeData=EmployeeDetails::where('user_id',Auth::user()->id)->first();


        $staffAll = User::allEmployees3($EmployeeData->department_id);

        $staffs = User::allEmployees2($EmployeeData->department_id)->paginate(20);
        // $staffAll = User::query()->where('status', 1)->get();
        // $staffs = User::query()->where('status', 1)->paginate(20);

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
            return view('member.rota.all_staff', compact('rota', 'staffs', 'staffAll', 'days','c_type','startDate1','endDate1','superadmin','pageTitle','pageIcon','global','invoiceSetting','pusherSettings','pushSetting','companyName','adminTheme','languageSettings','rtl','unreadNotificationCount','user','modules','worksuitePlugins','isClient','stickyNotes','unreadExpenseCount','unreadTicketCount','unreadMessageCount','userRole','unreadProjectCount','faqs','timer'));
        }
        $rota = Rota::query()->where('user_id',$request->staff)->whereBetween('date', [$startDate, $endDate])->get();
        $staff_user=$request->staff;
        $staffsrequest = User::query()->where('status', 1)->where('id',$staff_user)->first();

        return view('member.rota.staff', compact('rota', 'staffs', 'staffsrequest','staffAll', 'days','c_type','times','staff_user','startDate1','endDate1','superadmin','pageTitle','pageIcon','global','invoiceSetting','pusherSettings','pushSetting','companyName','adminTheme','languageSettings','rtl','unreadNotificationCount','user','modules','worksuitePlugins','isClient','stickyNotes','unreadExpenseCount','unreadTicketCount','unreadMessageCount','userRole','unreadProjectCount','faqs','timer'));

    }

    public function myRota(Request $request)
    {

        $superadmin=$this->superadmin;
        $pageTitle=$this->pageTitle;
        $pageIcon=$this->pageIcon;
        $global=$this->global;
        $invoiceSetting=$this->invoiceSetting;
        $pushSetting=$this->pushSetting;
        $companyName=$this->companyName;
        @$adminTheme=$this->adminTheme;
        @$languageSettings=$this->languageSettings;
        @$pusherSettings=$this->pusherSettings;
        @ $rtl=$this->rtl;
        @ $unreadNotificationCount=$this->unreadNotificationCount;
        @$user=$this->user;
        @ $modules=$this->modules;
        @ $worksuitePlugins=$this->worksuitePlugins;
        @ $isClient=$this->isClient;
        @ $stickyNotes=$this->stickyNotes;
        @ $unreadExpenseCount=$this->unreadExpenseCount;
        @ $unreadTicketCount=$this->unreadTicketCount;
        @$unreadMessageCount=$this->unreadMessageCount;
        @$userRole=$this->userRole;
        @$unreadProjectCount=$this->unreadProjectCount;
        @$faqs=$this->faqs;
        @$timer=$this->timer;
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
        return view('member.rota.my_rota', compact('rota', 'days','times','startDate','endDate','superadmin','pageTitle','pageIcon','global','invoiceSetting','pusherSettings','pushSetting','companyName','adminTheme','languageSettings','rtl','unreadNotificationCount','user','modules','worksuitePlugins','isClient','stickyNotes','unreadExpenseCount','unreadTicketCount','unreadMessageCount','userRole','unreadProjectCount','faqs','timer'));

    }

}