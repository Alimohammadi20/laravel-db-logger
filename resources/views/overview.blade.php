@extends('dblogger::master')
@push('head')
    <link rel="stylesheet" href="{{asset('vendor/alimi7372/dblogger/libs/DataTables3/datatables.css')}}">
    <link rel="stylesheet"
          href="{{asset('vendor/alimi7372/dblogger/libs/prettyPrint/dist/css/pretty-print-json.min.css')}}">
    <link rel="stylesheet" href="{{asset("vendor/alimi7372/dblogger/libs/flatpickr/flatpickr.css")}}"/>
@endpush
@section('content')
    <div class="container-fluids mt-2 px-5">
        <div class="card">
            <div class="card-header">
                نمایش کلی
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($datas as $day =>$data)
                        @php
                            $date = jdate($day)->format('Y-m-d');
                        @endphp
                        <div class="col-md-3" id="card-box-{{$date}}">
                            <div class="card">
                                <div class="card-header">
                                    {{$date}}
                                </div>
                                <div class="card-body">
                                    <div class="card my-2">
                                        <ul class="list-group list-group-flush">
                                            @foreach($data as $level)
                                                @php
                                                    $levelClass = match (strtolower($level['level'])){
                                                        'error'=>['class'=>'bg-danger','title'=>'خطا'],
                                                        'emergency'=>['class'=>'bg-danger','title'=>'اورژانسی'],
                                                        'critical'=>['class'=>'bg-danger','title'=>'بحرانی'],
                                                        'alert'=>['class'=>'bg-warning','title'=>'اطلاع'],
                                                        'warning'=>['class'=>'bg-warning','title'=>'هشدار'],
                                                        'info'=>['class'=>'bg-info','title'=>'اطلاعات'],
                                                        'debug'=>['class'=>'bg-dark','title'=>'دیباگ'],
                                                        'notice'=>['class'=>'bg-info','title'=>'اطلاع'],
                                                        'success'=>['class'=>'bg-success','title'=>'موفق'],
                                                        default=>['class'=>'bg-secondary','title'=>'ناشناخته']
                                                    }
                                                @endphp
                                                <li class="list-group-item {{$levelClass['class']}} bg-gradient text-white bg-opacity-75">
                                                    <div class="row my-2 p-2">
                                                        <div class="col-4 text-start d-flex align-items-center"> {{$levelClass['title']}}</div>
                                                        <div class="col-4 text-center d-flex align-items-center">{{$level['count']}}</div>
                                                        <div class="col-4 text-center d-flex align-items-center">
                                                            <a href="{{route('dblogger::index',['date'=>$date,'level'=>$level['level']])}}"
                                                               class="btn btn-light text-dark">
                                                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="card-footer d-flex justify-content-end">
                                        <a href="{{route('dblogger::index',['date'=>$date])}}"
                                           class="btn btn-success mx-2">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                        <a href="{{route('dblogger::destroy',['date'=>$date])}}"
                                           data-reference="card-box-{{$date}}"
                                           class="btn btn-danger delete-logs">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{asset('vendor/alimi7372/dblogger/libs/DataTables3/datatables.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/prettyPrint/dist/pretty-print-json.min.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/moment/moment.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/jdate/jdate.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/flatpickr/flatpickr-jdate.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/flatpickr/l10n/fa-jdate.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
    <script
            src="{{asset('vendor/alimi7372/dblogger/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/jquery-timepicker/jquery-timepicker.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/pickr/pickr.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/js/forms-pickers.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/js/index.js')}}"></script>

@endpush
