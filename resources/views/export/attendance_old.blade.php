<table>
   

    <thead>
   
  
    <tr>
        <th>Roster Date</th>
        <th>Roster Start Time</th>
        <th>Roster End Time</th>
        <th>Employee Name</th>
        <th>Clock In</th>
        <th>Clock Out</th>
        @foreach($reasons as $res)
        <th>{{$res->name}}</th>
        @endforeach
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
                 $clock_in=date("H:i",strtotime($at['clock_in_time']));
                 if($at['clock_out_time']){
                   $clock_out=date("H:i",strtotime($at['clock_out_time']));
                 }
                 if($at['first_break_start']){
                   $b_one_start=date("H:i",strtotime($at['first_break_start']));
                 }
                 if($at['first_break_end']){
                   $b_one_end=date("H:i",strtotime($at['first_break_end']));
                 }
                 if($at['second_break_start']){
                   $b_two_start=date("H:i",strtotime($at['second_break_start']));
                 }
                 if($at['second_break_end']){
                   $b_two_end=date("H:i",strtotime($at['second_break_end']));
                 }
                 if($at['third_break_start']){
                   $b_three_start=date("H:i",strtotime($at['third_break_start']));
                 }
                 if($at['third_break_end']){
                   $b_three_end=date("H:i",strtotime($at['third_break_end']));
                 }
                 if($at['four_break_start']){
                   $b_four_start=date("H:i",strtotime($at['four_break_start']));
                 }
                 if($at['four_break_end']){
                   $b_four_end=date("H:i",strtotime($at['four_break_end']));
                 }
                 if($at['five_break_start']){
                   $b_five_start=date("H:i",strtotime($at['five_break_start']));
                 }
                 if($at['five_break_end']){
                   $b_five_end=date("H:i",strtotime($at['five_break_end']));
                 }
                 if($at['six_break_start']){
                   $b_six_start=date("H:i",strtotime($at['six_break_start']));
                 }
                 if($at['six_break_end']){
                   $b_six_end=date("H:i",strtotime($at['six_break_end']));
                 }
                 if($at['seven_break_start']){
                   $b_seven_start=date("H:i",strtotime($at['seven_break_start']));
                 }
                 if($at['seven_break_end']){
                   $b_seven_end=date("H:i",strtotime($at['seven_break_end']));
                 }
                 if($at['eight_break_start']){
                   $b_eight_start=date("H:i",strtotime($at['eight_break_start']));
                 }
                 if($at['eight_break_end']){
                   $b_eight_end=date("H:i",strtotime($at['eight_break_end']));
                 }
                 if($at['nine_break_start']){
                   $b_nine_start=date("H:i",strtotime($at['nine_break_start']));
                 }
                 if($at['nine_break_end']){
                   $b_nine_end=date("H:i",strtotime($at['nine_break_end']));
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
           
            
            @endif
           

           
          
        </tr>
      @endforeach  
      
     
     
   
    </tbody>
</table>
