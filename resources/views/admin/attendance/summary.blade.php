@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 text-right bg-title-right">
            <a href="javascript:;"
            class="btn btn-info btn-outline btn-sm bulk-attendance">@lang('modules.attendance.bulkAttendance') </a>

            <a href="{{ route('admin.attendances.create') }}"
            class="btn btn-success btn-outline btn-sm">@lang('modules.attendance.markAttendance') <i class="fa fa-plus"  aria-hidden="true"></i></a>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
    <style>
        .table-responsive {
             overflow: hidden !important;
        }
    </style>
@endpush
@php

@endphp
@section('content')
    <div class="row">
   

        <div class="sttabs tabs-style-line col-md-12">
            <div class="white-box">
                <nav>
                    <ul>
                        <li class="tab-current"><a href="{{ route('admin.attendances.summary') }}"><span>@lang('app.summary')</span></a>
                        </li>
                        <li><a href="{{ route('admin.attendances.index') }}"><span>@lang('modules.attendance.attendanceByMember')</span></a>
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">@lang('app.department')</label>
                            <select class="select2 form-control department_id" data-placeholder="Choose Department" id="department_id" name="department_id">
                                <option value="0">--</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ ucwords($department->team_name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">@lang('modules.timeLogs.employeeName')</label>
                            <select class="select2 form-control user_id" data-placeholder="Choose Employee" id="user_id" name="user_id">
                                <option value="0">--</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">@lang('app.select') @lang('app.month')</label>
                            <select class="select2 form-control" data-placeholder="" id="month">
                                <option @if($month == '01') selected @endif value="01">@lang('app.january')</option>
                                <option @if($month == '02') selected @endif value="02">@lang('app.february')</option>
                                <option @if($month == '03') selected @endif value="03">@lang('app.march')</option>
                                <option @if($month == '04') selected @endif value="04">@lang('app.april')</option>
                                <option @if($month == '05') selected @endif value="05">@lang('app.may')</option>
                                <option @if($month == '06') selected @endif value="06">@lang('app.june')</option>
                                <option @if($month == '07') selected @endif value="07">@lang('app.july')</option>
                                <option @if($month == '08') selected @endif value="08">@lang('app.august')</option>
                                <option @if($month == '09') selected @endif value="09">@lang('app.september')</option>
                                <option @if($month == '10') selected @endif value="10">@lang('app.october')</option>
                                <option @if($month == '11') selected @endif value="11">@lang('app.november')</option>
                                <option @if($month == '12') selected @endif value="12">@lang('app.december')</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">@lang('app.select') @lang('app.year')</label>
                            <select class="select2 form-control" data-placeholder="" id="year">
                                @for($i = $year; $i >= ($year-4); $i--)
                                    <option @if($i == $year) selected @endif value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group m-t-20">
                            <button type="button" id="apply-filter" class="btn btn-info btn-block">@lang('app.apply')</button>
                        </div>
                    </div>

                </div>

            </div>
        </div>


    </div>

    <div class="row">
        <div class="col-xs-12" id="attendance-data"></div>
    </div>

    {{--Timer Modal--}}
    <div class="modal fade bs-modal-lg in" id="attendanceModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{--Timer Modal Ends--}}

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>

<script>
    
    $('#apply-filter').click(function () {
       showTable();
    });

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    function showTable() {

        var year = $('#year').val();
        var month = $('#month').val();

        var userId = $('#user_id').val();
         var department_id = $('#department_id').val();
      
        //refresh counts
        var url = '{!!  route('admin.attendances.summaryData') !!}';

        var token = "{{ csrf_token() }}";

        $.easyAjax({
            type: 'POST',
            data: {
                '_token': token,
                year: year,
                month: month,
                userId: userId,
                department_id: department_id
            },
            url: url,
            success: function (response) {
               $('#attendance-data').html(response.data);
            }
        });

    }

    showTable();

    $('#attendance-data').on('click', '.view-attendance',function () {
        var attendanceID = $(this).data('attendance-id');
        var url = '{!! route('admin.attendances.info', ':attendanceID') !!}';
        url = url.replace(':attendanceID', attendanceID);

        $('#modelHeading').html('{{__("app.menu.attendance") }}');
        $.ajaxModal('#projectTimerModal', url);
    });

    $('#attendance-data').on('click', '.edit-attendance',function (event) {
        var attendanceDate = $(this).data('attendance-date');
        var userData       = $(this).closest('tr').children('td:first');
        var userID         = userData[0]['firstChild']['nextSibling']['dataset']['employeeId'];
        var year           = $('#year').val();
        var month          = $('#month').val();

        var url = '{!! route('admin.attendances.mark', [':userid',':day',':month',':year',]) !!}';
        url = url.replace(':userid', userID);
        url = url.replace(':day', attendanceDate);
        url = url.replace(':month', month);
        url = url.replace(':year', year);

        $('#modelHeading').html('{{__("app.menu.attendance") }}');
        $.ajaxModal('#projectTimerModal', url);
    });

    $('body').on('click', '.bulk-attendance',function (event) {
        var url = '{!! route('admin.attendances.bulk') !!}';
        $('#modelHeading').html('{{__("app.menu.attendance") }}');
        $.ajaxModal('#projectTimerModal', url);
    });

    function editAttendance (id) {
        $('#projectTimerModal').modal('hide');

        var url = '{!! route('admin.attendances.edit', [':id']) !!}';
        url = url.replace(':id', id);
        console.log('sri ram');
        $('#modelHeading').html('{{__("app.menu.attendance") }}');
        $.ajaxModal('#attendanceModal', url);
    }


// new code for employee filter using department

$('.department_id').on('change', function(){
        
        var department_id=$(this).val();
      
       
        $.get('filter/department',{ department_id: department_id }, function(data){
          
  
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