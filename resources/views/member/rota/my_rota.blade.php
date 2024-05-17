@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <div class="col-sm-9 text-right">
            <div class="form-group">
{{--                <a onclick="showAdd()" class="btn btn-outline btn-success btn-sm pull-right m-l-5">Add Rota <i class="fa fa-plus" aria-hidden="true"></i></a>--}}

                {{--                <a href="{{ route('admin.attendances.create') }}"--}}
                {{--                   class="btn btn-success btn-sm">@lang('modules.attendance.markAttendance') <i class="fa fa-plus"--}}
                {{--                                                                                                aria-hidden="true"></i></a>--}}
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

@section('content')

    <style>
        table {
            overflow: hidden;
        }

        td, th {
            padding: 10px;
            position: relative;
            outline: 0;
        }

        body:not(.nohover) tbody tr:hover {
            background-color: #ffa;
        }

        td:hover::after,
        thead th:not(:empty):hover::after,
        td:focus::after,
        thead th:not(:empty):focus::after {
            content: '';
            height: 10000px;
            left: 0;
            position: absolute;
            top: -5000px;
            width: 100%;
            z-index: -1;
        }

        td:hover::after,
        th:hover::after {
            background-color: #ffa;
        }

        td:focus::after,
        th:focus::after {
            background-color: lightblue;
        }

        /* Focus stuff for mobile */
        td:focus::before,
        tbody th:focus::before {
            background-color: lightblue;
            content: '';
            height: 100%;
            top: 0;
            left: -5000px;
            position: absolute;
            width: 10000px;
            z-index: -1;
        }

        .profile-pic:hover .edit {
            display: block;
        }

        .profile-pic:hover .delete {
            display: block;
        }

        .edit {
            padding-top: 1px;
            padding-right: 5px;
            position: absolute;
            right: 0;
            top: 0;
            display: none;
        }

        .delete{
            padding-top: 8px;
            padding-right: 5px;
            position: absolute;
            right: 0;
            top: 18px;
            display: none;
        }

        .edit a {
            color: #000;
            font-size: 14px;
        }

        .delete a {
            color: red;
            font-size: 14px;
        }
    </style>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Roster Filter</h3>
                <div class="float-right">

                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('member.myRota') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input class="form-control  datetimepicker" type="date" name="from" value="{{ $startDate }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input class="form-control floating datetimepicker" value="{{ $endDate }}" type="date" name="to" required >
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success btn-block"> <i class="fa fa-search"></i> Search </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">

            <div class="card-header">
                <h3 class="card-title">Roster <b>({{ $startDate }} To {{ $endDate }})</b></h3>
            </div>
            <div class="card-body">
                <?php
                $start = "06:00"; //you can write here 00:00:00 but not need to it
                $end = "23:30";

                $tStart = strtotime($start);
                $tEnd = strtotime($end);
                $tNow = $tStart;

                $ttNow = strtotime($start);
                $ttEnd = strtotime($end);
                $collection=collect($rota);
                ?>
                <div class="table-responsive">
                    <table class="table table-bordered  mb-0 bg-white">
                        <thead>
                        <tr>
                            <th width="20%">Name</th>
                            @foreach($days as $time)
                                <th width="12%" class="text-center text-uppercase">{{ $time['day'] }}</th>
                            @endforeach

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($times as $time)
                            <?php
                            $check=$collection->where('start_time','<=',$time->c_times)->where('end_time','>=',$time->c_times);
                            ?>
                            <tr>
                                <td>{{\Carbon\Carbon::parse($time->c_times)->format('g:i a')}}</td>
                                @foreach($days as $key=>$time)
                                    @if($check && count($check)>0)
                                        @php
                                            $checkDate=$check->where('date',$time['date'])->first();
                                            $color="ccebf7";
                                            if ($key==0){
 $color="ccebf7";
                                            }elseif ($key==1){
 $color="eab3de";
                                            }elseif ($key==2){
 $color="aba6aa";
                                            }elseif ($key==3){
 $color="827eea";
                                            }elseif ($key==4){
 $color="7ee7ea";
                                            }elseif ($key==5){
 $color="3c8dbc";
                                            }elseif ($key==6){
 $color="b1bc3c";
                                            }
                                        @endphp

                                        <td class="text-center text-uppercase profile-pic" @if($checkDate) style="background-color: #{{ $color }}" @endif>

                                            {{--                                            @if($checkDate)--}}
                                            {{--                                                <div class="delete"><a href="{{ route('rota.delete',$checkDate->id) }}" onclick="return confirm('Are you sure you want to delete?');"><i class="fa fa-trash"></i></a></div>--}}
                                            {{--                                                {{ date('g:i A',strtotime($checkDate->start_time)) }} - {{ date('g:i A',strtotime($checkDate->end_time))  }}--}}

                                            {{--                                                <div class="edit"><a href="#" data-toggle="modal" data-target="#modal" onclick="loadModal('{{ route('rota.edit',$checkDate->id) }}')"><i class="fa fa-edit"></i></a></div>--}}

                                            {{--                                            @endif--}}
                                        </td>

                                    @else
                                        <td></td>
                                    @endif
                                @endforeach

                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')

@endsection
