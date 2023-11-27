@extends('dblogger::master')
@push('head')
    <link rel="stylesheet" href="{{asset('vendor/alimi7372/dblogger/libs/DataTables3/datatables.css')}}">
    <link rel="stylesheet"
          href="{{asset('vendor/alimi7372/dblogger/libs/prettyPrint/dist/css/pretty-print-json.min.css')}}">
    <link rel="stylesheet" href="{{asset("vendor/alimi7372/dblogger/libs/flatpickr/flatpickr.css")}}"/>
@endpush
@section('content')
    <div class="container-fluids mt-5 px-5">
        @include('dblogger::partials.filters')
        <hr>
        <div class="row">
            <table class="table table-hover" id="inbox-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th style="min-width: 100px">time</th>
                    <th>level</th>
                    <th>type</th>
                    <th>output</th>
                    <th>response time</th>
                    <th>details</th>
                </tr>
                </thead>
                <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{jdate($log->created_at)}}</td>
                        <td>
                            @switch($log->level)
                                @case('ERROR')
                                    <small class="badge badge bg-danger">{{$log->level}}</small>
                                    @break
                                @case('SUCCESS')
                                    <small class="badge bg-success">{{$log->level}}</small>
                                    @break
                                @default
                                    <small class="badge bg-info">{{$log->level}}</small>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            @switch($log->type)
                                @case('PROCESS')
                                    <small class="badge bg-warning">{{$log->type}}</small>
                                    @break
                                @case('SERVICE')
                                    <small class="badge bg-secondary">{{$log->type}}</small>
                                    @break
                                @default
                                    <small class="badge bg-primary">{{$log->type}}</small>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            <small>{{Str::limit($log->message,config('dblogger.show_message_limit'),'...')}}</small>
                        </td>
                        <td>
                            @if($log->response_time > config('dblogger.response_time.min') && $log->response_time < config('dblogger.response_time.max'))
                                <h5>s <span class="badge bg-warning">{{$log->response_time}}</span></h5>
                            @elseif( $log->response_time >= config('dblogger.response_time.max'))
                                <h5>s <span class="badge bg-danger">{{$log->response_time}}</span></h5>
                            @else
                                <h5>s <span class="badge bg-success">{{$log->response_time}}</span></h5>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-info show-input" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal" data-id="{{$log->id}}"
                                    data-url="{{route('dblogger::show',$log->id)}}">
                                نمایش
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <!-- Button trigger modal -->
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="max-width: 90%">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">جزییات</h1>
                        <button type="button" class="btn-close ms-0" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="direction: ltr;" id="json_viewer">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                        data-bs-target="#details-tab-pane" type="button" role="tab"
                                        aria-controls="details-tab-pane" aria-selected="true">اطلاعات کلی
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="input-tab" data-bs-toggle="tab"
                                        data-bs-target="#inputs-tab-pane" type="button" role="tab"
                                        aria-controls="inputs-tab-pane" aria-selected="false">ورودی
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab"
                                        data-bs-target="#outputs-tab-pane" type="button" role="tab"
                                        aria-controls="outputs-tab-pane" aria-selected="false">خروجی
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="context-tab" data-bs-toggle="tab"
                                        data-bs-target="#context-tab-pane" type="button" role="tab"
                                        aria-controls="context-tab-pane" aria-selected="false">کانتکس
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="extradata-tab" data-bs-toggle="tab"
                                        data-bs-target="#extradata-tab-pane" type="button" role="tab"
                                        aria-controls="extradata-tab-pane" aria-selected="false">دیتاهای اضافی
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="details-tab-pane" role="tabpanel"
                                 aria-labelledby="home-tab" tabindex="0">
                                <pre id="json_detail"></pre>
                            </div>
                            <div class="tab-pane fade" id="inputs-tab-pane" role="tabpanel"
                                 aria-labelledby="input-tab" tabindex="0">
                                <pre id="json_input"></pre>
                            </div>
                            <div class="tab-pane fade" id="outputs-tab-pane" role="tabpanel"
                                 aria-labelledby="contact-tab" tabindex="0">
                                <pre id="json_output"></pre>
                            </div>
                            <div class="tab-pane fade" id="context-tab-pane" role="tabpanel"
                                 aria-labelledby="context-tab" tabindex="0">
                                <pre id="json_context"></pre>
                            </div>
                            <div class="tab-pane fade" id="extradata-tab-pane" role="tabpanel"
                                 aria-labelledby="extradata-tab" tabindex="0">
                                <pre id="json_extradata"></pre>
                            </div>
                        </div>
                    </div>
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
