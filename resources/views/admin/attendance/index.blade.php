@extends('layouts.app')
<style>
.scroll-effect {
    overflow: auto;
}
.drp-calendar .left{
      overflow: auto;
}
.drp-calendar .right{
      overflow: auto;
}
</style>
@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <div class="col-sm-9 text-right">
            <div class="form-group">
                <a href="{{ route('admin.attendances.create') }}"
                   class="btn btn-success btn-sm">@lang('modules.attendance.markAttendance') <i class="fa fa-plus"
                                                                                                aria-hidden="true"></i></a>
            </div>
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
<link rel="stylesheet" href="{{ asset('plugins/bower_components/morrisjs/morris.css') }}">

<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterange-picker/daterangepicker.css') }}" />

@endpush

@section('content')
    <div class="row">

 
        <div class="sttabs tabs-style-line col-md-12">
            <div class="white-box">
                <nav>
                    <ul>
                        <li><a href="{{ route('admin.attendances.summary') }}"><span>@lang('app.summary')</span></a>

                        <li class="tab-current"><a href="{{ route('admin.attendances.index') }}"><span>@lang('modules.attendance.attendanceByMember')</span></a>
                        </li>
                        <li><a href="{{ route('admin.attendances.attendanceByDate') }}"><span>@lang('modules.attendance.attendanceByDate')</span></a>
                        </li>

                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!-- .row -->

    <div class="row">
        <div class="col-xs-12">
            <div class="white-box p-b-0">
                <div class="row">
                    <div class="col-md-3">
                        <label class="control-label">@lang('app.selectDateRange')</label>

                        <div class="form-group">
                            <div id="reportrange" class="form-control reportrange">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down pull-right"></i>
                            </div>
                          

                            <input type="hidden" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                                   value="{{ $startDate->format($global->date_format) }}"/>
                            <input type="hidden" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                                   value="{{ $endDate->format($global->date_format) }}"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">@lang('app.department')</label>
                            <select class="form-control select2 department_id" name="department" id="department" data-style="form-control">
                                <option value="all">@lang('modules.client.all')</option>
                                @forelse($departments as $department)
                                    <option value="{{$department->id}}">{{ ucfirst($department->team_name) }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">@lang('modules.timeLogs.employeeName')</label>
                            <select class="select2 form-control user_id" data-placeholder="Choose Employee" id="user_id" name="user_id">
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group m-t-20">
                            <button type="button" id="apply-filter" class="btn btn-success btn-block">@lang('app.apply')</button>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div class="col-xs-12">
            <div class="row dashboard-stats">
                <div class="col-md-12 m-b-30">
                    <div class="white-box">
                        <div class="col-md-2  col-sm-4 text-center">
                            <h4><span class="text-dark" id="totalWorkingDays">{{ $totalWorkingDays }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.attendance.totalWorkingDays')</span></h4>
                        </div>
                        <div class="col-md-2 b-l  col-sm-4 text-center">
                            <h4><span class="text-success" id="daysPresent">{{ $daysPresent }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.attendance.daysPresent')</span></h4>
                        </div>
                        <div class="col-md-2 b-l  col-sm-4 text-center">
                            <h4><span class="text-danger" id="daysLate">{{ $daysLate }}</span> <span class="font-12 text-muted m-l-5"> @lang('app.days') @lang('modules.attendance.late')</span></h4>
                        </div>
                        <div class="col-md-2 b-l  col-sm-4 text-center">
                            <h4><span class="text-warning" id="halfDays">{{ $halfDays }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.attendance.halfDay')</span></h4>
                        </div>
                        <div class="col-md-2 b-l  col-sm-4 text-center">
                            <h4><span class="text-info" id="absentDays">{{ (($totalWorkingDays - $daysPresent) < 0) ? '0' : ($totalWorkingDays - $daysPresent) }}</span> <span class="font-12 text-muted m-l-5"> @lang('app.days') @lang('modules.attendance.absent')</span></h4>
                        </div>
                        <div class="col-md-2 b-l  col-sm-4 text-center">
                            <h4><span class="text-primary" id="holidayDays">{{ $holidays }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.attendance.holidays')</span></h4>
                        </div>

                        
                    </div>
                </div>
@php
// dd($startDate); href="{{ route('admin.attendances.excelexport',['user_id'=>$userId,'startDate'=>$startDate,'endDate'=>$endDate]) }}"  
@endphp
            </div>
            <div class="col-md-2 b-l  col-sm-4 text-center pull-right">
                <button class="btn btn-success btn-sm" onclick="exportData1()">Export <i class="fa fa-file" aria-hidden="true"></i></button>
                <!--<h4> <a class="font-12 text-muted m-l-5" onclick="exportData1()"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a></h4>-->
            </div>
        </div>
        <?php
          $attendanceSetup=\App\AttedanceSetup::orderBy('id','asc')->where('company_id',\Illuminate\Support\Facades\Auth::user()->company_id)->get();
          $first_start_value="Start-1";
          $first_end_value="End-1";
          $second_start_value="Start-2";
          $second_end_value="End-2";
  
          $third_start_value="Start-3";
          $third_end_value="End-3";
  
          $fourth_start_value="Start-4";
          $fourth_end_value="End-4";
  
          $fifth_start_value="Start-5";
          $fifth_end_value="End-5";
  
          $sixth_start_value="Start-6";
          $sixth_end_value="End-6";
  
          $seventh_start_value="Start-7";
          $seventh_end_value="End-7";
  
          $eight_start_value="Start-8";
          $eight_end_value="End-8";
  
          $nineth_start_value="Start-9";
          $nineth_end_value="End-9";
  
  
          if($attendanceSetup->count() > 0){
             $first_start_value = $attendanceSetup->where('reasons_value',1);
             if($first_start_value->count()>0){
                 $first_start_value=$first_start_value->first()->name;
             }else{
                 $first_start_value="Start-1";
             }
             $first_end_value = $attendanceSetup->where('reasons_value',2);
             if($first_end_value->count()>0){
                 $first_end_value=$first_end_value->first()->name;
             }else{
                 $first_end_value="End-1";
             }
             $second_start_value = $attendanceSetup->where('reasons_value',3);
             if($second_start_value->count()>0){
                 $second_start_value=$second_start_value->first()->name;
             }else{
                 $second_start_value="Start-2";
             }
             $second_end_value = $attendanceSetup->where('reasons_value',4);
             if($second_end_value->count()>0){
                 $second_end_value=$second_end_value->first()->name;
             }else{
                 $second_end_value="End-2";
             }
  
             $third_start_value = $attendanceSetup->where('reasons_value',5);
             if($third_start_value->count()>0){
                 $third_start_value=$third_start_value->first()->name;
             }else{
                 $third_start_value="Start-3";
             }
  
             $third_end_value = $attendanceSetup->where('reasons_value',6);
             if($third_end_value->count()>0){
                 $third_end_value=$third_end_value->first()->name;
             }else{
                 $third_end_value="End-3";
             }
  
             $fourth_start_value = $attendanceSetup->where('reasons_value',7);
             if($fourth_start_value->count()>0){
                 $fourth_start_value=$fourth_start_value->first()->name;
             }else{
                 $fourth_start_value="Start-4";
             }
  
             $fourth_end_value = $attendanceSetup->where('reasons_value',8);
             if($fourth_end_value->count()>0){
                 $fourth_end_value=$fourth_end_value->first()->name;
             }else{
                 $fourth_end_value="End-4";
             }
  
             $fifth_start_value = $attendanceSetup->where('reasons_value',9);
             if($fifth_start_value->count()>0){
                 $fifth_start_value=$fifth_start_value->first()->name;
             }else{
                 $fifth_start_value="Start-5";
             }
  
             $fifth_end_value = $attendanceSetup->where('reasons_value',10);
             if($fifth_end_value->count()>0){
                 $fifth_end_value=$fifth_end_value->first()->name;
             }else{
                 $fifth_end_value="End-5";
             }
  
             $sixth_start_value = $attendanceSetup->where('reasons_value',11);
             if($sixth_start_value->count()>0){
                 $sixth_start_value=$sixth_start_value->first()->name;
             }else{
                 $sixth_start_value="Start-6";
             }
  
             $sixth_end_value = $attendanceSetup->where('reasons_value',12);
             if($sixth_end_value->count()>0){
                 $sixth_end_value=$sixth_end_value->first()->name;
             }else{
                 $sixth_end_value="End-6";
             }
  
             $seventh_start_value = $attendanceSetup->where('reasons_value',13);
             if($seventh_start_value->count()>0){
                 $seventh_start_value=$seventh_start_value->first()->name;
             }else{
                 $seventh_start_value="Start-7";
             }
  
             $seventh_end_value = $attendanceSetup->where('reasons_value',14);
             if($seventh_end_value->count()>0){
                 $seventh_end_value=$seventh_end_value->first()->name;
             }else{
                 $seventh_end_value="End-7";
             }
  
             $eight_start_value = $attendanceSetup->where('reasons_value',15);
             if($eight_start_value->count()>0){
                 $eight_start_value=$eight_start_value->first()->name;
             }else{
                 $eight_start_value="Start-8";
             }
  
             $eight_end_value = $attendanceSetup->where('reasons_value',16);
             if($eight_end_value->count()>0){
                 $eight_end_value=$eight_end_value->first()->name;
             }else{
                 $eight_end_value="End-8";
             }
  
             $nineth_start_value = $attendanceSetup->where('reasons_value',17);
             if($nineth_start_value->count()>0){
                 $nineth_start_value=$nineth_start_value->first()->name;
             }else{
                 $nineth_start_value="Start-9";
             }
  
             $nineth_end_value = $attendanceSetup->where('reasons_value',18);
             if($nineth_end_value->count()>0){
                 $nineth_end_value=$nineth_end_value->first()->name;
             }else{
                 $nineth_end_value="End-9";
             }
  
          }
             
   
        ?>
        <div class="col-xs-12">
            <div class="white-box">
    
            <table class="table scroll-effect" style="display:block;max-width:100%;width:100%;overflow-x:auto">
                <thead>
                <tr>
                    <th>@lang('app.date')</th>
                    <th>@lang('app.status')</th>
                    <th>@lang('modules.attendance.clock_in')</th>
                   
                        @foreach ($attendanceSetup as $att)
                        <th>{{$att->name}}</th>
                        @endforeach
                {{--  
                       <th>{{$first_start_value}}</th> 
                        <th>{{$first_end_value}}</th> 
                        <th>{{$second_start_value}}</th>
                        <th>{{$second_end_value}}</th>
                        <th>{{$third_start_value}}</th>
                        <th>{{$third_end_value}}</th>
                        <th>{{$fourth_start_value}}</th>
                        <th>{{$fourth_end_value}}</th>
                        <th>{{$fifth_start_value}}</th>
                        <th>{{$fifth_end_value}}</th>
                        <th>{{$sixth_start_value}}</th>
                        <th>{{$sixth_end_value}}</th>
                        <th>{{$seventh_start_value}}</th>
                        <th>{{$seventh_end_value}}</th>
                        <th>{{$eight_start_value}}</th>
                        <th>{{$eight_end_value}}</th>
                        <th>{{$nineth_start_value}}</th>
                        <th>{{$nineth_end_value}}</th>
                        --}} 
                    <th>@lang('modules.attendance.clock_out')</th>

                    <th>@lang('app.others')</th>
                </tr>
                </thead>
                <tbody id="attendanceData">
                </tbody>
            </table>
            </div>

        </div>

    </div>


@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="{{ asset('plugins/bower_components/waypoints/lib/jquery.waypoints.js') }}"></script>
<script src="{{ asset('plugins/bower_components/counterup/jquery.counterup.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/daterange-picker/daterangepicker.js') }}"></script>
<script>
    var startDate = '{{ $startDate->format($global->date_format) }}';
    var endDate = '{{ $endDate->format($global->date_format) }}';
    var days= '{{ $days }}';
     var yesterday= '{{ $yesterDays }}';
    $(function() {
        var dateformat = '{{ $global->moment_format }}';
       
        // var startDate = '{{ $startDate->format($global->date_format) }}';
        var start = moment(startDate, dateformat);
      
        // var endDate = '{{ $endDate->format($global->date_format) }}';
        var end = moment(endDate, dateformat);
        function cb(start, end) {
            $('#start-date').val(start.format(dateformat));
            $('#end-date').val(end.format(dateformat));
            $('#reportrange span').html(start.format(dateformat) + ' - ' + end.format(dateformat));
        }
        moment.locale('{{ $global->locale }}');
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,

            locale: {
                language: '{{ $global->locale }}',
                format: '{{ $global->moment_format }}',
            },
            linkedCalendars: false,
            ranges: {
                // 'Today': [moment().add(days,'days'), moment().add(days,'days')],
                // 'Yesterday': [moment().subtract(yesterday, 'days'), moment().subtract(yesterday, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            
        }, cb);

        cb(start, end);

    });
    $('.input-daterange-datepicker').daterangepicker({
        buttonClasses: ['btn', 'btn-sm'],
        cancelClass: 'btn-inverse',
        "locale": {
            "applyLabel": "{{ __('app.apply') }}",
            "cancelLabel": "{{ __('app.cancel') }}",
            "daysOfWeek": [
                "{{ __('app.su') }}",
                "{{ __('app.mo') }}",
                "{{ __('app.tu') }}",
                "{{ __('app.we') }}",
                "{{ __('app.th') }}",
                "{{ __('app.fr') }}",
                "{{ __('app.sa') }}"
            ],
            "monthNames": [
                "{{ __('app.january') }}",
                "{{ __('app.february') }}",
                "{{ __('app.march') }}",
                "{{ __('app.april') }}",
                "{{ __('app.may') }}",
                "{{ __('app.june') }}",
                "{{ __('app.july') }}",
                "{{ __('app.august') }}",
                "{{ __('app.september') }}",
                "{{ __('app.october') }}",
                "{{ __('app.november') }}",
                "{{ __('app.december') }}",
            ],
            "firstDay": {{ $global->week_start }},
        }
    })
    $('#apply-filter').click(function () {
       showTable();
    });

    // $(".select2").select2({
    //     formatNoMatches: function () {
    //         return "{{ __('messages.noRecordFound') }}";
    //     }
    // });

    var table;

    function showTable() {

        var startDate = $('#start-date').val();
        var endDate = $('#end-date').val();

        var userId = $('#user_id').val();
        if (userId == "") {
            userId = 0;
        }

        //refresh counts
        var url = '{!!  route('admin.attendances.refreshCount', [':startDate', ':endDate', ':userId']) !!}';
        url = url.replace(':startDate', startDate);
        url = url.replace(':endDate', endDate);
        url = url.replace(':userId', userId);

        $.ajax({
            type: 'GET',
            url: url,
            success: function (response) {
                $('#daysPresent').html(response.daysPresent);
                $('#daysLate').html(response.daysLate);
                $('#halfDays').html(response.halfDays);
                $('#totalWorkingDays').html(response.totalWorkingDays);
                $('#absentDays').html(response.absentDays);
                $('#holidayDays').html(response.holidays);
                initConter();
            }
        });

        //refresh datatable
        var url2 = '{!!  route('admin.attendances.employeeData', [':startDate', ':endDate', ':userId']) !!}';

        url2 = url2.replace(':startDate', startDate);
        url2 = url2.replace(':endDate', endDate);
        url2 = url2.replace(':userId', userId);

        $.ajax({
            type: 'GET',
            url: url2,
            success: function (response) {
                $('#attendanceData').html(response.data);
            }
        });
    }

    $('#attendanceData').on('click', '.delete-attendance', function(){
        var id = $(this).data('attendance-id');
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.recoverAttendance')",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "@lang('messages.deleteConfirmation')",
            cancelButtonText: "@lang('messages.confirmNoArchive')",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('admin.attendances.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                            showTable();
                        }
                    }
                });
            }
        });
    });

    function initConter() {
        $(".counter").counterUp({
            delay: 100,
            time: 1200
        });
    }

    showTable();

    function exportData(){

        // var employee = $('#employee').val();
        // var status   = $('#status').val();
        // var role     = $('#role').val();

        var startDate = $('#start-date').val();
        var endDate = $('#end-date').val();
        var employee = $('#user_id').val();
        var url = '{{ route('admin.employees.export', ['employee' ,'startDate', 'endDate']) }}';
        // url = url.replace(':role', role);
        // url = url.replace(':status', status);
        // url = url.replace(':employee', employee);

        window.location.href = url;
    }

    function exportData1(){

        var startDate = $('#start-date').val();
        var endDate = $('#end-date').val();
        var employee = $('#user_id').val();
        
        var url = '{{ route('admin.attendances.export') }}';
        $.ajax({
            type: 'GET',
                    url: url,
                    xhrFields: {
                responseType: 'blob'
            },
                    data: { startDate: startDate,endDate: endDate,employee: employee },
        // $.get(url,{ startDate: startDate,endDate: endDate,employee: employee }, 
        
        success: function (response) {
           
           
    var request = new XMLHttpRequest();
            var blob = new Blob([response]);
            console.log(request);
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "Attendance_detail.xlsx";
                link.click();
        
         }  
         ,
            error: function(blob){
                console.log(blob);
            }
          
                    ,
          
                
        
                            });

        
    }
    function downloadFile(response) {
  var blob = new Blob([response], {type: 'application/Pdf'})
  var url = URL.createObjectURL(blob);
  window.open(url);
} 

    $('.department_id').on('change', function(){
        
        var department_id=$(this).val();
      
       
        $.get('attendances/filter/department',{ department_id: department_id }, function(data){
          
  
            console.log(data);
  var appenddata1 = "";
  var append ="";
  $(".user_id").empty();
  append += '<option value="" style=" background-color: white !important;;">--</option>';
                    $.each(data,function(key,value){
                        console.log(value.id);
                         append += '<option   style="background-color: white !important; display:intial !important; " value="'+value.id+'">'+value.name+'</option>';

                     });
                     
                     $('#s2id_user_id').css({'display':'none'});
                    $('#user_id').css({'display':'block'});
                    // $('#user_id').css({"display","intial"});
                     $("#user_id").append(append);
                    
            

             

    });
});


</script>
@endpush
