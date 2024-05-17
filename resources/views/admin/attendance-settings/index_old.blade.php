@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 bg-title-right">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet"
          href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">

@endpush

@section('content')
    <script>
        $( document ).ready(function() {
           var total = $(".nodiv").length;

          
       $('.totaltextfield').val(parseInt(2+total*2));
       } );
        function add_field() {
                
            var total = $(".nodiv").length;
       var totaltextfield = $('.totaltextfield').val();
            var clockin_in_day=$('#clockin_in_day').val();
          
  
       console.log(totaltextfield);
       console.log(total);
       console.log(clockin_in_day);
      if(clockin_in_day>totaltextfield){
        var total_text = document.getElementsByClassName("input_text");
            total_text = total_text.length + 2;
           
           
      
           
             $('.totaltextfield').val(total_text+2+parseInt(total*2));

            
            document.getElementById("field_div").innerHTML = document.getElementById("field_div").innerHTML +
                "<p id='input_text" + total_text + "_wrapper'> <select class='form-control col-md-4' id='exampleFormControlSelect1' name='type[]'><option value='1'>Paid</option><option value='0'>Unpaid</option></select> <input type='text' class='input_text' id='input_text" + total_text + "' placeholder='Enter Text' name='check_setup[]'><input type='text' class='input_text' id='input_text" + total_text + "' placeholder='Enter Text' name='check_setup[]'><input type='button' value='Remove' onclick=remove_field('input_text" + total_text + "');></p>";
       }
            
            
         }
         var incre=0;

        function remove_field(id) {
      
            var total_text = document.getElementsByClassName("input_text");
            document.getElementById(id + "_wrapper").remove();

            var reasonid = id.split("-");

            reason_delete(reasonid[1]);
            total_text = total_text.length;
            var clockin_in_day=$('#clockin_in_day').val();
            var totaltextfield = $('.totaltextfield').val();
            var total = $(".nodiv").length;

                incre1=incre+2;
                console.log( total);

                console.log( total_text+total*2);
            $('.totaltextfield').val(2+total_text+total*2);
          
        }
        
        

            $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function reason_delete(reasonid){
     
if (confirm("Are you sure want to Delete?"+reasonid)){
$.ajax({
    type: "GET",
    url: "reason_order/",
    data: {
                id: reasonid,
               

            },
   

    success: function(response) {

      //  alert(response);

    }

});

}


}
    </script>
    <style>


        input[type="text"] {
            width: 200px;
            height: 35px;
            margin-right: 2px;
            border-radius: 3px;
            border: 1px solid green;
            padding: 5px;
        }

        input[type="button"] {
            background: none;
            color: white;
            border: none;
            width: 200px;
            height: 35px;
            border-radius: 3px;
            background-color: green;
            font-size: 16px;
        }
    </style>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">@lang('app.update') @lang('app.menu.attendanceSettings')</div>

                <div class="vtabs customvtab m-t-10">

                    @include('sections.admin_setting_menu')

                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">
                            {!! Form::open(['id'=>'editSettings','class'=>'ajax-form','method'=>'PUT']) !!}
                            <div class="row">
                                <div class="form-body ">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="input-group bootstrap-timepicker timepicker">
                                                <label>@lang('modules.attendance.officeStartTime')</label>
                                                <input type="text" name="office_start_time" id="office_start_time"
                                                       class="form-control"
                                                       value="{{ \Carbon\Carbon::createFromFormat('H:i:s', $attendanceSetting->office_start_time)->format($global->time_format) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="input-group bootstrap-timepicker timepicker">
                                                <label>@lang('modules.attendance.officeEndTime')</label>
                                                <input type="text" name="office_end_time" id="office_end_time"
                                                       class="form-control"
                                                       value="{{ \Carbon\Carbon::createFromFormat('H:i:s', $attendanceSetting->office_end_time)->format($global->time_format) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="input-group bootstrap-timepicker timepicker">
                                                <label>@lang('modules.attendance.halfDayMarkTime')</label>
                                                <input type="text" name="halfday_mark_time" id="halfday_mark_time"
                                                       class="form-control"
                                                       value="@if($attendanceSetting->halfday_mark_time){{ \Carbon\Carbon::createFromFormat('H:i:s', $attendanceSetting->halfday_mark_time)->format($global->time_format) }}@else 01:00 @endif">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="late_mark_duration">@lang('modules.attendance.lateMark')</label>
                                            <input type="number" class="form-control" id="late_mark_duration"
                                                   name="late_mark_duration"
                                                   value="{{ $attendanceSetting->late_mark_duration }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="late_mark_duration">@lang('modules.attendance.checkininday')</label>
                                            <input type="number" class="form-control" id="clockin_in_day"
                                                   name="clockin_in_day"  min="2" max="20" step="2"
                                                   value="{{ $attendanceSetting->clockin_in_day }}" onkeyup="checkmyinput()">
                                        </div>
                                    </div>
                                    <input type="hidden" class="totaltextfield" name="totaltextfield" value="">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <div id="field_div">
                                                <input type="button" value="Add Check Setup" onclick="add_field();">
                                            </div>
                                        </div>
                                            <?php
                                            foreach ($attendanceSetup as $datas) {

                                                @$array[] = @$datas->name;
                                                @$arrayid[] = @$datas->order_by;
                                                @$array1[] = @$datas->status;
                                                
                                            }
                                           
                                        
                                            $incprev=0;
                                            $countext=0;
                                            ?>
                                            <input type="hidden" name="" value="{{count($array)}}" class="total_db">
@foreach($array as $key=> $data)
<input type="hidden" value="{{@$arrayid[$key]}}" name="id[]">                                          
@if($key%2==0)





                                                <div class="col-md-12 nodiv" id="input_text-{{@$arrayid[$key]}}_wrapper">

                                                    <div class="col-md-3">
                                                        <select class="form-control" id="exampleFormControlSelect1"
                                                                name="type[]">
                                                            <option value="1" @if(@$array1[$key]==1) selected @endif>Paid
                                                            </option>
                                                            <option value="0" @if(@$array1[$key]==0) selected @endif>
                                                                UnPaid
                                                            </option>
                                                        </select>
                                                    </div>
                                                   @if($key+1==$incprev)
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" id="input_text-{{@$arrayid[$key]}}"
                                                               name="check_setup[]"
                                                               value="{{ @$array[$key+1] }}">
                                                    </div>
                                                    @else
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" id="input_text-{{@$arrayid[$key]}}"
                                                               name="check_setup[]"
                                                               value="{{ @$array[$key] }}">
                                                    </div>
                                                    @endif
                                                    @if($key+1==$incprev)
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" id="input_text-{{@$arrayid[$key]}}"
                                                               name="check_setup[]"
                                                               value="{{ @$array[$key+2] }}">
                                                    </div>
                                                    @else
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" id="input_text-{{@$arrayid[$key]}}"
                                                               name="check_setup[]"
                                                               value="{{ @$array[$key+1] }}">
                                                    </div>
                                                    @endif
                                                    <div class="col-md-3">
                                                        <input type='button' value='Remove'
                                                               onclick=remove_field('input_text-{{@$arrayid[$key]}}');>
                                                    </div>
                                                </div>
                                                @php
                                                $incprev=$key+2;
                                                @endphp
                                                @endif
                                            @endforeach
                                            {{-- </div>@if(!empty($array[2]))
                                            <div class="col-md-12" id="input_text2_wrapper">

                                                <div class="col-md-3">
                                                    <select class="form-control" id="exampleFormControlSelect1"
                                                            name="type[]">
                                                        <option value="1" @if(@$array1[2]==1) selected @endif>Paid
                                                        </option>
                                                        <option value="0" @if(@$array1[2]==0) selected @endif>
                                                            UnPaid
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id=""
                                                           name="check_setup[]"
                                                           value="{{ @$array[2] }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id=""
                                                           name="check_setup[]"
                                                           value="{{ @$array[3] }}">
                                                </div>

                                                <div class="col-md-3">
                                                    <input type='button' value='Remove'
                                                           onclick=remove_field('input_text2');>
                                                </div>
                                            </div>
@endif

                                        @if(!empty($array[4]))
                                            <div class="col-md-12" id="input_text3_wrapper">

                                                <div class="col-md-3">
                                                    <select class="form-control" id="exampleFormControlSelect1"
                                                            name="type[]">
                                                        <option value="1" @if(@$array1[4]==1) selected @endif>Paid
                                                        </option>
                                                        <option value="0" @if(@$array1[4]==0) selected @endif>
                                                            UnPaid
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id=""
                                                           name="check_setup[]"
                                                           value="{{ @$array[4] }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id=""
                                                           name="check_setup[]"
                                                           value="{{ @$array[5] }}">
                                                </div>

                                                <div class="col-md-3">
                                                    <input type='button' value='Remove'
                                                           onclick=remove_field('input_text3');>
                                                </div>
                                            </div>
                                        @endif

                                        @if(!empty($array[6]))
                                            <div class="col-md-12" id="input_text4_wrapper">

                                                <div class="col-md-3">
                                                    <select class="form-control" id="exampleFormControlSelect1"
                                                            name="type[]">
                                                        <option value="1" @if(@$array1[6]==1) selected @endif>Paid
                                                        </option>
                                                        <option value="0" @if(@$array1[6]==0) selected @endif>
                                                            UnPaid
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id=""
                                                           name="check_setup[]"
                                                           value="{{ @$array[6] }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id=""
                                                           name="check_setup[]"
                                                           value="{{ @$array[7] }}">
                                                </div>

                                                <div class="col-md-3">
                                                    <input type='button' value='Remove'
                                                           onclick=remove_field('input_text4');>
                                                </div>
                                            </div>
                                        @endif --}}

                                            {{--                                            <div class="checkbox checkbox-info  col-md-10">--}}
                                                {{--                                                <input id="first_break" name="first_break" value="yes"--}}
                                                {{--                                                       @if($attendanceSetting->first_break == "yes") checked--}}
                                                {{--                                                       @endif--}}
                                                {{--                                                       type="checkbox">--}}
                                                {{--                                                <label for="first_break">First Break Paid</label>--}}
                                                {{--                                            </div>--}}

                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-info  col-md-10">
                                                <input id="employee_clock_in_out" name="employee_clock_in_out"
                                                       value="yes"
                                                       @if($attendanceSetting->employee_clock_in_out == "yes") checked
                                                       @endif
                                                       type="checkbox">
                                                <label for="employee_clock_in_out">@lang('modules.attendance.allowSelfClock')</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-info  col-md-10">
                                                <input id="radius_check" name="radius_check" value="yes"
                                                       @if($attendanceSetting->radius_check == "yes") checked
                                                       @endif
                                                       type="checkbox">
                                                <label for="radius_check">@lang('modules.attendance.checkForRadius')</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 @if($attendanceSetting->radius_check == "no") hidden @endif"
                                         id="radiusBox">
                                        <div class="form-group">
                                            <label for="late_mark_duration">@lang('modules.attendance.radius')</label>
                                            <input type="number" class="form-control" id="radius"
                                                   name="radius"
                                                   value="{{ $attendanceSetting->radius }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-info  col-md-10">
                                                <input id="ip_check" name="ip_check" value="yes"
                                                       @if($attendanceSetting->ip_check == "yes") checked
                                                       @endif
                                                       type="checkbox">
                                                <label for="ip_check">@lang('modules.attendance.checkForIp')</label>
                                            </div>
                                        </div>
                                    </div>     

                                    <div class="col-md-12 @if($attendanceSetting->ip_check == "no") hidden @endif"
                                         id="ipBox">
                                        <div id="addMoreBox1" class="clearfix">
                                            @forelse($ipAddresses as $index => $ipAddress)
                                                <div class="col-md-5" style="margin-left: 5px;">
                                                    <div class="form-group" id="occasionBox">
                                                        <input class="form-control" type="text" value="{{ $ipAddress }}"
                                                               name="ip[{{ $index }}]"
                                                               placeholder="@lang('modules.attendance.ipAddress')"/>
                                                        <div id="errorOccasion"></div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-md-5" style="margin-left: 5px;">
                                                    <div class="form-group" id="occasionBox">
                                                        <input class="form-control" type="text" name="ip[0]"
                                                               placeholder="@lang('modules.attendance.ipAddress')"/>
                                                        <div id="errorOccasion"></div>
                                                    </div>
                                                </div>
                                            @endforelse
                                            <div class="col-md-1">
                                                {{--<button type="button"  onclick="removeBox(1)"  class="btn btn-sm btn-danger"><i class="fa fa-times"></i></button>--}}
                                            </div>
                                        </div>
                                        <div id="insertBefore"></div>
                                        <div class="clearfix">

                                        </div>
                                        <button type="button" id="plusButton" class="btn btn-sm btn-info"
                                                style="margin-bottom: 20px;margin-left: 13px">
                                            Add More <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="col-xs-12">
                                        <hr>
                                        <label class="control-label col-md-12 p-l-0">@lang('modules.attendance.officeOpenDays')</label>
                                        <div class="form-group">
                                            <div class="checkbox checkbox-inline checkbox-info  col-md-2 m-b-10">
                                                <input id="open_mon" name="office_open_days[]" value="1"
                                                       @if($openDays)
                                                           @foreach($openDays as $day)
                                                               @if($day == 1) checked @endif
                                                       @endforeach
                                                       @endif
                                                       type="checkbox">
                                                <label for="open_mon">@lang('app.monday')</label>
                                            </div>
                                            <div class="checkbox checkbox-inline checkbox-info  col-md-2 m-b-10">
                                                <input id="open_tues" name="office_open_days[]" value="2"
                                                       @if($openDays)
                                                           @foreach($openDays as $day)
                                                               @if($day == 2) checked @endif
                                                       @endforeach
                                                       @endif
                                                       type="checkbox">
                                                <label for="open_tues">@lang('app.tuesday')</label>
                                            </div>
                                            <div class="checkbox checkbox-inline checkbox-info  col-md-2 m-b-10">
                                                <input id="open_wed" name="office_open_days[]" value="3"
                                                       @if($openDays)
                                                           @foreach($openDays as $day)
                                                               @if($day == 3) checked @endif
                                                       @endforeach
                                                       @endif
                                                       type="checkbox">
                                                <label for="open_wed">@lang('app.wednesday')</label>
                                            </div>
                                            <div class="checkbox checkbox-inline checkbox-info  col-md-2 m-b-10">
                                                <input id="open_thurs" name="office_open_days[]" value="4"
                                                       @if($openDays)
                                                           @foreach($openDays as $day)
                                                               @if($day == 4) checked @endif
                                                       @endforeach
                                                       @endif
                                                       type="checkbox">
                                                <label for="open_thurs">@lang('app.thursday')</label>
                                            </div>
                                            <div class="checkbox checkbox-inline checkbox-info  col-md-2 m-b-10">
                                                <input id="open_fri" name="office_open_days[]" value="5"
                                                       @if($openDays)
                                                           @foreach($openDays as $day)
                                                               @if($day == 5) checked @endif
                                                       @endforeach
                                                       @endif
                                                       type="checkbox">
                                                <label for="open_fri">@lang('app.friday')</label>
                                            </div>
                                            <div class="checkbox checkbox-inline checkbox-info  col-md-2 m-b-10">
                                                <input id="open_sat" name="office_open_days[]" value="6"
                                                       @if($openDays)
                                                           @foreach($openDays as $day)
                                                               @if($day == 6) checked @endif
                                                       @endforeach
                                                       @endif
                                                       type="checkbox">
                                                <label for="open_sat">@lang('app.saturday')</label>
                                            </div>
                                            <div class="checkbox checkbox-inline checkbox-info  col-md-2 m-b-10">
                                                <input id="open_sun" name="office_open_days[]" value="0"
                                                       @if($openDays)
                                                           @foreach($openDays as $day)
                                                               @if($day == 0) checked @endif
                                                       @endforeach
                                                       @endif
                                                       type="checkbox">
                                                <label for="open_sun">@lang('app.sunday')</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <hr>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">@lang('app.attendanceReminderStatus')</label>
                                            <div class="switchery-demo">
                                                <input type="checkbox" name="alert_after_status"
                                                       @if($attendanceSetting->alert_after_status == 1) checked
                                                       @endif class="js-switch changeStatusSetting" data-color="#00c292"
                                                       data-secondary-color="#f96262"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="ReminderAfterMinutesBox">
                                        <div class="form-group">
                                            <label for="late_mark_duration">@lang('app.ReminderAfterMinutes')</label>
                                            <input type="number" class="form-control" id="ReminderAfterMinutes"
                                                   name="alert_after"
                                                   value="{{ $attendanceSetting->alert_after }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form-actions m-t-15">
                                            <button type="submit" id="save-form"
                                                    class="btn btn-success waves-effect waves-light m-r-10">
                                                @lang('app.update')
                                            </button>

                                        </div>

                                    </div>

                                </div>


                            </div>
                            {!! Form::close() !!}

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
    <!-- .row -->

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>

    <script>
        function checkmyinput () {
            // Get inputfield
            var inputfield = document.getElementById("clockin_in_day");

            // Get value from inputfield
            var inputval = inputfield.value;

            // Remove non numeric input
            var numeric = inputval.replace(/[^0-9]+/,"");

            // Check if input is numeric and even, if not empty field
            if (numeric.length != inputval.length || numeric%2 != 0) {
                inputfield.value = '';
            }
        }
        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function () {
            new Switchery($(this)[0], $(this).data());

        });
        var $insertBefore = $('#insertBefore');
        var $i = {{ count($ipAddresses) }};
        $('#office_end_time, #office_start_time, #halfday_mark_time').timepicker({
            @if($global->time_format == 'H:i')
            showMeridian: false
            @endif
        });
        @if($attendanceSetting->alert_after_status == 1)
        $('#ReminderAfterMinutesBox').show();
        @else
        $('#ReminderAfterMinutesBox').hide();
        @endif

        $('#save-form').click(function () {
            var totaltextfield = $('.totaltextfield').val();
            var clockin_in_day=$('#clockin_in_day').val();

            if(clockin_in_day< totaltextfield){
                alert('Clock in  Days is less Than your reason');
                return;
            }


            $.easyAjax({
                url: '{{route('admin.attendance-settings.update', ['1'])}}',
                container: '#editSettings',
                type: "POST",
                redirect: true,
                data: $('#editSettings').serialize()
            })
        });

        $('.changeStatusSetting').change(function () {
            if ($(this).is(':checked'))
                $('#ReminderAfterMinutesBox').show();
            else
                $('#ReminderAfterMinutesBox').hide();

        });

        $('#radius_check').click(function () {
            if ($(this).prop("checked") == true) {
                $('#radiusBox').attr("style", "display: block !important");
            } else if ($(this).prop("checked") == false) {
                $('#radiusBox').attr("style", "display: none !important");
            }
        });
        $('#ip_check').click(function () {
            if ($(this).prop("checked") == true) {
                $('#ipBox').attr("style", "display: block !important");
            } else if ($(this).prop("checked") == false) {
                $('#ipBox').attr("style", "display: none !important");
            }
        });
        // Add More Inputs
        $('#plusButton').click(function () {

            $i = $i + 1;
            var indexs = $i + 1;
            $(' <div id="addMoreBox' + indexs + '" class="clearfix"> ' +
                '<div class="col-md-5 "style="margin-left:5px;"><div class="form-group"><input class="form-control " name="ip[' + $i + ']" type="text" value="" placeholder="@lang('modules.attendance.ipAddress')"/></div></div>' +
                '<div class="col-md-1"><button type="button" onclick="removeBox(' + indexs + ')" class="btn btn-sm btn-danger"><i class="fa fa-times"></i></button></div>' +
                '</div>').insertBefore($insertBefore);

        });

        // Remove fields
        function removeBox(index) {
            $('#addMoreBox' + index).remove();
        }

    </script>

@endpush

