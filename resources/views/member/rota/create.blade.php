{{--<script type="text/javascript">--}}
{{--    $(".modal-title").text("New Rota");--}}
{{--    $(".modal-dialog").addClass('modal-lg');--}}
{{--</script>--}}
{{--<div class="row">--}}
{{--    <div class="col-md-12">--}}
{{--        {!! Form::open(['url'=>URL::to('rota'),'id'=>'myForm','files'=>true]) !!}--}}
{{--        <div class="form-group clearfix">--}}
{{--            <label class="control-label">Employee--}}
{{--                <star>*</star>--}}
{{--            </label>--}}
{{--            {!! Form::select('user_id',$staffs,null,['class'=>'form-control','required','placeholder'=>'Select Staff']) !!}--}}
{{--        </div>--}}
{{--        <div class="form-group clearfix">--}}
{{--            <label class="control-label">Date--}}
{{--                <star>*</star>--}}
{{--            </label>--}}
{{--            <input type="date" name="date" required class="form-control">--}}
{{--        </div>--}}
{{--        <div class="form-group clearfix">--}}
{{--            <label class="control-label">Start time--}}
{{--                <star>*</star>--}}
{{--            </label>--}}
{{--            {!! Form::select('start_time',$times,null,['class'=>'form-control','required']) !!}--}}
{{--        </div>--}}
{{--        <div class="form-group clearfix">--}}
{{--            <label class="control-label">End time--}}
{{--                <star>*</star>--}}
{{--            </label>--}}
{{--            {!! Form::select('end_time',$times,null,['class'=>'form-control','required']) !!}--}}
{{--        </div>--}}

{{--        <div class="col-md-12">--}}
{{--            <div class="pull-right">--}}
{{--                <button class="btn btn-success">Save</button>--}}
{{--                <button class="btn btn-black" data-dismiss="modal">Close</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        {!! Form::close() !!}--}}

{{--    </div>--}}
{{--</div>--}}


<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"> <i class="fa fa-plus"></i>  Roster Create</h4>
</div>
<div class="modal-body">
    <div class="col-md-12">
        {!! Form::open(['route'=>'member.rota.store','id'=>'','novalidate'=>'']) !!}
        <div class="form-group clearfix">
            <label class="control-label">Employee
                <star>*</star>
            </label>
            {!! Form::select('user_id',$staffs,null,['class'=>'form-control','required','placeholder'=>'Select Staff']) !!}
        </div>
        <div class="form-group clearfix">
            <label class="control-label">From Date
                <star>*</star>
            </label>
            <input type="date" name="date" required class="form-control">
        </div>
        <div class="form-group clearfix">
            <label class="control-label">To Date
                <star>*</star>
            </label>
            <input type="date" name="to_date" required class="form-control">
        </div>
        <div class="form-group clearfix">
            <label class="control-label">Start time
                <star>*</star>
            </label>
            <select class="form-control" id="start_time" name="start_time">
                @foreach($times as $time)
                    <option value="{{$time->c_times}}">{{\Carbon\Carbon::parse($time->c_times)->format('g:i a')}}</option>
                @endforeach
            </select>
            {{--            {!! Form::select('start_time',$times,null,['class'=>'form-control','required']) !!}--}}
        </div>
        <div class="form-group clearfix">
            <label class="control-label">End time
                <star>*</star>
            </label>
            <select class="form-control" id="end_time" name="end_time">
                @foreach($times as $time)
                    <option value="{{$time->c_times}}">{{\Carbon\Carbon::parse($time->c_times)->format('g:i a')}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12">
            <div class="pull-right">
                <button class="btn btn-success">Save</button>
                <button class="btn btn-black" data-dismiss="modal">Close</button>
            </div>
        </div>
        {!! Form::close() !!}

    </div>
</div>
<div class="modal-footer">

</div>

<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<script>
    var $insertBefore = $('#insertBefore');
    var $i = 0;
    // Date Picker
    jQuery('.date-picker').datepicker({
        autoclose: true,
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });
    // Add More Inputs
    $('#plusButton').click(function(){

        $i = $i+1;
        var indexs = $i+1;
        $(' <div id="addMoreBox'+indexs+'" class="clearfix"> ' +
            '<div class="col-md-5"><div class="form-group "><input autocomplete="off" class="form-control date-picker'+$i+'" id="dateField'+indexs+'" name="date['+$i+']" data-date-format="dd/mm/yyyy" type="text" value="" placeholder="Date"/></div></div>' +
            '<div class="col-md-5 "style="margin-left:5px;"><div class="form-group"><input class="form-control " name="occasion['+$i+']" type="text" value="" placeholder="Occasion"/></div></div>' +
            '<div class="col-md-1"><button type="button" onclick="removeBox('+indexs+')" class="btn btn-sm btn-danger"><i class="fa fa-times"></i></button></div>' +
            '</div>').insertBefore($insertBefore);

        // Recently Added date picker assign
        jQuery('#dateField'+indexs).datepicker({
            autoclose: true,
            todayHighlight: true,
            weekStart:'{{ $global->week_start }}',
            format: '{{ $global->date_picker_format }}',
        });
    });
    // Remove fields
    function removeBox(index){
        $('#addMoreBox'+index).remove();
    }

    // Store Holidays
    function storeHolidays(){
        $('#dateBox').removeClass("has-error");
        $('#occasionBox').removeClass("has-error");
        $('#errorDate').html('');
        $('#errorOccasion').html('');
        $('.help-block').remove();
        var url = "{{ route('admin.holidays.store') }}";
        $.easyAjax({
            type: 'POST',
            url: url,
            container: '#add_holiday_form',
            data: $('#add_holiday_form').serialize(),
            success: function (response) {
                $('#edit-column-form').modal('hide');
            },error: function (response) {
                if(response.status == '422'){
                    if(typeof response.responseJSON.errors['date.0'] != 'undefined' && typeof response.responseJSON.errors['date.0'][0] != 'undefined'){
                        $('#dateBox').addClass("has-error");
                        $('#errorDate').html('<span class="help-block" id="errorDate">'+response.responseJSON.errors['date.0'][0]+'</span>');
                    }
                    if(typeof response.responseJSON.errors['occasion.0'] != "undefined" && response.responseJSON.errors['occasion.0'][0]  != 'undefined'){
                        $('#occasionBox').addClass("has-error");
                        $('#errorOccasion').html('<span class="help-block" id="errorOccasion">'+response.responseJSON.errors['occasion.0'][0]+'</span>');
                    }

                }
            }
        });
    }

</script>


