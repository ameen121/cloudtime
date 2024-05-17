
@foreach ($dateWiseData as $key => $dateData)
    @php
        $currentDate = \Carbon\Carbon::parse($key);
    @endphp
    @if($dateData['attendance'])

        <tr>
            <td>
                {{ $currentDate->format($global->date_format) }}
                <br>
                <label class="label label-success">{{ $currentDate->format('l') }}</label>
            </td>
            <td><label class="label label-success attendence-list">@lang('modules.attendance.present')</label>
            @foreach ($dateData['attendance'] as $attendance)
                @if ($attendance->late == 'yes')
                  <br class="pt-4">  <label class="label label-danger attendence-list pt-4">@lang('modules.attendance.late')</label>
                @endif
                @endforeach
            
            </td>
            <td colspan="16">
                <table width="100%" >

                    @foreach($dateData['attendance'] as $attendance)
                            <?php // dd($attendance->first_open_break_time-);  ?>
                        <tr>

                            <td width="15%" class="al-center bt-border">
                                {{ $attendance->clock_in_time->timezone($global->timezone)->format($global->time_format) }}
                            </td>
                            <td width="15%" class="al-center bt-border">
                                @if(!is_null($attendance->first_open_break_time))
                                    <?php
                                    $tz = $global->timezone;
                                    $timestamp = strtotime($attendance->first_open_break_time);
                                    $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                                    $dt->setTimestamp($timestamp);
                                    //$dt->format($global->time_format);
//                                    $dateTimeData=$dt;
//                                    dd($dateTimeData);
                                    ?>

                                {{ $dt->format($global->time_format)}}
                                @else - @endif

                            </td>
                            <td width="15%" class="al-center bt-border">
                                @if(!is_null($attendance->first_close_break_time))
                                    <?php
                                    $tz = $global->timezone;
                                    $timestamp = strtotime($attendance->first_close_break_time);
                                    $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                                    $dt->setTimestamp($timestamp);
                                    //$dt->format($global->time_format);
//                                    $dateTimeData=$dt;
//                                    dd($dateTimeData);
                                    ?>

                                {{ $dt->format($global->time_format)}}
                                @else - @endif
                            </td>
                            <td width="15%" class="al-center bt-border">
                                @if(!is_null($attendance->lunch_break_start_time))
                                        <?php
                                        $tz = $global->timezone;
                                        $timestamp = strtotime($attendance->lunch_break_start_time);
                                        $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                                        $dt->setTimestamp($timestamp);
                                        //$dt->format($global->time_format);
//                                    $dateTimeData=$dt;
//                                    dd($dateTimeData);
                                        ?>

                                    {{ $dt->format($global->time_format)}}
                                @else - @endif
                            </td>
                            <td width="15%" class="al-center bt-border">
                                @if(!is_null($attendance->lunch_break_close_time))
                                        <?php
                                        $tz = $global->timezone;
                                        $timestamp = strtotime($attendance->lunch_break_close_time);
                                        $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                                        $dt->setTimestamp($timestamp);
                                        //$dt->format($global->time_format);
//                                    $dateTimeData=$dt;
//                                    dd($dateTimeData);
                                        ?>

                                    {{ $dt->format($global->time_format)}}
                                @else - @endif
                            </td>
                            <td width="25%" class="al-center bt-border">

                                @if(!is_null($attendance->clock_out_time)) {{ $attendance->clock_out_time->timezone($global->timezone)->format($global->time_format) }} @else - @endif
                            </td>
<?php

// //dd($attendanceSetup);
// $datetime1=strtotime($attendance->clock_in_time);
// $datetime2=strtotime($attendance->clock_out_time);
// $diff_minutes = ($datetime1 - $datetime2) / 60;

// for($i=1;$i<=count($attendanceSetup);$i++)
// {
//     if($i==1)
//     {
//          $datetime1=strtotime($attendance->first_open_break_time);
// $datetime2=strtotime($attendance->first_close_break_time);
// $diff_minutes = ($datetime1 - $datetime2) / 60;
//     $attedanceData1= DB::table('attandance_setup')->where('order_by',$i)->first();
//     if($attedanceData1->status==0)
//     {
        
//     }
//     }
//      elseif($i==2)
//     {
//          $datetime1=strtotime($attendance->lunch_break_start_time);
// $datetime2=strtotime($attendance->lunch_break_close_time);
// $diff_minutes = ($datetime1 - $datetime2) / 60;
//     $attedanceData1=DB::table('attandance_setup')->where('order_by',$i)->first();
//     if($attedanceData1->status==0)
//     {
        
//     }
//     }
//      elseif($i==3)
//     {
//          $datetime1=strtotime($attendance->clock_in_time);
// $datetime2=strtotime($attendance->clock_out_time);
// $diff_minutes = ($datetime1 - $datetime2) / 60;
//     $attedanceData1=DB::table('attandance_setup')->where('order_by',$i)->first();
//     if($attedanceData1->status==0)
//     {
        
//     }
//     }
//      elseif($i==4)
//     {
//          $datetime1=strtotime($attendance->clock_in_time);
// $datetime2=strtotime($attendance->clock_out_time);
// $diff_minutes = ($datetime1 - $datetime2) / 60;
//     $attedanceData1=DB::table('attandance_setup')->where('order_by',$i)->first();
//     if($attedanceData1->status==0)
//     {
        
//     }
//     }
//      elseif($i==5)
//     {
//          $datetime1=strtotime($attendance->clock_in_time);
// $datetime2=strtotime($attendance->clock_out_time);
// $diff_minutes = ($datetime1 - $datetime2) / 60;
//     $attedanceData1=DB::table('attandance_setup')->where('order_by',$i)->first();
//   if($attedanceData1->status==0)
//     {
        
//     }
//     }
   
// }

?>
 <td width="25%" class="al-center bt-border">

<?php
//echo $mainTime;
?>

                            </td>

                            <td class="bt-border" style="padding-bottom: 5px;">
                                <strong>@lang('modules.attendance.clock_in') IP: </strong> {{ $attendance->clock_in_ip }}<br>
                                <strong>@lang('modules.attendance.clock_out') IP: </strong> {{ $attendance->clock_out_ip }}<br>
                                <strong>@lang('modules.attendance.working_from'): </strong> {{ $attendance->working_from }}<br>
                             @if(\Illuminate\Support\Facades\Auth::user()->cans('view_delete'))   <a href="javascript:;" data-attendance-id="{{ $attendance->aId }}" class="delete-attendance btn btn-outline btn-danger btn-xs m-t-5"><i class="fa fa-times"></i> @lang('app.delete')</a>@endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </td>

        </tr>
    @else
        <tr>
            <td>
                {{ $currentDate->format($global->date_format) }}
                <br>
                <label class="label label-success">{{ $currentDate->format('l') }}</label>
            </td>
            <td>
                @if(!$dateData['holiday'] && !$dateData['leave'])
                    <label class="label label-info">@lang('modules.attendance.absent')</label>
                @elseif($dateData['leave'])
                    <label class="label label-primary">@lang('modules.attendance.leave')</label>
                @else
                    <label class="label label-megna">@lang('modules.attendance.holiday')</label>
                @endif
            </td>
            <td colspan="3">
                <table width="100%">
                    <tr>
                        <td width="25%" class="al-center">-</td>
                        <td width="25%" class="al-center">-</td>
                        <td style="padding-bottom: 5px;text-align: left;">
                            @if($dateData['holiday']  && !$dateData['leave'])
                                @lang('modules.attendance.holidayfor') {{ ucwords($dateData['holiday']->occassion) }}
                            @elseif($dateData['leave'])
                                @lang('modules.attendance.leaveFor') {{ ucwords($dateData['leave']['reason']) }}
                            @else
                                -
                            @endif

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    @endif

@endforeach

