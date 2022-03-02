<div class="header_iner d-flex justify-content-between align-items-center">
    <div class="sidebar_icon d-lg-none">
        <i class="ti-menu"></i>
    </div>
    <div class="category_search d-flex category_box_iner">

        <div class="input-group-prepend2">
            <a href="#" class="categories_menu">
                <i class="fas fa-th"></i>
                {{__('courses.Category')}}
            </a>

            <div class="menu_dropdown">
                <ul>
                    @if(isset($categories))
                        @foreach($categories as $category)
                            <li class="mega_menu_dropdown active_menu_item">
                                <a href="{{route('categoryCourse',[$category->id,$category->slug])}}">{{$category->name}}</a>
                                @if(isset($category->activeSubcategories))
                                    @if(count($category->activeSubcategories)!=0)
                                        <ul>
                                            <li>
                                                <div class="menu_dropdown_iner d-flex">
                                                    <div class="single_menu_dropdown">
                                                        <h4>{{__('courses.Sub Category')}}</h4>
                                                        <ul>
                                                            @if(isset($category->activeSubcategories))
                                                                @foreach( $category->activeSubcategories as $subcategory)
                                                                    <li>
                                                                        <a href="{{route('subCategory.course',[$subcategory->id,$subcategory->slug])}}">{{$subcategory->name}}</a>
                                                                    </li>
                                                                @endforeach
                                                            @endif
                                                        </ul>
                                                    </div>

                                                </div>
                                            </li>
                                        </ul>
                                    @endif
                                @endif
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
        <form action="{{route('search')}}">
            <div class="input-group theme_search_field ">
                <div class="input-group-prepend">
                    <button class="btn" type="button" id="button-addon1"><i
                            class="ti-search"></i>
                    </button>
                </div>

                <input type="text" class="form-control" name="query"
                       placeholder="{{__('frontend.Search for course, skills and Videos')}}"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = '{{__('frontend.Search for course, skills and Videos')}}'">

            </div>
        </form>
    </div>
    <div class="d-flex align-items-center">
        <div class="notification_wrapper" id="main-nav-for-chat">
            <ul>
                <li>
                    <a href="{{route('myWishlists')}}">
                        <div class="notify_icon">
                            <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/heart.svg" alt="">
                        </div>
                        <span class="notify_count">{{@totalWhiteList()}}</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="cart_store">
                        <div class="notify_icon">
                            <img class="" src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/cart.svg" alt="">
                        </div>
                        <span class="notify_count ">{{@cartItem()}}</span>
                    </a>
                </li>

                @if(isModuleActive('Chat'))
                    <li class="scroll_notification_list">
                        @if(env('BROADCAST_DRIVER') == null)
                            <jquery-notification-component
                                :loaded_unreads="{{ json_encode($notifications_for_chat) }}"
                                :user_id="{{ json_encode(auth()->id()) }}"
                                :redirect_url="{{ json_encode(route('chat.index')) }}"
                                :check_new_notification_url="{{ json_encode(route('chat.notification.check')) }}"
                                :mark_all_as_read_url="{{ json_encode(route('chat.notification.allRead')) }}"
                                :asset_type="{{ json_encode('/public') }}"
                            ></jquery-notification-component>
                        @else
                            <notification-component
                                :loaded_unreads="{{ json_encode($notifications_for_chat) }}"
                                :user_id="{{ json_encode(auth()->id()) }}"
                                :redirect_url="{{ json_encode(route('chat.index')) }}"
                                :mark_all_as_read_url="{{ json_encode(route('chat.notification.allRead')) }}"
                                :asset_type="{{ json_encode('/public') }}"
                            ></notification-component>
                        @endif
                    </li>
                @endif
            </ul>
        </div>
        <div class="profile_info collaps_part">
            <div class="profile_img collaps_icon     d-flex align-items-center">
                <div class="studentProfileThumb"
                     style="background-image: url('{{getProfileImage(Auth::user()->image)}}')"></div>

                <span class="">{{Auth::user()->name}}
                    <br style="display: block">
          <small>
                        @if(Auth::user()->balance==0)
                  {{Settings('currency_symbol') ??'৳'}} 0
              @else
                  {{getPriceFormat(Auth::user()->balance)}}
              @endif
          </small>

                </span>

            </div>
            <div class="profile_info_iner collaps_part_content">
                <a href="{{url('/')}}">{{__('frontendmanage.Home')}}</a>
                <a href="{{route('myProfile')}}">{{__('frontendmanage.My Profile')}}</a>
                <a href="{{route('myAccount')}}">{{__('frontend.Account Settings')}}</a>
                <a href="{{route('logout')}}">{{__('frontend.Log Out')}}</a>
            </div>
        </div>
    </div>
</div>
