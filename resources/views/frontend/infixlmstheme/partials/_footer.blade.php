<!-- FOOTER::START  -->

<footer class="{{Settings('footer_show')==0?'d-none d-sm-none d-md-block d-lg-block d-xl-block':''}}">
    @if($homeContent->show_subscribe_section==1)
        @if($newsletterSetting)
            @if($newsletterSetting->home_status==1)
                <div class="footer_top_area">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="footer__cta">
                                    <div class="thumb">
                                        <img src="{{asset(@$frontendContent->subscribe_logo)}}" alt="" class="w-100">
                                    </div>
                                    <div class="cta_content">
                                        <h3>{{@$frontendContent->subscribe_title}}</h3>
                                        <p>{{@$frontendContent->subscribe_sub_title}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">

                                <div class="subcribe-form theme_mailChimp">
                                    <form action="{{route('subscribe')}}"
                                          method="POST" class="subscription relative">@csrf
                                        <input name="email" class="form-control"
                                               placeholder="{{__('frontend.Enter e-mail Address')}}"
                                               onfocus="this.placeholder = ''"
                                               onblur="this.placeholder = '{{__('frontend.Email')}}'"
                                               required="" type="email" value="{{old('email')}}">

                                        <button type="submit">{{__('frontend.Subscribe Now')}}</button>
                                        <div class="info">
                                            <span class="text-danger" role="alert">{{$errors->first('email')}}</span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endif
        @endif
    @endif
    <div class="copyright_area">
        <div class="container">
            <div class="row">
                {{--                @if(!isset($sectionWidgets) || (count($sectionWidgets['one'])==0 && count($sectionWidgets['two'])==0 && count($sectionWidgets['three'])==0 ) )--}}

                <div class="col-xl-4 col-lg-4 col-md-6">
                    <div class="footer_widget">
                        <div class="footer_logo">
                            <a href="#">
                                <img src="{{getCourseImage(Settings('logo2'))}}" alt=""
                                     style="width: 108px">
                            </a>
                        </div>
                        <p>{{ Settings('footer_about_description')  }}</p>
                        <div class="copyright_text">
                            <p>{!! Settings('footer_copy_right')  !!}</p>
                        </div>

                        <style>


                        </style>
                        <div class="">
                            <ul class="pt-3 ">
                                <ul class="social-network social-circle col-lg-12 ">
                                    @if(isset($social_links))
                                        @foreach($social_links as $social)
                                            <li><a target="_blank" href="{{$social->link}}" class=""
                                                   title="{{$social->name  }}"><i
                                                        class="{{$social->icon}}"></i></a></li>
                                        @endforeach
                                    @endif
                                </ul>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-8 col-md-6">
                    <div class="row">
                        @if(isset($sectionWidgets))
                            @if(count($sectionWidgets['one'])!=0)
                                <div class="col-lg-4">
                                    <div class="footer_widget">

                                        <div class="footer_title">
                                            <h3>{{ Settings('footer_section_one_title')  }}</h3>
                                        </div>
                                        <ul class="footer_links">
                                            @foreach($sectionWidgets['one'] as $page)
                                                @if(isset($page->frontpage->id))
                                                    @php
                                                        $route = $page->is_static == 0 ? route('frontPage',$page->frontpage->slug) : url($page->frontpage->slug)
                                                    @endphp
                                                    <li><a href="{{ $route }}">{{$page->name}} </a></li>
                                                @else
                                                    <li><a href="">{{$page->name}} </a></li>
                                                @endif
                                            @endforeach
                                        </ul>

                                    </div>
                                </div>
                            @endif
                            @if(count($sectionWidgets['two'])!=0)
                                <div class="col-lg-4">
                                    <div class="footer_widget">
                                        <div class="footer_title">
                                            <h3>{{ Settings('footer_section_two_title')  }}</h3>
                                        </div>
                                        <ul class="footer_links">

                                            @foreach($sectionWidgets['two'] as $key=> $page)
                                                @if(isset($page->frontpage->id))
                                                    @php
                                                        $route = $page->is_static == 0 ? route('frontPage',$page->frontpage->slug) : url($page->frontpage->slug)
                                                    @endphp
                                                    <li><a href="{{ $route }}">{{$page->name}} </a></li>
                                                @else
                                                    <li><a href="">{{$page->name}} </a></li>
                                                @endif
                                            @endforeach

                                        </ul>
                                    </div>
                                </div>
                            @endif
                            @if(count($sectionWidgets['three'])!=0)
                                <div class="col-lg-4">
                                    <div class="footer_widget">
                                        <div class="footer_title">
                                            <h3>{{ Settings('footer_section_three_title')  }}</h3>
                                        </div>
                                        <ul class="footer_links">
                                            @foreach($sectionWidgets['three'] as $page)
                                                @if(isset($page->frontpage->id))
                                                    @php
                                                        $route = $page->is_static == 0 ? route('frontPage',$page->frontpage->slug) : url($page->frontpage->slug)
                                                    @endphp
                                                    <li><a href="{{ $route }}">{{$page->name}} </a></li>
                                                @else
                                                    <li><a href="">{{$page->name}} </a></li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- FOOTER::END  -->
<!-- shoping_cart::start  -->
<div class="shoping_wrapper">
    <div class="dark_overlay"></div>
    <div class="shoping_cart">
        <div class="shoping_cart_inner">
            <div class="cart_header d-flex justify-content-between">
                <h4>{{__('frontend.Shopping Cart')}}</h4>
                <div class="chart_close">
                    <i class="ti-close"></i>
                </div>
            </div>
            <div id="cartView">
                <div class="single_cart">
                    Loading...
                </div>
            </div>


        </div>
        <div class="view_checkout_btn d-flex justify-content-end gap_10 flex-wrap" style="display: none!important;">
            <a href="{{url('my-cart')}}"
               class="theme_btn small_btn3 flex-fill text-center">{{__('frontend.View cart')}}</a>
            <a href="{{route('myCart',['checkout'=>true])}}"
               class="theme_btn small_btn3 flex-fill text-center">{{__('frontend.Checkout')}}</a>
        </div>
    </div>
</div>
<!-- shoping_cart::end  -->

<!-- UP_ICON  -->
<div id="back-top" style="display: none;">
    <a title="Go to Top" href="#">
        <i class="ti-angle-up"></i>
    </a>
</div>

<input type="hidden" name="item_list" class="item_list" value="{{url('getItemList')}}">
<input type="hidden" name="enroll_cart" class="enroll_cart" value="{{url('enrollOrCart')}}">
<input type="hidden" name="csrf_token" class="csrf_token" value="{{csrf_token()}}">
<!--/ UP_ICON -->

@if($cookie)
    <style>
        .remove_cart {
            margin-left: -22px;
            margin-right: 8px;
            cursor: pointer;
        }

        .theme_cookies {
            background: {{@$cookie->bg_color}};
        }

        .theme_cookies .cookie_btn {
            background: {{$cookie->text_color}};
        }
    </style>


    <!-- theme_cookies::start  -->
    <div class="theme_cookies" style="display: none">
        <div class="theme_cookies_info flex-fill">
            <div class="icon">
                <img src="{{asset(@$cookie->image)}}" alt="">
            </div>
            <p>{!! @$cookie->details !!}</p>
        </div>
        <button type="button" class="cookie_btn" onclick="setCookies();">{{@$cookie->btn_text}}</button>
    </div>
    <!-- theme_cookies::end  -->
@endif

<!--ALL JS SCRIPTS -->

@if(isRtl())
    <script src="{{ asset('public') }}/js/popper.min.js"></script>
    <script src="{{ asset('public') }}/js/bootstrap.rtl.min.js"></script>
@else
    <script src="{{ asset('public') }}/js/bootstrap.bundle.min.js"></script>
@endif

<script src="{{ asset('public/frontend/infixlmstheme') }}/js/owl.carousel.min.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/waypoints.min.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/jquery.counterup.min.js"></script>
{{--<script src="{{ asset('public/frontend/infixlmstheme') }}/js/imagesloaded.pkgd.min.js"></script>--}}
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/wow.min.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/nice-select.min.js"></script>
{{--<script src="{{ asset('public/frontend/infixlmstheme') }}/js/barfiller.js"></script>--}}
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/jquery.slicknav.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/jquery.magnific-popup.min.js"></script>
{{--<script src="{{ asset('public/frontend/infixlmstheme') }}/js/jquery.ajaxchimp.min.js"></script>--}}
{{--<script src="{{ asset('public/frontend/infixlmstheme') }}/js/parallax.js"></script>--}}
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/mail-script.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/jquery.lazy.min.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/main.js"></script>
<script src="{{ asset('public/frontend/infixlmstheme') }}/js/footer.js"></script>
<script src="{{asset('public')}}/js/toastr.min.js"></script>
{!! Toastr::message() !!}
@yield('js')

<script>
    setTimeout(function () {
        $('.preloader').fadeOut('slow', function () {
            // $(this).remove();
            @if($cookie->allow)
            checkCookie();
            @endif
        });
    }, 0);

    function setCookies() {
        $('.theme_cookies').hide(500);
        var d = new Date();
        document.cookie = "allow=1; expires=Thu, 21 Dec " + (d.getFullYear() + 1) + " 12:00:00 UTC";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    function checkCookie() {
        var check = getCookie("allow");
        if (check != "") {
            $('.theme_cookies').hide();
        } else {
            $('.theme_cookies').show();
        }
    }

    $('#cartView').on('click', '.remove_cart', function () {
        let id = $(this).data('id');
        let total = $('.notify_count').html();

        $(this).closest(".single_cart").hide();
        let url = "{{url(('/home/removeItemAjax'))}}" + '/' + id;

        $.ajax({
            type: 'GET',
            url: url,
            success: function (data) {

                $('.notify_count').html(total - 1);
            }
        });
    });

    $(function () {
        $('.lazy').Lazy();
    });


</script>
@auth
    @if(Settings('device_limit_time')!=0)
        @if(\Illuminate\Support\Facades\Auth::user()->role_id==3)
            <script>
                function update() {
                    $.ajax({
                        type: 'GET',
                        url: "{{url('/')}}" + "/update-activity",
                        success: function (data) {


                        }
                    });
                }

                var intervel = "{{Settings('device_limit_time')}}"
                var time = (intervel * 60) - 20;

                setInterval(function () {
                    update();
                }, time * 1000);

            </script>
        @endif
    @endif
@endauth
<script>
    if ($('#main-nav-for-chat').length) {
    } else {
        $('#main-content').append('<div id="main-nav-for-chat" style="visibility: hidden;"></div>');
    }

    if ($('#admin-visitor-area').length) {
    } else {
        $('#main-content').append('<div id="admin-visitor-area" style="visibility: hidden;"></div>');
    }
</script>



@if(str_contains(request()->url(), 'chat'))
    <script src="{{asset('public/backend/js/jquery-ui.js')}}"></script>
    <script src="{{asset('public/backend/vendors/select2/select2.min.js')}}"></script>
    <script src="{{ asset('public/js/app.js') }}"></script>
    <script src="{{ asset('public/chat/js/custom.js') }}"></script>
@endif

@if(auth()->check() && auth()->user()->role_id == 3 && !str_contains(request()->url(), 'chat'))
    <script src="{{ asset('public/js/app.js') }}"></script>
@endif


@if(isModuleActive("WhatsappSupport"))
    <script src="{{ asset('public/whatsapp-support/scripts.js') }}"></script>

    @include('whatsappsupport::partials._popup')

    @endif


    </body>

    </html>
