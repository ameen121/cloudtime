


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
             @foreach($data as $reasons)
             @if($reasons['reasons_value']==1)
                @if($reasons['data1']['first_break_start']==null)
                  <option value="{{$reasons['reasons_value']}},{{$reasons['status']}}">{{$reasons['name']}}</option>
                @endif
             @endif
             @if($reasons['reasons_value']==3)
                @if($reasons['data1']['second_break_start']==null)
                  <option value="{{$reasons['reasons_value']}},{{$reasons['status']}}">{{$reasons['name']}}</option>
                @endif
             @endif
             @if($reasons['reasons_value']==5)
                @if($reasons['data1']['third_break_start']==null)
                  <option value="{{$reasons['reasons_value']}},{{$reasons['status']}}">{{$reasons['name']}}</option>
                @endif
             @endif
             @if($reasons['reasons_value']==7)
                @if($reasons['data1']['four_break_start']==null)
                  <option value="{{$reasons['reasons_value']}},{{$reasons['status']}}">{{$reasons['name']}}</option>
                @endif
             @endif
             @if($reasons['reasons_value']==9)
                @if($reasons['data1']['five_break_start']==null)
                  <option value="{{$reasons['reasons_value']}},{{$reasons['status']}}">{{$reasons['name']}}</option>
                @endif
             @endif
            
             @if($reasons['reasons_value']==11)
                @if($reasons['data1']['six_break_start']==null)
                  <option value="{{$reasons['reasons_value']}},{{$reasons['status']}}">{{$reasons['name']}}</option>
                @endif
              @endif 
              @if($reasons['reasons_value']==13)
                @if($reasons['data1']['seven_break_start']==null)
                  <option value="{{$reasons['reasons_value']}},{{$reasons['status']}}">{{$reasons['name']}}</option>
                @endif
              @endif   
              
              @if($reasons['reasons_value']==15)
                @if($reasons['data1']['eight_break_start']==null)
                  <option value="{{$reasons['reasons_value']}},{{$reasons['status']}}">{{$reasons['name']}}</option>
                @endif
              @endif  
              @if($reasons['reasons_value']==17)
                @if($reasons['data1']['nine_break_start']==null)
                  <option value="{{$reasons['reasons_value']}},{{$reasons['status']}}">{{$reasons['name']}}</option>
                @endif
              @endif 
              
             @endforeach
                <option value="25">Clock Out</option> 
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
