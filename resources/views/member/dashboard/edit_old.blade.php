


<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"> <i class="fa fa-plus"></i>Reason Check Out</h4>
</div>
<div class="modal-body">
    <div class="col-md-12">
        {!! Form::model($data,['url'=>['member/attendances',$attendanceId],'id'=>'myForm','novalidate'=>'','method'=>'PUT','files'=>true]) !!}

        <?php
        foreach($data as $datas)
        {

            @$array[]=@$datas->name;
        }



        ?>
        <div class="form-group clearfix">
            <label class="control-label">Reason Check Out
                <star>*</star>
            </label>
            <select class="form-control" id="start_time" name="reason">
                <option value="10">Clock Out</option>
@if(empty(@$data1->first_open_break_time))
                    <option value="1">{{@$array[0]}}</option>
                    @endif
                    @if(empty(@$data1->lunch_break_start_time))
                    <option value="3">{{@$array[2]}}</option>
                    @endif
                     @if(empty(@$data1->third_break_in))
                    <option value="5">{{@$array[4]}}</option>
                    @endif
                     @if(empty(@$data1->for_break_in))
                    <option value="7">{{@$array[6]}}</option>
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
