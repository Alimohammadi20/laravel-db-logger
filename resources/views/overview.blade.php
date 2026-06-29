@extends('dblogger::master')
@push('head')
    <link rel="stylesheet" href="{{asset('vendor/alimi7372/dblogger/libs/DataTables3/datatables.css')}}">
    <link rel="stylesheet"
          href="{{asset('vendor/alimi7372/dblogger/libs/prettyPrint/dist/css/pretty-print-json.min.css')}}">
    <link rel="stylesheet" href="{{asset("vendor/alimi7372/dblogger/libs/flatpickr/flatpickr.css")}}"/>
@endpush
@section('content')
    <div class="container-fluid py-3">

        {{-- Spinner --}}
        <div id="overview-loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">در حال بارگذاری...</span>
            </div>
        </div>

        {{-- محتوا --}}
        <div id="overview-content" style="display:none;">
            <div id="overview-container row"></div>
        </div>

        {{-- خطا --}}
        <div id="overview-error" class="alert alert-danger" style="display:none;">
            خطا در دریافت اطلاعات
        </div>

    </div>
@endsection

@push('script')
    <script>
        window.dblogger = {
            overviewApiUrl: "{{ route('dblogger::overview.api') }}",
            showUrl: "{{route('dblogger::index')}}",
            deleteBaseUrl: "{{ url('logs') }}",
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
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
    <script src="{{asset('vendor/alimi7372/dblogger/js/overview.js')}}"></script>

@endpush
