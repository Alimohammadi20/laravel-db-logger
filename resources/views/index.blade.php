@extends('dblogger::master')
@push('head')
    <link rel="stylesheet" href="{{asset('vendor/alimi7372/dblogger/libs/DataTables3/datatables.css')}}">
    <link rel="stylesheet"
          href="{{asset('vendor/alimi7372/dblogger/libs/prettyPrint/dist/css/pretty-print-json.min.css')}}">
    <link rel="stylesheet" href="{{asset("vendor/alimi7372/dblogger/libs/flatpickr/flatpickr.css")}}"/>
    <style>
        #table-loading-overlay {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.75);
            z-index: 10;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 12px;
        }

        #table-loading-overlay.active {
            display: flex;
        }

        #table-wrapper {
            position: relative;
            min-height: 200px;
        }

        .loading-text {
            font-size: 14px;
            color: #555;
            direction: rtl;
        }

        #api-error-banner {
            display: none;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluids mt-5 px-5">
        @include('dblogger::partials.filters')
        <hr>

        {{-- بنر خطا --}}
        <div id="api-error-banner" class="alert alert-danger alert-dismissible" role="alert">
            <strong>خطا در دریافت داده‌ها:</strong> <span id="api-error-msg"></span>
            <button type="button" class="btn-close" onclick="$('#api-error-banner').hide()"></button>
        </div>

        <div class="row">
            <div id="table-wrapper">
                {{-- loading overlay --}}
                <div id="table-loading-overlay">
                    <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="loading-text">در حال دریافت اطلاعات، لطفاً صبر کنید...</span>
                    <small class="text-muted" id="loading-elapsed"></small>
                </div>

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
                    <tbody></tbody>
                </table>
            </div>
        </div>

        {{-- Modal --}}
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="max-width: 90%">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">جزییات</h1>
                        <button type="button" class="btn-close ms-0" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="direction: ltr;" id="json_viewer">
                        {{-- loading state مودال --}}
                        <div id="modal-loading" class="text-center py-5" style="display:none;">
                            <div class="spinner-border text-secondary" role="status"></div>
                            <p class="mt-2 text-muted" style="direction:rtl">در حال بارگذاری...</p>
                        </div>
                        <div id="modal-content-wrapper">
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
    </div>

    {{-- پاس دادن route به JS --}}
    <script>
        window.dblogger = {
            apiUrl: "{{ route('dblogger::index.api') }}",
            showUrl: "{{ route('dblogger::index.api').'/get/{id}/input' }}",
            msgLimit: {{ config('dblogger.show_message_limit', 80) }},
            responseTimeMin: {{ config('dblogger.response_time.min', 1) }},
            responseTimeMax: {{ config('dblogger.response_time.max', 3) }},
        };
    </script>
@endsection

@push('script')
    <script src="{{asset('vendor/alimi7372/dblogger/libs/DataTables3/datatables.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/prettyPrint/dist/pretty-print-json.min.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/moment/moment.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/jdate/jdate.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/flatpickr/flatpickr-jdate.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/flatpickr/l10n/fa-jdate.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/jquery-timepicker/jquery-timepicker.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/pickr/pickr.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/js/forms-pickers.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/js/index.js')}}"></script>
@endpush
