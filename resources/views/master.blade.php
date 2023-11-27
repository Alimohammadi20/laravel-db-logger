<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>
        نمایش لاگ ها
    </title>
    <meta name="language" content="FA"/>
    <meta name="robots" content="noindex,nofollow"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" media="screen" type="text/css"
          href="{{asset('vendor/alimi7372/logger/libs/bootstrap-5.0.2/css/bootstrap.rtl.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('vendor/alimi7372/logger/libs/sweetalert/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('vendor/alimi7372/logger/css/index.css') }}">
    @stack('head')
</head>
<!-- Body-->
<body id="body">
<div class="other-side">
    <!-- Page Content-->
    <div class="container-fluid" id="content-page">
        @yield('content')
    </div>
    <!-- Footer-->

</div>

<script src="{{asset('vendor/alimi7372/logger/libs/jquery-3.7.1/jquery-3.7.1.min.js')}}"></script>
<script src="{{asset('vendor/alimi7372/logger/libs/bootstrap-5.0.2/js/bootstrap.bundle.min.js')}}"></script>

@stack('script')

</body>
</html>
