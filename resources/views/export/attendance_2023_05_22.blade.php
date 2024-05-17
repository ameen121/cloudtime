<table>
   
@php 

$company=\App\Company::where('id',\Illuminate\Support\Facades\Auth::user()->company_id)->first();
$tz = $company->timezone;
                           

@endphp

    <thead>
    <tr>
        <th>Roster Date</th>
        <th>Roster Start Time</th>
        <th>Roster End Time</th>
        <th>Employee Name</th>
        <th>Clock In</th>
        <th>Clock Out</th>
        @if($reasons)
        @foreach($reasons as $res)
        <th>{{$res->name}}</th>
        @endforeach
        @endif
        <th>UnPaid Hours</th>
        <th>Paid Hours</th>
        <th>Total Hours</th>
      
    </tr>
    </thead>
    <tbody>
     
     $dataRecords=[];
    
      @foreach($rows as $r)
      @php
        $clock_in=""; 
        $clock_out="";
        $b_one_start="";
        $b_one_end="";
        $b_two_start="";
        $b_two_end="";
        $b_three_start="";
        $b_three_end="";
        $b_four_start="";
        $b_four_end="";
        $b_five_start="";
        $b_five_end="";
        $b_six_start="";
        $b_six_end="";
        $b_seven_start="";
        $b_seven_end="";
        $b_eight_start="";
        $b_eight_end="";
        $b_nine_start="";
        $b_nine_end="";
        $attendanceId="";
        $attendanceData="";

        $date=$r['date'];
        if(count($r['user'])>0){
          $empName=$r['user']['name'];   
       }
       if(count($r['user']['attendance'])>0){
                   
            $att=$r['user']['attendance'];
           
            $isAttendanceDate=array_filter($att,function($item)use($date){
                return $item['clock_in_date']==$date;  
            });


          

            if(count($isAttendanceDate)){
              foreach($isAttendanceDate as $at){
               
               
                 $attendanceId=$at['id'];
              

                

                  $timestamp = strtotime($at['clock_in_time']);
                    $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                    $dt->setTimestamp($timestamp);
                    $clock_in= $dt->format('h:i A');



                 if($at['clock_out_time']){

                  $timestamp = strtotime($at['clock_out_time']);
                  $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                  $dt->setTimestamp($timestamp);
                  $clock_out= $dt->format('h:i A');
                  
                 }
                 if($at['first_break_start']){

                  $timestamp = strtotime($at['first_break_start']);
                  $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                  $dt->setTimestamp($timestamp);
                  $b_one_start= $dt->format('h:i A');

                 
                 }
                 if($at['first_break_end']){
                 
                      $timestamp = strtotime($at['first_break_end']);
                      $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                      $dt->setTimestamp($timestamp);
                      $b_one_end= $dt->format('h:i A');
                 }
                 if($at['second_break_start']){
                 

                    $timestamp = strtotime($at['second_break_start']);
                    $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                    $dt->setTimestamp($timestamp);
                    $b_two_start= $dt->format('h:i A');
                 }
                 if($at['second_break_end']){
                 

                   $timestamp = strtotime($at['second_break_end']);
                    $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                    $dt->setTimestamp($timestamp);
                    $b_two_end= $dt->format('h:i A');
                 }
                 if($at['third_break_start']){
                  
                   $timestamp = strtotime($at['third_break_start']);
                    $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                    $dt->setTimestamp($timestamp);
                    $b_three_start= $dt->format('h:i A');
                 }
                 if($at['third_break_end']){
                   
                   $timestamp = strtotime($at['third_break_end']);
                    $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                    $dt->setTimestamp($timestamp);
                    $b_three_end= $dt->format('h:i A');
                 }
                 if($at['four_break_start']){

                  $timestamp = strtotime($at['four_break_start']);
                  $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                  $dt->setTimestamp($timestamp);
                  $b_four_start= $dt->format('h:i A');

                 
                 }
                 if($at['four_break_end']){
                   

                    $timestamp = strtotime($at['four_break_end']);
                    $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                    $dt->setTimestamp($timestamp);
                    $b_four_end= $dt->format('h:i A');
                 }
                 if($at['five_break_start']){
                  
                   $timestamp = strtotime($at['five_break_start']);
                   $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                   $dt->setTimestamp($timestamp);
                   $b_five_start= $dt->format('h:i A');
                 }
                 if($at['five_break_end']){
                 
                   $timestamp = strtotime($at['five_break_end']);
                   $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                   $dt->setTimestamp($timestamp);
                   $b_five_end= $dt->format('h:i A');
                 }
                 if($at['six_break_start']){
                 
                   $timestamp = strtotime($at['six_break_start']);
                   $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                   $dt->setTimestamp($timestamp);
                   $b_six_start= $dt->format('h:i A');
                 }
                 if($at['six_break_end']){

                   $timestamp = strtotime($at['six_break_end']);
                   $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                   $dt->setTimestamp($timestamp);
                   $b_six_end= $dt->format('h:i A');
                  
                 }
                 if($at['seven_break_start']){
                  

                   $timestamp = strtotime($at['seven_break_start']);
                   $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                   $dt->setTimestamp($timestamp);
                   $b_seven_start= $dt->format('h:i A');
                 }
                 if($at['seven_break_end']){

                   $timestamp = strtotime($at['seven_break_end']);
                   $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                   $dt->setTimestamp($timestamp);
                   $b_seven_end= $dt->format('h:i A');
                
                 }
                 if($at['eight_break_start']){
                  $timestamp = strtotime($at['eight_break_start']);
                   $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                   $dt->setTimestamp($timestamp);
                   $b_eight_start= $dt->format('h:i A');
                  
                 }
                 if($at['eight_break_end']){

                   $timestamp = strtotime($at['eight_break_end']);
                   $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                   $dt->setTimestamp($timestamp);
                   $b_eight_end= $dt->format('h:i A');
                 
                 }
                 if($at['nine_break_start']){
                  
                   $timestamp = strtotime($at['nine_break_start']);
                   $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                   $dt->setTimestamp($timestamp);
                   $b_nine_start= $dt->format('h:i A');
                 }
                 if($at['nine_break_end']){
                   $timestamp = strtotime($at['nine_break_end']);
                   $dt = new \DateTime("now", new \DateTimeZone($tz)); 
                   $dt->setTimestamp($timestamp);
                   $b_nine_end= $dt->format('h:i A');
                 }
              }
            }

            $clock_in= $clock_in; 
            $clock_out= $clock_out;
            
        }
        
      
    
      @endphp 
            
        <tr>

            <td>{{$r['date']}}</td>
            <td>{{$r['start_time']}}</td>
            <td>{{$r['end_time']}}</td>
            <td>{{$empName}}</td>
            <td>{{$clock_in}}</td>
            <td>{{$clock_out}}</td>
            @if(count($r['attendance_setup'])>0)
            @if($r['attendance_setup']['s1']!="")
             <td>{{$b_one_start}}</td>
            @endif
            @if($r['attendance_setup']['e1']!="")
             <td>{{$b_one_end}}</td>
            @endif
            @if($r['attendance_setup']['s2']!="")
             <td>{{$b_two_start}}</td>
            @endif
            @if($r['attendance_setup']['e2']!="")
             <td>{{$b_two_end}}</td>
            @endif
            @if($r['attendance_setup']['s3']!="")
             <td>{{$b_three_start}}</td>
            @endif
            @if($r['attendance_setup']['e3']!="")
             <td>{{$b_three_end}}</td>
            @endif
            @if($r['attendance_setup']['s4']!="")
             <td>{{$b_four_start}}</td>
            @endif
            @if($r['attendance_setup']['e4']!="")
             <td>{{$b_four_end}}</td>
            @endif
            @if($r['attendance_setup']['s5']!="")
             <td>{{$b_five_start}}</td>
            @endif
            @if($r['attendance_setup']['e5']!="")
             <td>{{$b_five_end}}</td>
            @endif
            @if($r['attendance_setup']['s6']!="")
             <td>{{$b_six_start}}</td>
            @endif
            @if($r['attendance_setup']['e6']!="")
             <td>{{$b_six_end}}</td>
            @endif
            @if($r['attendance_setup']['s7']!="")
             <td>{{$b_seven_start}}</td>
            @endif
            @if($r['attendance_setup']['e7']!="")
             <td>{{$b_seven_end}}</td>
            @endif
            @if($r['attendance_setup']['s8']!="")
             <td>{{$b_eight_start}}</td>
            @endif
            @if($r['attendance_setup']['e8']!="")
             <td>{{$b_eight_end}}</td>
            @endif
            @if($r['attendance_setup']['s9']!="")
             <td>{{$b_nine_start}}</td>
            @endif
            @if($r['attendance_setup']['e9']!="")
             <td>{{$b_nine_end}}</td>
            @endif
          
            <!--<td>{{unPaidHours($calPaidTimeReasons,$attendanceId)}}</td>-->
            @php 
            
              $unpaid=unPaidHours($calPaidTimeReasons,$attendanceId);
              $calculated_hours=calculatedTime1($calPaidTimeReasons,$attendanceId);

             $cal_hrs= strtotime($calculated_hours)-strtotime($unpaid);
             $cal_hrs=date("H:i", $cal_hrs);
             if($calculated_hours=="00:00"){
                $cal_hrs="00:00";
              }

            @endphp
            
            @if($unpaid!="00:00")  
            <td>{{$unpaid}}</td>
            @endif
            @if($cal_hrs!="00:00")
            <td> {{ $cal_hrs }}</td>
            @endif
            @if($calculated_hours!="00:00")
            <td>{{$calculated_hours}}</td>
            @endif

            @endif
           
   
           
          
        </tr>
      @endforeach  
     
      
     
     
   
    </tbody>
</table>
