


<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"> <i class="fa fa-plus"></i>Reason</h4>
</div>
<div class="modal-body">
    <div class="col-md-12">
        {!! Form::model($data,['url'=>['member/attendances',$attendanceId],'id'=>'myForm','novalidate'=>'','method'=>'PUT','files'=>true]) !!}

        <?php
        $array=[];
       
        // foreach($data as $datas)
        // {

        //     @$array[]=@$datas->name;
        // }
        // dd($array);

        ?>
        <div class="form-group clearfix">
            <label class="control-label">Reason Check In
                <star>*</star>
            </label>
            <select class="form-control" id="start_time" name="reason">
               @if($data->count()>0) 
               @foreach($data as $datas)
                <option value="{{$datas->reasons_value}}">{{$datas->name}} </option>    
                @endforeach
                @endif
           </select>

           {{-- 
            @if($data->count()==18) 
            <select class="form-control" id="start_time" name="reason">
               @if(\Session::get('first_break_end'))
                <option value="2">{{@$array[1]}}</option>
                @endif
                @if(\Session::get('second_break_end'))
                <option value="4">{{@$array[3]}}</option>
                @endif
                @if(\Session::get('third_break_end'))
                <option value="6">{{@$array[5]}}</option>
                @endif
                @if(\Session::get('four_break_end'))
                <option value="8">{{@$array[7]}}</option>
                @endif 
                @if(\Session::get('five_break_end'))
                <option value="five_break_end">{{@$array[9]}}</option>    
                @endif
                @if(\Session::get('six_break_end'))
                <option value="12">{{@$array[11]}}</option>    
                @endif
                @if(\Session::get('seven_break_end'))
                <option value="14">{{@$array[13]}}</option>  
                @endif
                @if(\Session::get('eight_break_end'))
                 <option value="16">{{@$array[15]}}</option>     
                @endif
                @if(\Session::get('nine_break_end'))
                 <option value="18">{{@$array[17]}}</option>     
                @endif
            </select>
            @endif 

            @if($data->count()==16) 
            <select class="form-control" id="start_time" name="reason">
              
                @if(\Session::get('first_break_end'))
                <option value="2">{{@$array[1]}}</option>
                @endif
                @if(\Session::get('second_break_end'))
                <option value="4">{{@$array[3]}}</option>
                @endif
                @if(\Session::get('third_break_end'))
                <option value="6">{{@$array[5]}}</option>
                @endif
                @if(\Session::get('four_break_end'))
                <option value="8">{{@$array[7]}}</option>
                @endif 
                @if(\Session::get('five_break_end'))
                <option value="five_break_end">{{@$array[9]}}</option>    
                @endif
                @if(\Session::get('six_break_end'))
                <option value="12">{{@$array[11]}}</option>    
                @endif
                @if(\Session::get('seven_break_end'))
                <option value="14">{{@$array[13]}}</option>  
                @endif
                @if(\Session::get('eight_break_end'))
                 <option value="16">{{@$array[15]}}</option>     
                @endif
            </select>
            @endif 

            @if($data->count()==14) 
            <select class="form-control" id="start_time" name="reason">
            @if(\Session::get('first_break_end'))
                <option value="2">{{@$array[1]}}</option>
                @endif
                @if(\Session::get('second_break_end'))
                <option value="4">{{@$array[3]}}</option>
                @endif
                @if(\Session::get('third_break_end'))
                <option value="6">{{@$array[5]}}</option>
                @endif
                @if(\Session::get('four_break_end'))
                <option value="8">{{@$array[7]}}</option>
                @endif 
                @if(\Session::get('five_break_end'))
                <option value="five_break_end">{{@$array[9]}}</option>    
                @endif
                @if(\Session::get('six_break_end'))
                <option value="12">{{@$array[11]}}</option>    
                @endif
                 @if(\Session::get('seven_break_end'))
                 <option value="14">{{@$array[13]}}</option>     
                <!--reason 10 val replace -->
                @endif
            </select>
            @endif 

            @if($data->count()==12) 
            <select class="form-control" id="start_time" name="reason">
                @if(\Session::get('first_break_end'))
                <option value="2">{{@$array[1]}}</option>
                @endif
                @if(\Session::get('second_break_end'))
                <option value="4">{{@$array[3]}}</option>
                @endif
                @if(\Session::get('third_break_end'))
                <option value="6">{{@$array[5]}}</option>
                @endif
                @if(\Session::get('four_break_end'))
                <option value="8">{{@$array[7]}}</option>
                @endif 
                @if(\Session::get('five_break_end'))
                <option value="five_break_end">{{@$array[9]}}</option>    
                @endif
                @if(\Session::get('six_break_end'))
                <option value="12">{{@$array[11]}}</option>    
                @endif
            </select>
            @endif  
          
            @if($data->count()==10) 
            <select class="form-control" id="start_time" name="reason">
               @if(\Session::get('first_break_end'))
                <option value="2">{{@$array[1]}}</option>
                @endif
                @if(\Session::get('second_break_end'))
                <option value="4">{{@$array[3]}}</option>
                @endif
                @if(\Session::get('third_break_end'))
                <option value="6">{{@$array[5]}}</option>
                @endif
                @if(\Session::get('four_break_end'))
                <option value="8">{{@$array[7]}}</option>
                @endif 
                @if(\Session::get('five_break_end'))
                 <option value="five_break_end">{{@$array[9]}}</option>    
                @endif
            </select>
            @endif  

           @if($data->count()==8) 
            <select class="form-control" id="start_time" name="reason">
                @if(\Session::get('first_break_end'))
                <option value="2">{{@$array[1]}}</option>
                @endif
                @if(\Session::get('second_break_end'))
                <option value="4">{{@$array[3]}}</option>
                @endif
                @if(\Session::get('third_break_end'))
                <option value="6">{{@$array[5]}}</option>
                @endif
                @if(\Session::get('four_break_end'))
                <option value="8">{{@$array[7]}}</option>
                @endif     
            </select>
            @endif
           
            @if($data->count()==6) 
            <select class="form-control" id="start_time" name="reason">
            @if(\Session::get('first_break_end'))
                <option value="2">{{@$array[1]}}</option>
                @endif
                @if(\Session::get('second_break_end'))
                <option value="4">{{@$array[3]}}</option>
                  @endif
                  @if(\Session::get('third_break_end'))
                <option value="6">{{@$array[5]}}</option>
                  @endif
                  
            </select>
            @endif

            @if($data->count()==4) 
            <select class="form-control" id="start_time" name="reason">
              @if(\Session::get('first_break_end'))
                <option value="2">{{@$array[1]}}</option>
                @endif
              @if(\Session::get('second_break_end'))
                <option value="4">{{@$array[3]}}</option>
              @endif  
                 
            </select>
            @endif

            @if($data->count()==2) 
            <select class="form-control" id="start_time" name="reason">
              @if(empty(@$data1->first_break_end))
                <option value="2">{{@$array[1]}}</option>
                @endif
                 
            </select>
            @endif
            --}}
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
