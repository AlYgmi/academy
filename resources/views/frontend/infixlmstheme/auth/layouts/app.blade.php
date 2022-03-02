<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ Settings('site_title')  }}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="{{getCourseImage(Settings('favicon') )}}">

    <!-- CSS here -->
    <link rel="stylesheet" href="{{ asset('public/css/bootstrap.min.css') }}">
    <style>
        :root {
            --system_primery_color: {{$color->primary_color??'#FB1159' }};
            --system_secendory_color: {{$color->secondary_color??'#202E3B' }} ;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme/css/fontawesome.css') }} ">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme/css/slicknav.css') }}">
    <link rel="stylesheet" href="{{asset('public/css/toastr.min.css')}}"/>
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme/css/style.css') }}">


    <script src="{{ asset('public/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{asset('public/js/toastr.min.js')}}"></script>
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/nice-select.css">
    <script src="{{ asset('public/frontend/infixlmstheme') }}/js/nice-select.min.js"></script>

    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/gijgo.min.css">
    <script src="{{ asset('public/frontend/infixlmstheme') }}/js/gijgo.min.js"></script>
</head>

<body>
@yield('content')

{!! \Brian2694\Toastr\Facades\Toastr::message() !!}
{!! NoCaptcha::renderJs() !!}

<script>
    if ($('.small_select').length > 0) {
        $('.small_select').niceSelect();
    }

    if ($('.datepicker').length > 0) {
        $('.datepicker').datepicker();
    }
</script>

</body>


</html>
