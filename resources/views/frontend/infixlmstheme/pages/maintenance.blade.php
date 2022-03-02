<!doctype html>
<html lang="en"    >

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <title>
        {{env('APP_NAME')}}} | {{$maintain->maintenance_title}}
    </title>


    <!-- Place favicon.ico in the root directory -->
    <link rel="stylesheet" href="{{ asset('public') }}/css/bootstrap.min.css">


    <style>
        :root {
            --system_primery_color: {{$color->primary_color??'#FB1159' }};
            --system_secendory_color: {{$color->secondary_color??'#202E3B' }} ;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/owl.carousel.min.css">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/magnific-popup.css">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/fontawesome.css ">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/themify-icons.css">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/flaticon.css">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/nice-select.css">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/animate.min.css">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/slicknav.css">
    <link rel="stylesheet" href="{{asset('public')}}/css/toastr.min.css"/>
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/rtl_style.css">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/style.css">
    <link rel="stylesheet" href="{{asset('public/css/preloader.css')}}"/>


</head>

<body>

@include('preloader')


<div class="error_wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12">
                <div class="error_wrapper_info text-center">
                    <div class="thumb">
                        <img src="{{asset($maintain->maintenance_banner)}}" alt="">
                    </div>
                    <h3>{{$maintain->maintenance_title}}</h3>
                    <p>{!! $maintain->maintenance_sub_title !!}</p>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="{{ asset('public') }}/js/jquery-3.5.1.min.js"></script>
<script src="{{ asset('public') }}/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('public/frontend/infixlmstheme') }}/js/owl.carousel.min.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/waypoints.min.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/jquery.counterup.min.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/imagesloaded.pkgd.min.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/wow.min.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/nice-select.min.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/barfiller.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/jquery.slicknav.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/jquery.magnific-popup.min.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/jquery.ajaxchimp.min.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/parallax.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/mail-script.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/main.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/footer.js"></script>
<script src="{{asset('public')}}/js/toastr.min.js"></script>


<script>
    setTimeout(function () {
        $('.preloader').fadeOut('slow', function () {
            $(this).remove();
        });
    }, 0);
</script>

</body>

</html>

