<?php

namespace App\Exports;
// namespace Yajra\DataTables\Exports;
use App\Company;
use App\EmployeeDetails;
use App\Leave;
use App\Rota;
use App\Team;
use App\User;
use App\Attendance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\RestAPI\Entities\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use DateTime;

// class LeaveReportExport implements FromCollection,WithHeadings,ShouldAutoSize
class LeaveReportExport implements FromView
{
    use Exportable;

    private $data;
    private $startDate;
    private $endDate;

    public function __construct($data,$startDate,$endDate)
    {
        
   
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    
    public function view(): View
    {
      $companyId= User::find($this->data)->first()->company_id;
      $attendanceSetup= \App\AttedanceSetup::where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->get();
      $userId= $this->data;
      $startDate = $this->startDate;
      $endDate=  $this->endDate;
      $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
       
      $rows=Attendance::with(['user.rota'=>function($q)use($startDate,$endDate){
         return $q->where('date','>=',$startDate)->where('date','<=',$endDate);
      }]
      )
      ->whereDate('clock_in_time','>=',$startDate)
      ->whereDate('clock_in_time','<=',$endDate)
      ->where('user_id',$userId)->orderBy('id', 'DESC')->get();
     
      
    //   $rows= Rota::with(['user'=>function($att) use ($startDate,$endDate) {
    //             return $att->with(['attendance'=>function($q) use ($startDate,$endDate){
    //                 return $q->select('*',\DB::raw('date(clock_in_time) as clock_in_date'))->whereDate('clock_in_time','>=',$startDate)->whereDate('clock_in_time','<=',$endDate);
    //             }]);
    //   }])->where('date','>=',$startDate)->where('date','<=',$endDate)->where('user_id',$userId)->get();
      
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

         $rows = $rows

         ->map(fn ($rows) => collect($rows)
             ->put('attendance_setup',$arrData )
             ->toArray()
         ); 
        


        $calPaidTimeReasons=DB::table('attendances')
        ->leftJoin('attendance_reasons_paid_unpaid_status as att_stat', 'attendances.id', '=', 'att_stat.attendance_id')
        ->where('company_id',$companyId)
        ->whereDate('clock_in_time','>=',$startDate)->whereDate('clock_in_time','<=',$endDate)->get();
       
        
        return view('export.attendance',['reasons'=> $attendanceSetup,'rows'=>$rows,'calPaidTimeReasons'=>$calPaidTimeReasons]);
    }

    // public function headings(): array
    // {
    //    // return ['Staff Id', 'Staff Name', 'Branch', 'From Roster Date and Time', 'To Roster Date and Time','Time1','Time2','Reason','Paid Breaks','Total Working Hours'];
       
    //    return ['Roster Date','Emplyee Name','Roster Start Time','Roster End Time','Clock In','First Break Start','First Break End'];
    // }

    /**
     * @return \Illuminate\Support\Collection
     */
    // public function collection()
    // // public function view(): View
    // {
    //     // $startDate
    //     // // $startDate = $this->startDate;
    //     // //$data = $this->data;
    //     $userId= $this->data;
    //     $startDate = $this->startDate;
    //     $endDate=  $this->endDate;
    //     $rows= $rota=Rota::with(['user'=>function($att) use ($startDate,$endDate) {
    //           return $att->with(['attendance'=>function($q) use ($startDate,$endDate){
    //               return $q->select('*',\DB::raw('date(clock_in_time) as clock_in_date'))->whereDate('clock_in_time','>=',$startDate)->whereDate('clock_in_time','<=',$endDate);
    //           }]);
    //     }])->where('date','>=',$startDate)->where('date','<=',$endDate)->where('user_id',$userId)->get();
        
     
    //    try{
           
    //     foreach( $rows as $Key=>$r )
    //      {
    //           $array['date']=$r->date;
    //          if($r->user->count()>0){
    //            $array['emp_name']=$r->user->name;   
    //          }else{
    //               $array['emp_name']='';  
    //          }
             
    //           $array['start_time']=$r->start_time;
    //           $array['end_time']=$r->end_time;
    //           if($r->user->attendance->count()>0){
                   
    //                 $att=$r->user->attendance;
    //                 $isAttendanceDate = $att->where('clock_in_date',$r->date)->first();
    //                 if($isAttendanceDate){
    //                     $array['clock_in']=date('H:i', strtotime($isAttendanceDate->clock_in_time));
    //                     $array['first_break_start']=date('H:i', strtotime($isAttendanceDate->first_break_start));
    //                     $array['first_break_end']=date('H:i', strtotime($isAttendanceDate->first_break_end));
    //                 }
            
                     
    //           }else{
    //             $array['clock_in']='';   
    //             $array['first_break_start']='';
    //             $array['first_break_end']='';
    //           }
        
             
    //           $data[]= $array;
    //      }
    //       return  new Collection($data) ;
                  

    //     } catch (exception $e) {

    // }
         

    //     // $rows1 =     EmployeeDetails::with(['user' => function ($attend) use ($startDate,$endDate,$userId)  {
    //     //     return $attend->with('company')->with(['rota'=> function ($rota) use ($startDate,$endDate,$userId)  {  return $rota->whereBetween('date',[$startDate,$endDate]);},] ) 
            
    //     //     ;},])->with('department')->where('user_id',$userId)->get();
       
         
            
    //     //     try {
                  

    //     // $data=[];
      
    //     //             foreach( $rows1 as $Key=>$empldetail )
    
    //     //             {
                        
    //     //                 $array['staff_id']=$empldetail->id;
    //     //                 $empldetail->user !=null?
    //     //                 $array['staff_name']=$empldetail->user->name:
    //     //                 $array['staff_name']='null';
    //     //                 $empldetail->department !=null?   $array['staff_branch']=$empldetail->department->team_name:
    //     //                 $array['staff_branch']='null';
                        
    //     //                 if($empldetail->user->rota!='[]'){
                           
    //     //                 $array['R_startTime']=$empldetail->user->rota[0]->date.''.$empldetail->user->rota[0]->start_time;
    //     //                 $array['R_endTime']=$empldetail->user->rota[0]->date.''.$empldetail->user->rota[0]->end_time;
    //     //                 }else{
                          
    //     //                     $array['R_startTime']=null;
    //     //                     $array['R_endTime']=null;
    //     //                 }
                        
    //     //                 // $array['R_date']=$empldetail->user->rota[0]->date;
    //     //                 $array['clock_in']=DB::table('attendances')->where('company_id',$empldetail->company_id)->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)->get()->pluck('clock_in_time')->toArray();
    //     //                 $array['clock_out']=DB::table('attendances')->where('company_id',$empldetail->company_id)->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)->get()->pluck('clock_out_time')->toArray();
    //     //                 $array['reason']=DB::table('attandance_setup')->where('company_id',$empldetail->company_id)->get()->pluck('name')->toArray();
                       
    //     //                $calPaidTimeReasons=DB::table('attendances')
    //     //                                        ->leftJoin('attendance_reasons_paid_unpaid_status as att_stat', 'attendances.id', '=', 'att_stat.attendance_id')
    //     //                                        ->where('company_id',$empldetail->company_id)
    //     //                                        ->whereDate('clock_in_time','>=',$startDate)->whereDate('clock_in_time','<=',$endDate)->get();
                                                 
                                               
               
                                         
    //     //                 $time1=[];
    //     //                 $time2=[];
                      
    //     //              if($calPaidTimeReasons->count()>0){
    //     //                 foreach($calPaidTimeReasons as $time){
    //     //                     if(($time->first_break_start!=null && $time->first_break_start!=null) && ($time->status_1===null || $time->status_1===1 )){
    //     //                         $start_time=date('H:i',strtotime($time->first_break_start));
    //     //                         $end_time=date('H:i',strtotime($time->first_break_end));
    //     //                         $start = strtotime($start_time);
    //     //                         $end = strtotime($end_time);
    //     //                         $cal_time = $end - $start;
    //     //                         array_push($time1, date("H:i", $cal_time));
    //     //                     }
    //     //                     if(($time->second_break_start!=null && $time->second_break_end!=null) && ($time->status_2===null || $time->status_2===1 )){
    //     //                         $start_time=date('H:i',strtotime($time->second_break_start));
    //     //                         $end_time=date('H:i',strtotime($time->second_break_end));
    //     //                         $start = strtotime($start_time);
    //     //                         $end = strtotime($end_time);
    //     //                         $cal_time = $end - $start;
    //     //                         array_push($time1, date("H:i", $cal_time));
    //     //                     }
    //     //                     if(($time->third_break_start!=null && $time->third_break_end!=null) && ($time->status_3===null || $time->status_3===1 )){
    //     //                         $start_time=date('H:i',strtotime($time->third_break_start));
    //     //                         $end_time=date('H:i',strtotime($time->third_break_end));
    //     //                         $start = strtotime($start_time);
    //     //                         $end = strtotime($end_time);
    //     //                         $cal_time = $end - $start;
    //     //                         array_push($time1, date("H:i", $cal_time));
    //     //                     }
    //     //                     if(($time->four_break_start!=null && $time->four_break_end!=null) && ($time->status_4===null || $time->status_4===1 ) ){
    //     //                         $start_time=date('H:i',strtotime($time->four_break_start));
    //     //                         $end_time=date('H:i',strtotime($time->four_break_end));
    //     //                         $start = strtotime($start_time);
    //     //                         $end = strtotime($end_time);
    //     //                         $cal_time = $end - $start;
    //     //                         array_push($time1, date("H:i", $cal_time));
    //     //                     }
    //     //                     if(($time->five_break_start!=null && $time->five_break_end!=null) && ($time->status_5===null || $time->status_5===1 )){
    //     //                         $start_time=date('H:i',strtotime($time->five_break_start));
    //     //                         $end_time=date('H:i',strtotime($time->five_break_end));
    //     //                         $start = strtotime($start_time);
    //     //                         $end = strtotime($end_time);
    //     //                         $cal_time = $end - $start;
    //     //                         array_push($time1, date("H:i", $cal_time));
    //     //                     }
    //     //                     if(($time->six_break_start!=null && $time->six_break_end!=null) && ($time->status_6===null || $time->status_6===1 )){
    //     //                         $start_time=date('H:i',strtotime($time->six_break_start));
    //     //                         $end_time=date('H:i',strtotime($time->six_break_end));
    //     //                         $start = strtotime($start_time);
    //     //                         $end = strtotime($end_time);
    //     //                         $cal_time = $end - $start;
    //     //                         array_push($time1, date("H:i", $cal_time));
    //     //                     }
    //     //                     if(($time->seven_break_start!=null && $time->seven_break_end!=null) && ($time->status_7===null || $time->status_7===1)){
    //     //                         $start_time=date('H:i',strtotime($time->seven_break_start));
    //     //                         $end_time=date('H:i',strtotime($time->seven_break_end));
    //     //                         $start = strtotime($start_time);
    //     //                         $end = strtotime($end_time);
    //     //                         $cal_time = $end - $start;
    //     //                         array_push($time1, date("H:i", $cal_time));
    //     //                     }
    //     //                     if(($time->eight_break_start!=null && $time->eight_break_end!=null) && ($time->status_8===null || $time->status_8===1 )){
    //     //                         $start_time=date('H:i',strtotime($time->eight_break_start));
    //     //                         $end_time=date('H:i',strtotime($time->eight_break_end));
    //     //                         $start = strtotime($start_time);
    //     //                         $end = strtotime($end_time);
    //     //                         $cal_time = $end - $start;
    //     //                         array_push($time1, date("H:i", $cal_time));
    //     //                     }
    //     //                     if(($time->nine_break_start!=null && $time->nine_break_end!=null) && ($time->status_9===null || $time->status_9===1 )  ){
                              
    //     //                         $start_time=date('H:i',strtotime($time->nine_break_start));
                              
    //     //                         $end_time=date('H:i',strtotime($time->nine_break_end));
                               
    //     //                         $start = strtotime($start_time);
    //     //                         $end = strtotime($end_time);
                              
    //     //                         $cal_time = $end - $start;
                            
    //     //                         array_push($time1, date("H:i", $cal_time));
    //     //                     }
    //     //                      if(($time->clock_in_time!=null && $time->clock_out_time!=null)){
    //     //                         $start_time=date('H:i',strtotime($time->clock_in_time));
    //     //                         $end_time=date('H:i',strtotime($time->clock_out_time));
    //     //                         $start = strtotime($start_time);
    //     //                         $end = strtotime($end_time);
    //     //                         $cal_time = $end - $start;
    //     //                         array_push($time2, date("H:i", $cal_time));
    //     //                     }
                            
                           
    //     //                 }
    //     //             } 

    //     //                 if($empldetail->user->rota!='[]'){
    //     //                 //   $array['R_timeCalculate']=$this->AddTimeToStr($time1);
    //     //                 // $empldetail->user->rota[0]->time_calc/3600;
                       
    //     //                 $array['R_timeCalculate']= $this->AddTimeToStr($time1);
    //     //                 $array['total_hrs']= $this->AddTimeToStr($time2);
    //     //                 }else{
                          
    //     //                   $array['R_timeCalculate']=null;
    //     //                   $array['total_hrs']=null;
    //     //                 }
                      
                       
    //     //                 $data[]= $array;
    //     //                 // dd($data);
    //     //             }
                   
    //     //             return  new Collection($data) ;
                  

    //     //         } catch (exception $e) {

    //     //         }
                   
                 
         
    // }

    function AddTimeToStr($aElapsedTimes) {
        $totalHours = 0;
        $totalMinutes = 0;
      
        foreach($aElapsedTimes as $time) {
          $timeParts = explode(":", $time);
          $h = $timeParts[0];
          $m = $timeParts[1];
          $totalHours += $h;
          $totalMinutes += $m;
        }
      
        $additionalHours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        $hours = $totalHours + $additionalHours;
      
        $strMinutes = strval($minutes);
        if ($minutes < 10) {
            $strMinutes = "0" . $minutes;
        }
      
        $strHours = strval($hours);
        if ($hours < 10) {
            $strHours = "0" . $hours;
        }
      
        return($strHours . ":" . $strMinutes);
      }

    // public function map($user): array
    // {
        
    //     $rows = [];
        

    //     $rows[]=$user['staff_id'];
    //     $rows[]=$user['staff_name'];
    //     $rows[]=$user['staff_branch'];
        
        
    //      foreach($user['clock_in'] as $key=>$relation)
    //     { 
         
            
    //            $rows[]= $relation;
                   
                 
                  
    //     }
    //     //  $rows[]= $row; 
    //      foreach($user['clock_out'] as $key=>$relation1)
    //      { 
            
             
    //             $row1[]= 
    //           $relation1;
                    
                  
                   
    //      }
    //       $rows[]= $row1; 
    //       foreach($user['reason'] as $key=>$relation2)
    //       { 
             
              
    //              $row2[]= $relation2;
                     
                   
                    
    //       }
    //        $rows[]= $row2; 
       
    //        $data = [['Name', 'Email'], [null, 'johndoe@example.com'], ['Jane Smith', 'janesmith@example.com']];
    //      //dd($rows);
    //    return $rows;
    //     // return [
    //     //     $member->clock_in,
    //     //     $member->clock_out,
            
    //     // ];
    // }


}
