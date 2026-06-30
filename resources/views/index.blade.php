@extends('dblogger::master')
@push('head')
    <link rel="stylesheet" href="{{asset('vendor/alimi7372/dblogger/libs/DataTables3/datatables.css')}}">
    <link rel="stylesheet"
          href="{{asset('vendor/alimi7372/dblogger/libs/prettyPrint/dist/css/pretty-print-json.min.css')}}">
    <link rel="stylesheet" href="{{asset("vendor/alimi7372/dblogger/libs/flatpickr/flatpickr.css")}}" />
@endpush
@section('content')
    <div class="container-fluid mt-4 px-4">

        {{-- فیلترها --}}
        <div class="filter-card mb-3">
            <p class="section-title">فیلترها</p>
            @include('dblogger::partials.filters')
        </div>

        {{-- بنر خطا --}}
        <div id="api-error-banner"
             class="alert alert-danger alert-dismissible"
             role="alert" style="display: none;">
            <div class="d-flex align-items-center gap-2 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     viewBox="0 0 16 16" class="flex-shrink-0">
                    <path
                            d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                </svg>
                <div>
                    <strong>خطا در دریافت داده‌ها:</strong>
                    <span id="api-error-msg"></span>
                </div>
                <button type="button" class="btn-close ms-auto"
                        onclick="$('#api-error-banner').hide()" aria-label="بستن"></button>
            </div>
        </div>

        {{-- جدول --}}
        <div class="table-card">
            <div class="table-card-header">
                <div>
                    <p class="card-title">لاگ‌های سیستم</p>
                    <p class="card-subtitle">نتایج بر اساس فیلترهای انتخاب‌شده</p>
                </div>
            </div>

            <div id="table-wrapper">
                {{-- Loading overlay --}}
                <div id="table-loading-overlay">
                    <div class="spinner-border text-primary" role="status"
                         style="width:2.6rem;height:2.6rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="loading-text">در حال دریافت اطلاعات، لطفاً صبر کنید...</span>
                    <small class="text-muted" id="loading-elapsed"></small>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="inbox-table">
                        <thead>
                        <tr>
                            <th class="ps-3">#</th>
                            <th style="min-width:130px">زمان</th>
                            <th>سطح</th>
                            <th>نوع</th>
                            <th>خروجی</th>
                            <th style="min-width:140px">زمان پاسخ</th>
                            <th class="text-center pe-3">جزئیات</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- ─── Modal ─── --}}
    <div class="modal fade" id="exampleModal" tabindex="-1"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">جزئیات لاگ</h5>
                    <button type="button" class="btn-close ms-0"
                            data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>

                <div class="modal-body" style="direction:ltr;">

                    {{-- loading state --}}
                    <div id="modal-loading" class="text-center py-5" style="display:none;">
                        <div class="spinner-border text-secondary" role="status"></div>
                        <p class="mt-2 text-muted small" style="direction:rtl">در حال بارگذاری...</p>
                    </div>

                    <div id="modal-content-wrapper">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab"
                                        data-bs-target="#details-tab-pane"
                                        type="button" role="tab" aria-selected="true">
                                    اطلاعات کلی
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab"
                                        data-bs-target="#inputs-tab-pane"
                                        type="button" role="tab" aria-selected="false">
                                    ورودی
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab"
                                        data-bs-target="#outputs-tab-pane"
                                        type="button" role="tab" aria-selected="false">
                                    خروجی
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab"
                                        data-bs-target="#context-tab-pane"
                                        type="button" role="tab" aria-selected="false">
                                    کانتکست
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab"
                                        data-bs-target="#extradata-tab-pane"
                                        type="button" role="tab" aria-selected="false">
                                    دیتاهای اضافی
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="details-tab-pane" role="tabpanel">
                                <div id="json_detail"></div>
                            </div>
                            <div class="tab-pane fade" id="inputs-tab-pane" role="tabpanel">
                                <div id="json_input"></div>
                            </div>
                            <div class="tab-pane fade" id="outputs-tab-pane" role="tabpanel">
                                <div id="json_output"></div>
                            </div>
                            <div class="tab-pane fade" id="context-tab-pane" role="tabpanel">
                                <div id="json_context"></div>
                            </div>
                            <div class="tab-pane fade" id="extradata-tab-pane" role="tabpanel">
                                <div id="json_extradata"></div>
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
            responseTimeMax: {{ config('dblogger.response_time.max', 3) }}
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
    <script
            src="{{asset('vendor/alimi7372/dblogger/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/jquery-timepicker/jquery-timepicker.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/libs/pickr/pickr.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/js/forms-pickers.js')}}"></script>
    <script src="{{asset('vendor/alimi7372/dblogger/js/index.js')}}"></script>
@endpush
