


<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"> <i class="fa fa-plus"></i>Reason</h4>
</div>
<div class="modal-body">
    <div class="col-md-12">
        {!! Form::model($data,['url'=>['member/attendances',$attendanceId],'id'=>'myForm','novalidate'=>'','method'=>'PUT','files'=>true]) !!}

        <?php
        foreach($data as $datas)
        {

            @$array[]=@$datas->name;
        }

//dd($array);

        ?>
        <div class="form-group clearfix">
            <label class="control-label">Reason Check In
                <star>*</star>
            </label>
            <select class="form-control" id="start_time" name="reason">
@if(empty(@$data1->first_close_break_time))
                <option value="2">{{@$array[1]}}</option>
                @endif
                  @if(empty(@$data1->lunch_break_close_time))
                <option value="4">{{@$array[3]}}</option>
                  @endif
                    @if(empty(@$data1->third_break_out))
                <option value="6">{{@$array[5]}}</option>
                  @endif
                    @if(empty(@$data1->for_break_out))
                <option value="8">{{@$array[7]}}</option>
                  @endif
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
