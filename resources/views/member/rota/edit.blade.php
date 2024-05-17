{{--<script type="text/javascript">--}}
{{--    $(".modal-title").text("Edit Rota");--}}
{{--    $(".modal-dialog").addClass('modal-lg');--}}
{{--</script>--}}
{{--<div class="row">--}}
{{--    <div class="col-md-12">--}}
{{--        {!! Form::model($rota,['url'=>['rota',$rota->id],'id'=>'myForm','novalidate'=>'','method'=>'PUT','files'=>true]) !!}--}}
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
{{--            <input type="date" name="date" required class="form-control" value="{{ $rota->date }}">--}}
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
    <h4 class="modal-title"> <i class="fa fa-plus"></i> Roster Edit</h4>
</div>
<div class="modal-body">
    <div class="col-md-12">
        {!! Form::model($rota,['url'=>['member/rota',$rota->id],'id'=>'myForm','novalidate'=>'','method'=>'PUT','files'=>true]) !!}
        <div class="form-group clearfix">
            <label class="control-label">Employee
                <star>*</star>
            </label>
            {!! Form::select('user_id',$staffs,null,['class'=>'form-control','required','placeholder'=>'Select Staff']) !!}
        </div>
        <div class="form-group clearfix">
            <label class="control-label">Date
                <star>*</star>
            </label>
            <input type="date" name="date" required class="form-control" value="{{ $rota->date }}">
        </div>
        <div class="form-group clearfix">
            <label class="control-label">Start time
                <star>*</star>
            </label>
            <select class="form-control" id="start_time" name="start_time">
                @foreach($times as $time)
                    <option value="{{$time->c_times}}" @if($time->c_times==$rota->start_time) selected @endif>{{\Carbon\Carbon::parse($time->c_times)->format('g:i a')}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group clearfix">
            <label class="control-label">End time
                <star>*</star>
            </label>
            <select class="form-control" id="end_time" name="end_time">
                @foreach($times as $time)
                    <option value="{{$time->c_times}}" @if($time->c_times==$rota->end_time) selected @endif>{{\Carbon\Carbon::parse($time->c_times)->format('g:i a')}}</option>
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

    </div></div>
<div class="modal-footer">

</div>

<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
