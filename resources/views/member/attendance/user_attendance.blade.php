

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
              
                    @if ($attendance['half_day'] == 'yes')
                        <label class="label label-success attendence-list">@lang('modules.attendance.halfDay')</label>
                    @endif
                    @if ($attendance['late'] == 'yes')
                        <label class="label label-danger attendence-list">@lang('modules.attendance.late')</label>
                    @endif
                @endforeach
            </td>
            <td>
             
            <?php
                            $company=\App\Company::where('id',\Illuminate\Support\Facades\Auth::user()->company_id)->first();

                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['clock_in_time']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                            //$dt->format($global->time_format);
                        //                                    $dateTimeData=$dt;
                        //                                    dd($dateTimeData);
                            ?>
                {{ $dt->format($global->time_format)}} 
            </td>
          
            @if($attendance['attendance_setup']['s1']!="")
             <td >
                        @if(!is_null($attendance['first_break_start']))
                            <?php
                           
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['first_break_start']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                            //$dt->format($global->time_format);
                        //                                    $dateTimeData=$dt;
                        //                                    dd($dateTimeData);
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else -  @endif
            </td>
            @endif

            @if($attendance['attendance_setup']['e1']!="")
            <td>
               
                        @if(!is_null($attendance['first_break_end']))
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['first_break_end']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                            //$dt->format($global->time_format);
                        //                                    $dateTimeData=$dt;
                        //                                    dd($dateTimeData);
                            ?>
                           

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @if($attendance['attendance_setup']['s2']!="")
            <td>
                        @if(!is_null($attendance['second_break_start']))
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['second_break_start']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                       
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @if($attendance['attendance_setup']['e2']!="")
            <td>
                        @if(!is_null($attendance['second_break_end']))
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['second_break_end']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                           
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif

            @if($attendance['attendance_setup']['s3']!="")
            <td>
              
                        @if(!is_null($attendance['third_break_start']))
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['third_break_start']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                       
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @if($attendance['attendance_setup']['e3']!="")
            <td>
                        @if(!is_null($attendance['third_break_end']))
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['third_break_end']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                           
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @if($attendance['attendance_setup']['s4']!="")
            <td>
               
                        @if(!is_null($attendance['four_break_start']))
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['four_break_start']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                            
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @if($attendance['attendance_setup']['e4']!="")
          
            <td>
           
                        @if(!is_null($attendance['four_break_end']))
                            <?php
                            $tz = $company->timezone;
                            
                            $timestamp = strtotime($attendance['four_break_end']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                          
                       
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @if($attendance['attendance_setup']['s5']!="")
            <td>
                        @if(!is_null($attendance['five_break_start']))
                     
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['five_break_start']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                       
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @if($attendance['attendance_setup']['e5']!="")
            <td>
                        @if(!is_null($attendance['five_break_end']))
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['five_break_end']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                       
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @if($attendance['attendance_setup']['s6']!="")
            <td>
                        @if(!is_null($attendance['six_break_start']))
                       
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['six_break_start']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                       
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @if($attendance['attendance_setup']['e6']!="")
            <td>
                        @if(!is_null($attendance['six_break_end']))
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['six_break_end']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                       
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @if($attendance['attendance_setup']['s7']!="")
            <td>
          
                        @if(!is_null($attendance['seven_break_start']))
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['seven_break_start']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                       
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @if($attendance['attendance_setup']['e7']!="")
            <td>
                        @if(!is_null($attendance['seven_break_end']))
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['seven_break_end']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                       
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @if($attendance['attendance_setup']['s8']!="")
            <td>
                        @if(!is_null($attendance['eight_break_start']))
                            <?php
                             
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['eight_break_start']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                       
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @if($attendance['attendance_setup']['e8']!="")
            <td>
                        @if(!is_null($attendance['eight_break_end']))
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['eight_break_end']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                       
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @if($attendance['attendance_setup']['s9']!="")
            @if(!is_null($attendance['attendance_setup']['s9']))
            <td>
                        @if(!is_null($attendance['nine_break_start']))
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['nine_break_start']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                       
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            @endif
            @if($attendance['attendance_setup']['e9']!="")
            <td>
                        @if(!is_null($attendance['nine_break_end']))
                            <?php
                           
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['nine_break_end']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                       
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            @endif
            <td>
                        @if(!is_null($attendance['clock_out_time']))
                            <?php
                            $tz = $company->timezone;
                            $timestamp = strtotime($attendance['clock_out_time']);
                            $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
                            $dt->setTimestamp($timestamp);
                       
                            ?>

                        {{ $dt->format($global->time_format)}}
                        @else - @endif
            </td>
            <td class="bt-border" style="padding-bottom: 5px;">
            <strong>@lang('modules.attendance.clock_in') IP: </strong> {{ $attendance['clock_in_ip'] }}<br>
            <strong>@lang('modules.attendance.clock_out') IP: </strong> {{ $attendance['clock_out_ip']}}<br>
            <strong>@lang('modules.attendance.working_from'): </strong> {{ $attendance['working_from']}}<br>
            @if(\Illuminate\Support\Facades\Auth::user()->cans('view_delete'))   <a href="javascript:;" data-attendance-id="{{ $attendance['aId'] }}" class="delete-attendance btn btn-outline btn-danger btn-xs m-t-5"><i class="fa fa-times"></i> @lang('app.delete')</a>@endif
            </td>
          {{--  <td colspan="20">
                <table width="100%">
                    @foreach($dateData['attendance'] as $attendance)
               
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
                                @if (!is_null($attendance->clock_out_time))
                                    {{ $attendance->clock_out_time->timezone($company->timezone)->format($global->time_format) }}
                                @else - @endif
                            </td>
                            <td class="bt-border" style="padding-bottom: 5px;">
                                <strong>@lang('modules.attendance.clock_in') IP: </strong>
                                {{ $attendance->clock_in_ip }}<br>
                                <strong>@lang('modules.attendance.clock_out') IP: </strong>
                                {{ $attendance->clock_out_ip }}<br>
                                <strong>@lang('modules.attendance.working_from'): </strong>
                                {{ $attendance->working_from }}<br>
                                <a href="javascript:;" data-attendance-id="{{ $attendance->aId }}"
                                    class="delete-attendance btn btn-outline btn-danger btn-xs m-t-5"><i
                                        class="fa fa-times"></i> @lang('app.delete')</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </td>--}}

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

