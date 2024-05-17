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
        function check($a,$b,$total_db,$wrapper_count){
            $value=$a-$total_db;
           
            if($total_db>0){
               console.log($total_db);
              if($value>$wrapper_count){
                  return true;
               }
            }else{
                
             if($a>$b){
                return true;
              }  
            }
            // console.log($a,$b,$total_db,$wrapper_count);
            // if($total_db>$wrapper_count){
            //    return true;
            // }
            // if($a>$b){
            //     return true;
            // }else if($total_db>$wrapper_count){
            //    return true;
            // }
            
        }
        $( document ).ready(function() {
           var total = $(".nodiv").length;

          
       $('.totaltextfield').val(parseInt(2+total*2));
       } );
        function add_field() {
                
            var total = $(".nodiv").length;
           
       var totaltextfield = $('.totaltextfield').val();
       var clockin_in_day=$('#clockin_in_day').val();
      
       clockin_in_day=clockin_in_day-2;
      
    
       var total_db=$('.total_db').val();
       var wrapper_count=$("#field_div").find(".input_text").length;

       var cal_clockin_in_day=clockin_in_day/2;
      
       
     
        // (clockin_in_day>total_db && total_db==0) ||
       //  && (clockin_in_day>total_db && total_db==0)
    //    console.log(clockin_in_day+"-"+total_db);
   

      console.log("db_val",total_db,);
      if( check(clockin_in_day,wrapper_count,total_db,wrapper_count) ){
         
        var total_text = document.getElementsByClassName("input_text");
            total_text = total_text.length + 2;   
             $('.totaltextfield').val(total_text+2+parseInt(total*2));
            
            document.getElementById("field_div").innerHTML = document.getElementById("field_div").innerHTML +
                "<p id='input_text" + total_text + "_wrapper' class='wrapper-class'> <select class='form-control col-md-4' id='exampleFormControlSelect1' name='type[]'><option value='1'>Paid</option><option value='0'>Unpaid</option></select> <input type='text' class='input_text' id='input_text" + total_text + "' placeholder='Enter Text' name='check_setup[]'><input type='text' class='input_text' id='input_text" + total_text + "' placeholder='Enter Text' name='check_setup[]'><input type='button' value='Remove' onclick=remove_field('input_text" + total_text + "');></p>";
        }else{
           
            alert("you can not assign more reasons");
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
                                    <!-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="late_mark_duration">@lang('modules.attendance.checkininday')</label>
                                            <input type="number" class="form-control" id="clockin_in_day"
                                                   name="clockin_in_day"  min="2" max="20" step="2"
                                                   value="{{ $attendanceSetting->clockin_in_day }}" onkeyup="checkmyinput()">
                                        </div>
                                    </div> -->
                                    
                                    <!-- <input type="hidden" class="totaltextfield" name="totaltextfield" value="20">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <div id="field_div">
                                                <input type="button" value="Add Check Setup" onclick="add_field();">
                                            </div>
                                        </div> -->

                                        <div class="col-md-12 nodiv">
                                     
                                     <div class="col-md-3 col-md-offset-9 " style="padding-top:4px">
                                         <input id="checkbox_all" class="checkbox-all" name="office_open_days" value="2" type="checkbox">
                                         <label for="open_tues">All</label>
                                     </div>
                                 </div>
                                  @php
                                 $first_start_value="";
                                 $first_end_value="";
                                 $second_start_value="";
                                 $second_end_value="";

                                 $third_start_value="";
                                 $third_end_value="";

                                 $fourth_start_value="";
                                 $fourth_end_value="";

                                 $fifth_start_value="";
                                 $fifth_end_value="";

                                 $sixth_start_value="";
                                 $sixth_end_value="";

                                 $seventh_start_value="";
                                 $seventh_end_value="";

                                 $eight_start_value="";
                                 $eight_end_value="";

                                 $nineth_start_value="";
                                 $nineth_end_value="";


                                 if($attendanceSetup->count() > 0){
                                    $first_start_value = $attendanceSetup->where('reasons_value',1);
                                    if($first_start_value->count()>0){
                                        $first_start_value=$first_start_value->first()->name;
                                    }else{
                                        $first_start_value="";
                                    }
                                    $first_end_value = $attendanceSetup->where('reasons_value',2);
                                    if($first_end_value->count()>0){
                                        $first_end_value=$first_end_value->first()->name;
                                    }else{
                                        $first_end_value="";
                                    }
                                    $second_start_value = $attendanceSetup->where('reasons_value',3);
                                    if($second_start_value->count()>0){
                                        $second_start_value=$second_start_value->first()->name;
                                    }else{
                                        $second_start_value="";
                                    }
                                    $second_end_value = $attendanceSetup->where('reasons_value',4);
                                    if($second_end_value->count()>0){
                                        $second_end_value=$second_end_value->first()->name;
                                    }else{
                                        $second_end_value="";
                                    }

                                    $third_start_value = $attendanceSetup->where('reasons_value',5);
                                    if($third_start_value->count()>0){
                                        $third_start_value=$third_start_value->first()->name;
                                    }else{
                                        $third_start_value="";
                                    }

                                    $third_end_value = $attendanceSetup->where('reasons_value',6);
                                    if($third_end_value->count()>0){
                                        $third_end_value=$third_end_value->first()->name;
                                    }else{
                                        $third_end_value="";
                                    }

                                    $fourth_start_value = $attendanceSetup->where('reasons_value',7);
                                    if($fourth_start_value->count()>0){
                                        $fourth_start_value=$fourth_start_value->first()->name;
                                    }else{
                                        $fourth_start_value="";
                                    }

                                    $fourth_end_value = $attendanceSetup->where('reasons_value',8);
                                    if($fourth_end_value->count()>0){
                                        $fourth_end_value=$fourth_end_value->first()->name;
                                    }else{
                                        $fourth_end_value="";
                                    }

                                    $fifth_start_value = $attendanceSetup->where('reasons_value',9);
                                    if($fifth_start_value->count()>0){
                                        $fifth_start_value=$fifth_start_value->first()->name;
                                    }else{
                                        $fifth_start_value="";
                                    }

                                    $fifth_end_value = $attendanceSetup->where('reasons_value',10);
                                    if($fifth_end_value->count()>0){
                                        $fifth_end_value=$fifth_end_value->first()->name;
                                    }else{
                                        $fifth_end_value="";
                                    }

                                    $sixth_start_value = $attendanceSetup->where('reasons_value',11);
                                    if($sixth_start_value->count()>0){
                                        $sixth_start_value=$sixth_start_value->first()->name;
                                    }else{
                                        $sixth_start_value="";
                                    }

                                    $sixth_end_value = $attendanceSetup->where('reasons_value',12);
                                    if($sixth_end_value->count()>0){
                                        $sixth_end_value=$sixth_end_value->first()->name;
                                    }else{
                                        $sixth_end_value="";
                                    }

                                    $seventh_start_value = $attendanceSetup->where('reasons_value',13);
                                    if($seventh_start_value->count()>0){
                                        $seventh_start_value=$seventh_start_value->first()->name;
                                    }else{
                                        $seventh_start_value="";
                                    }

                                    $seventh_end_value = $attendanceSetup->where('reasons_value',14);
                                    if($seventh_end_value->count()>0){
                                        $seventh_end_value=$seventh_end_value->first()->name;
                                    }else{
                                        $seventh_end_value="";
                                    }

                                    $eight_start_value = $attendanceSetup->where('reasons_value',15);
                                    if($eight_start_value->count()>0){
                                        $eight_start_value=$eight_start_value->first()->name;
                                    }else{
                                        $eight_start_value="";
                                    }

                                    $eight_end_value = $attendanceSetup->where('reasons_value',16);
                                    if($eight_end_value->count()>0){
                                        $eight_end_value=$eight_end_value->first()->name;
                                    }else{
                                        $eight_end_value="";
                                    }

                                    $nineth_start_value = $attendanceSetup->where('reasons_value',17);
                                    if($nineth_start_value->count()>0){
                                        $nineth_start_value=$nineth_start_value->first()->name;
                                    }else{
                                        $nineth_start_value="";
                                    }

                                    $nineth_end_value = $attendanceSetup->where('reasons_value',18);
                                    if($nineth_end_value->count()>0){
                                        $nineth_end_value=$nineth_end_value->first()->name;
                                    }else{
                                        $nineth_end_value="";
                                    }

                                 }
                                  
                                 
                                 @endphp

                             
                                
                                
                                <div class="col-md-12 nodiv" id="divOne">
                                        
                                        <div class="col-md-3">
                                        <input type="hidden"  <?php echo (($first_start_value!="") && ($first_end_value!="") ) ? '':'disabled'  ?> value="1"  name="reason_value[]"/>
                                        <input type="hidden" <?php echo (($first_start_value!="") && ($first_end_value!="") ) ? '':'disabled'  ?>  value="2"  name="reason_value[]"/>
                                        <select class="form-control" id="exampleFormControlSelect1"
                                        name="type[]">
                                        <option value="1">Paid
                                        </option>
                                        <option value="0">
                                        UnPaid
                                        </option>
                                        </select>
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" <?php echo (($first_start_value!="") && ($first_end_value!="") ) ? '':'disabled'  ?> class="form-control" 
                                        placeholder="start-1"
                                        value="{{$first_start_value}}"
                                        name="check_setup[]"
                                       >
                                        </div>
                                        <div class="col-md-3">
                                        <input 
                                        <?php echo (($first_start_value!="") && ($first_end_value!="") ) ? '':'disabled'  ?>
            
                                        type="text" 
                                        placeholder="end-1"
                                        value="{{$first_end_value}}"
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        
                                        <div class="col-md-3" style="padding-top:4px">
                                            
                                            <input id="checkbox_one" <?php echo (($first_start_value!="") && ($first_end_value!="") ) ? 'checked':''  ?> class="checkbox-one" name="office_open_days" value="2" type="checkbox">
                                            <label for="open_tues"></label>

                                        </div>
                                    </div>
                                    <div class="col-md-12 nodiv" id="divTwo" style="margin-top:2px">
                                        <div class="col-md-3">
                                        <input type="hidden"  <?php echo (($second_start_value!="") && ($second_end_value!="") ) ? '':'disabled'  ?> value="3"  name="reason_value[]"/>
                                        <input type="hidden"  <?php echo (($second_start_value!="") && ($second_end_value!="") ) ? '':'disabled'  ?> value="4"  name="reason_value[]"/>
                                        <select class="form-control" id="exampleFormControlSelect1"
                                        name="type[]">
                                        <option value="1">Paid
                                        </option>
                                        <option value="0">
                                        UnPaid
                                        </option>
                                        </select>
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        
                                        <?php echo (($second_start_value!="") && ($second_end_value!="") ) ? '':'disabled'  ?>
                                        value="{{$second_start_value}}"
                                        placeholder="start-2"
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        
                                        <?php echo (($second_start_value!="") && ($second_end_value!="") ) ? '':'disabled'  ?>
                                        value="{{$second_end_value}}"
                                        placeholder="end-2"
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        
                                            <div class="col-md-3" style="padding-top:4px">
                                            <input <?php echo (($second_start_value!="") && ($second_end_value!="") ) ? 'checked':''  ?>  id="checkbox_two" name="office_open_days" value="2" type="checkbox">
                                            <label for="open_tues"></label>
                                            </div>
                                    </div> 
                                    <div class="col-md-12 nodiv" id="divThree" style="margin-top:2px">
                                        <div class="col-md-3">
                                        <input type="hidden"   <?php echo (($third_start_value!="") && ($third_end_value!="") ) ? '':'disabled'  ?> value="5"  name="reason_value[]"/>
                                        <input type="hidden"   <?php echo (($third_start_value!="") && ($third_end_value!="") ) ? '':'disabled'  ?>  value="6"  name="reason_value[]"/>
                                        <select class="form-control" id="exampleFormControlSelect1"
                                        name="type[]">
                                        <option value="1">Paid
                                        </option>
                                        <option value="0">
                                        UnPaid
                                        </option>
                                        </select>
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        <?php echo (($third_start_value!="") && ($third_end_value!="") ) ? '':'disabled'  ?>
                                        value="{{$third_start_value}}"
                                        placeholder="start-3"
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        <?php echo (($third_start_value!="") && ($third_end_value!="") ) ? '':'disabled'  ?>
                                        value="{{$third_end_value}}"
                                        placeholder="end-3"
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        
                                        <div class="col-md-3" style="padding-top:4px">

                                        <input  <?php echo (($third_start_value!="") && ($third_end_value!="") ) ? 'checked':''  ?> id="checkbox_three" name="office_open_days" value="2" type="checkbox">
                                        <label for="open_tues"></label>

                                        </div>
                                    </div> 

                                    <div class="col-md-12 nodiv" id="divFour" style="margin-top:2px">
                                        <div class="col-md-3">
                                        <input type="hidden"    <?php echo (($fourth_start_value!="") && ($fourth_end_value!="") ) ? '':'disabled'  ?> value="7"  name="reason_value[]"/>
                                        <input type="hidden"  <?php echo (($fourth_start_value!="") && ($fourth_end_value!="") ) ? '':'disabled'  ?>  value="8"  name="reason_value[]"/>
                                        <select class="form-control" id="exampleFormControlSelect1"
                                        name="type[]">
                                        <option value="1">Paid
                                        </option>
                                        <option value="0">
                                        UnPaid
                                        </option>
                                        </select>
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        <?php echo (($fourth_start_value!="") && ($fourth_end_value!="") ) ? '':'disabled'  ?>
                                        value="{{$fourth_start_value}}"
                                        placeholder="start-4"
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        <?php echo (($fourth_start_value!="") && ($fourth_end_value!="") ) ? '':'disabled'  ?>
                                        value="{{$fourth_end_value}}"
                                        placeholder="end-4"
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        
                                        <div class="col-md-3" style="padding-top:4px">

                                        <input <?php echo (($fourth_start_value!="") && ($fourth_end_value!="") ) ? 'checked':''  ?> id="checkbox_four" name="office_open_days" value="2" type="checkbox">
                                        <label for="open_tues"></label>

                                        </div>
                                    </div> 

                                    <div class="col-md-12 nodiv" id="divFive" style="margin-top:2px">
                                        <div class="col-md-3">
                                        <input type="hidden" <?php echo (($fifth_start_value!="") && ($fifth_end_value!="") ) ? '':'disabled'  ?> value="9"  name="reason_value[]"/>
                                        <input type="hidden"  <?php echo (($fifth_start_value!="") && ($fifth_end_value!="") ) ? '':'disabled'  ?> value="10"  name="reason_value[]"/>
                                        <select class="form-control" id="exampleFormControlSelect1"
                                        name="type[]">
                                        <option value="1">Paid
                                        </option>
                                        <option value="0">
                                        UnPaid
                                        </option>
                                        </select>
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                    
                                        <?php echo (($fifth_start_value!="") && ($fifth_end_value!="") ) ? '':'disabled'  ?>
                                        value="{{$fifth_start_value}}"
                                        placeholder="start-5"
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        
                                        <?php echo (($fifth_start_value!="") && ($fifth_end_value!="") ) ? '':'disabled'  ?>
                                        value="{{$fifth_end_value}}"
                                        placeholder="end-5"
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        
                                            <div class="col-md-3" style="padding-top:4px">

                                            <input <?php echo (($fifth_start_value!="") && ($fifth_end_value!="") ) ? 'checked':''  ?> id="checkbox_five" name="office_open_days" value="2" type="checkbox">
                                            <label for="open_tues"></label>

                                            </div>
                                    </div>
                                    
                                    <div class="col-md-12 nodiv" id="divSix" style="margin-top:2px">
                                        <div class="col-md-3">
                                        <input type="hidden"  <?php echo (($sixth_start_value!="") && ($sixth_end_value!="") ) ? '':'disabled'  ?> value="11"  name="reason_value[]"/>
                                        <input type="hidden"   <?php echo (($sixth_start_value!="") && ($sixth_end_value!="") ) ? '':'disabled'  ?> value="12"  name="reason_value[]"/>
                                        <select class="form-control" id="exampleFormControlSelect1"
                                        name="type[]">
                                        <option value="1">Paid
                                        </option>
                                        <option value="0">
                                        UnPaid
                                        </option>
                                        </select>
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        <?php echo (($sixth_start_value!="") && ($sixth_end_value!="") ) ? '':'disabled'  ?>
                                        value="{{$sixth_start_value}}"
                                        placeholder="start-6"
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        <?php echo (($sixth_start_value!="") && ($sixth_end_value!="") ) ? '':'disabled'  ?>
                                        value="{{$sixth_end_value}}"
                                        placeholder="end-6"
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        
                                            <div class="col-md-3" style="padding-top:4px">

                                            <input <?php echo (($sixth_start_value!="") && ($sixth_end_value!="") ) ? 'checked':''  ?> id="checkbox_six" name="office_open_days" value="2" type="checkbox">
                                            <label for="open_tues"></label>

                                            </div>
                                    </div>

                                    <div class="col-md-12 nodiv" id="divSeven" style="margin-top:2px">
                                        <div class="col-md-3">
                                        <input type="hidden" <?php echo (($seventh_start_value!="") && ($seventh_end_value!="") ) ? '':'disabled'  ?> value="13"  name="reason_value[]"/>
                                        <input type="hidden" <?php echo (($seventh_start_value!="") && ($seventh_end_value!="") ) ? '':'disabled'  ?>  value="14"  name="reason_value[]"/>
                                        <select class="form-control" id="exampleFormControlSelect1"
                                        name="type[]">
                                        <option value="1">Paid
                                        </option>
                                        <option value="0">
                                        UnPaid
                                        </option>
                                        </select>
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        <?php echo (($seventh_start_value!="") && ($seventh_end_value!="") ) ? '':'disabled'  ?>
                                        placeholder="start-7"
                                        value="{{$seventh_start_value}}"
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        <?php echo (($seventh_start_value!="") && ($seventh_end_value!="") ) ? '':'disabled'  ?>
                                        value="{{$seventh_end_value}}"
                                        placeholder="end-7"
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        
                                            <div class="col-md-3" style="padding-top:4px">

                                            <input <?php echo (($seventh_start_value!="") && ($seventh_end_value!="") ) ? 'checked':''  ?> id="checkbox_seven" name="office_open_days" value="2" type="checkbox">
                                            <label for="open_tues"></label>

                                            </div>
                                        </div>
                                        <div class="col-md-12 nodiv" id="divEight" style="margin-top:2px">
                                        <div class="col-md-3">
                                        <input type="hidden"   <?php echo (($eight_start_value!="") && ($eight_end_value!="") ) ? '':'disabled'  ?> value="15"  name="reason_value[]"/>
                                        <input type="hidden"   <?php echo (($eight_start_value!="") && ($eight_end_value!="") ) ? '':'disabled'  ?>  value="16"  name="reason_value[]"/>
                                        <select class="form-control" id="exampleFormControlSelect1"
                                        name="type[]">
                                        <option value="1">Paid
                                        </option>
                                        <option value="0">
                                        UnPaid
                                        </option>
                                        </select>
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        value="{{$eight_start_value}}"
                                        placeholder="start-8"
                                        <?php echo (($eight_start_value!="") && ($eight_end_value!="") ) ? '':'disabled'  ?>
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        value="{{$eight_end_value}}"
                                        placeholder="end-8"
                    
                                        <?php echo (($eight_start_value!="") && ($eight_end_value!="") ) ? '':'disabled'  ?>
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        
                                        <div class="col-md-3" style="padding-top:4px">
                                    
                                                <input  <?php echo (($eight_start_value!="") && ($eight_end_value!="") ) ? 'checked':''  ?> id="checkbox_eight" name="office_open_days" value="2" type="checkbox">
                                                <label for="open_tues"></label>
             
                                        </div>
                                    </div>

                                    <div class="col-md-12 nodiv" id="divNine" style="margin-top:2px">
                                        <div class="col-md-3">
                                        <input type="hidden" <?php echo (($nineth_start_value!="") && ($nineth_start_value!="") ) ? '':'disabled'  ?> value="17"  name="reason_value[]"/>
                                        <input type="hidden" <?php echo (($nineth_start_value!="") && ($nineth_start_value!="") ) ? '':'disabled'  ?>  value="18"  name="reason_value[]"/>
                                        <select class="form-control" id="exampleFormControlSelect1"
                                        name="type[]">
                                        <option value="1">Paid
                                        </option>
                                        <option value="0">
                                        UnPaid
                                        </option>
                                        </select>
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        <?php echo (($nineth_start_value!="") && ($nineth_start_value!="") ) ? '':'disabled'  ?>
                                        placeholder="start-9"
                                        class="form-control" 
                                        value="{{$nineth_start_value}}"
                                        name="check_setup[]"
                                       >
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" 
                                        placeholder="end-9"
                                        value="{{$nineth_end_value}}"
                                        <?php echo (($nineth_start_value!="") && ($nineth_start_value!="") ) ? '':'disabled'  ?>
                                        class="form-control" 
                                        name="check_setup[]"
                                       >
                                        </div>
                                        
                                        <div class="col-md-3" style="padding-top:4px">
                                    
                                                <input  <?php echo (($nineth_start_value!="") && ($nineth_start_value!="") ) ? 'checked':''  ?> id="checkbox_nine" name="office_open_days" value="2" type="checkbox">
                                                <label for="open_tues"></label>
             
                                        </div>
                                    </div>
                                  
                                    </div>
                                    

                                  




                                      {{--   
                                            <?php
                                            $array=[];
                                           if($attendanceSetup->count()>0){
                                            foreach ($attendanceSetup as $datas) {
                                   
                                                @$array[] = @$datas->name;
                                                @$arrayid[] = @$datas->order_by;
                                                @$array1[] = @$datas->status;
                                                
                                            }
                                        }
                                           
                                      
                                            $incprev=0;
                                            $countext=0;
                                          
                                           
                                            ?>
                                       
                         <input type="hidden" name="" value="{{ count($array) }}" class="total_db">




@if(!empty($array))                                     
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
                                                               name="check_setup1[]"
                                                               value="{{ @$array[$key+1] }}">
                                                    </div>
                                                    @else
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" id="input_text-{{@$arrayid[$key]}}"
                                                               name="check_setup1[]"
                                                               value="{{ @$array[$key] }}">
                                                    </div>
                                                    @endif
                                                    @if($key+1==$incprev)
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" id="input_text-{{@$arrayid[$key]}}"
                                                               name="check_setup1[]"
                                                               value="{{ @$array[$key+2] }}">
                                                    </div>
                                                    @else
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" id="input_text-{{@$arrayid[$key]}}"
                                                               name="check_setup1[]"
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
                                            @endif
                                            --}}
                                           

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

$(document).ready(function(){

            
$("#checkbox_all").change(function() {
            if($(this).prop('checked')) {
                $("#divOne :input[type='text']").attr("disabled", false);
                $("#divOne :input[type='hidden']").attr("disabled", false);
               
                $("#checkbox_one").prop('checked', true);
               

                $("#divTwo :input[type='text']").attr("disabled", false);
                $("#divTwo :input[type='hidden']").attr("disabled", false);
                $("#checkbox_two").prop('checked', true);


                $("#divThree :input[type='text']").attr("disabled", false);
                $("#divThree :input[type='hidden']").attr("disabled", false);
                $("#checkbox_three").prop('checked', true);

                $("#divFour :input[type='text']").attr("disabled", false);
                $("#divFour :input[type='hidden']").attr("disabled", false);
                $("#checkbox_four").prop('checked', true);

                $("#divFive :input[type='text']").attr("disabled", false);
                $("#divFive :input[type='hidden']").attr("disabled", false);
                $("#checkbox_five").prop('checked', true);

                $("#divSix :input[type='text']").attr("disabled", false);
                $("#divSix :input[type='hidden']").attr("disabled", false);
                $("#checkbox_six").prop('checked', true);

                $("#divSeven :input[type='text']").attr("disabled", false);
                $("#divSeven :input[type='hidden']").attr("disabled", false);
                $("#checkbox_seven").prop('checked', true);


                $("#divEight :input[type='text']").attr("disabled", false);
                $("#divEight :input[type='hidden']").attr("disabled", false);
                $("#checkbox_eight").prop('checked', true);

                $("#divNine :input[type='text']").attr("disabled", false);
                $("#divNine :input[type='hidden']").attr("disabled", false);
                $("#checkbox_nine").prop('checked', true);

              
            } else {
                $("#divOne :input[type='text']").attr("disabled", true);
                $("#divOne :input[type='hidden']").attr("disabled", true);
                $("#checkbox_one").prop('checked', false);
               
               


                $("#divTwo :input[type='text']").attr("disabled", true);
                $("#divTwo :input[type='hidden']").attr("disabled", true);
                $("#checkbox_two").prop('checked', false);

                $("#divThree :input[type='text']").attr("disabled", true);
                $("#divThree :input[type='hidden']").attr("disabled", true);
                $("#checkbox_three").prop('checked', false);

                $("#divFour :input[type='text']").attr("disabled", true);
                $("#divFour :input[type='hidden']").attr("disabled", true);
                $("#checkbox_four").prop('checked', false);

                $("#divFive :input[type='text']").attr("disabled", true);
                $("#divFive :input[type='hidden']").attr("disabled", true);
                $("#checkbox_five").prop('checked', false)

                $("#divSix :input[type='text']").attr("disabled", true);
                $("#divSix :input[type='hidden']").attr("disabled", true);
                $("#checkbox_six").prop('checked', false);

                $("#divSeven :input[type='text']").attr("disabled", true);
                $("#divSeven :input[type='hidden']").attr("disabled", true);
                $("#checkbox_seven").prop('checked', false);

                $("#divEight :input[type='text']").attr("disabled", true);
                $("#divEight :input[type='hidden']").attr("disabled", true);
                $("#checkbox_eight").prop('checked', false);

                $("#divNine :input[type='text']").attr("disabled", true);
                $("#divNine :input[type='hidden']").attr("disabled", true);
                $("#checkbox_nine").prop('checked', false);

                


            }
    });
    $("#checkbox_one").change(function() {
            if($(this).prop('checked')) {
                $("#divOne :input[type='text']").attr("disabled", false);
                $("#divOne :input[type='hidden']").attr("disabled", false);
               
            } else {
                $("#divOne :input[type='text']").attr("disabled", true);
                $("#divOne :input[type='hidden']").attr("disabled", true);
            }
    });
    $("#checkbox_two").change(function() {
            if($(this).prop('checked')) {
                $("#divTwo :input[type='text']").attr("disabled", false);
                $("#divTwo :input[type='hidden']").attr("disabled", false);

            } else {
                $("#divTwo :input[type='text']").attr("disabled", true);
                $("#divTwo :input[type='hidden']").attr("disabled", true);
            }
    });
    $("#checkbox_three").change(function() {
            if($(this).prop('checked')) {
                $("#divThree :input[type='text']").attr("disabled", false);
                $("#divThree :input[type='hidden']").attr("disabled", false);
            } else {
                $("#divThree :input[type='text']").attr("disabled", true);
                $("#divThree :input[type='hidden']").attr("disabled", true);
            }
    });
    $("#checkbox_four").change(function() {
            if($(this).prop('checked')) {
                $("#divFour :input[type='text']").attr("disabled", false);
                $("#divFour :input[type='hidden']").attr("disabled", false);
            } else {
                $("#divFour :input[type='text']").attr("disabled", true);
                $("#divFour :input[type='hidden']").attr("disabled", true);
            }
    });
    $("#checkbox_five").change(function() {
            if($(this).prop('checked')) {
                $("#divFive :input[type='text']").attr("disabled", false);
                $("#divFive :input[type='hidden']").attr("disabled", false);
            } else {
                $("#divFive :input[type='text']").attr("disabled", true);
                $("#divFive :input[type='hidden']").attr("disabled", true);
            }
    });
    $("#checkbox_six").change(function() {
            if($(this).prop('checked')) {
                $("#divSix :input[type='text']").attr("disabled", false);
                $("#divSix :input[type='hidden']").attr("disabled", false);
            } else {
                $("#divSix :input[type='text']").attr("disabled", true);
                $("#divSix :input[type='hidden']").attr("disabled", true);
            }
    });
    $("#checkbox_seven").change(function() {
            if($(this).prop('checked')) {
                $("#divSeven :input[type='text']").attr("disabled", false);
                $("#divSeven :input[type='hidden']").attr("disabled", false);
            } else {
                $("#divSeven :input[type='text']").attr("disabled", true);
                $("#divSeven :input[type='hidden']").attr("disabled", true);
            }
    });
    $("#checkbox_eight").change(function() {
        
            if($(this).prop('checked')) {
                $("#divEight :input[type='text']").attr("disabled", false);
                $("#divEight :input[type='hidden']").attr("disabled", false);
            } else {
                $("#divEight :input[type='text']").attr("disabled", true);
                $("#divEight :input[type='hidden']").attr("disabled", true);
            }
    });
    $("#checkbox_nine").change(function() {
            if($(this).prop('checked')) {
                $("#divNine :input[type='text']").attr("disabled", false);
                $("#divNine :input[type='hidden']").attr("disabled", false);
            } else {
                $("#divNine :input[type='text']").attr("disabled", true);
                $("#divNine :input[type='hidden']").attr("disabled", true);
            }
    });
});   

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
         
         

            // if(clockin_in_day< totaltextfield){
            //     alert('Clock in  Days is less Than your reason');
            //     return;
            // }


            $.easyAjax({
                url: '{{route('admin.attendance-settings.update', ['1'])}}',
                container: '#editSettings',
                type: "POST",
                redirect: true,
                data: $('#editSettings').serialize(),
                success: function (response) {
                    console.log("acdd",response);
               
              }
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

