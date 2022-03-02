@extends('backend.master')
@section('mainContent')
    @include("backend.partials.alertMessage")
    <style>
        .page-item.active .page-link {
            background: linear-gradient(
                90deg, #7c32ff 0%, #c738d8 100%);
        }
    </style>
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1> {{ __('setting.City List') }}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('common.Dashboard')}}</a>
                    <a href="#">{{__('setting.Setting')}}</a>
                    <a class="active" href="#"> {{ __('setting.City List') }}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="white_box mb_30">
                        <div class="white_box_tittle list_header">
                            <h4>{{__('courses.Advanced Filter')}} </h4>
                        </div>
                        <form action="{{route('city.index')}}" method="GET">
                            <div class="row">

                                <div class="col-lg-5 mt-10">

                                    <label class="primary_input_label" for="country">{{__('common.Country')}}</label>
                                    <select class="primary_select" name="country" id="country">
                                        <option data-display="{{__('common.Select')}} {{__('common.Country')}}"
                                                value="">{{__('common.Select')}} {{__('common.Country')}}</option>
                                        @foreach($countries as $country)
                                            <option
                                                value="{{$country->id}}" {{isset($country_search)?$country_search==$country->id?'selected':'':''}}>{{@$country->name}} </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-5 mt-10">
                                    <label class="primary_input_label" for="category">{{__('common.Name')}}</label>
                                    <input name="name" class="primary_input_field name" placeholder="City Name"
                                           value="{{$city_search}}"
                                           type="text">

                                </div>


                                <div class="col-lg-2 mt-50">
                                    <div class="search_course_btn text-right">
                                        <button type="submit"
                                                class="primary-btn radius_30px mr-10 fix-gr-bg">{{__('courses.Filter')}} </button>
                                    </div>
                                </div>
                            </div>
                            {{--                            <input type="hidden" name="page" value="{{isset($_GET['page'])?$_GET['page']:1}}">--}}
                        </form>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="box_header common_table_header">
                                <div class="main-title d-md-flex">
                                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('setting.City List') }}</h3>
                                    <ul class="d-flex">

                                        <li><a data-toggle="modal" class="primary-btn radius_30px mr-10 fix-gr-bg"
                                               href="#" onclick="open_add_city_modal()"><i
                                                    class="ti-plus"></i>{{ __('common.Add New') }} {{ __('common.City') }}
                                            </a></li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <!-- table-responsive -->
                            <div class="">
                                <table id="lms_table" class="table  ">
                                    <thead>
                                    <tr>
                                        <th scope="col">{{ __('common.SL') }}</th>
                                        <th scope="col">{{ __('common.Name') }}</th>
                                        <th scope="col">{{ __('common.Country') }}</th>

                                        <th scope="col">{{ __('common.Action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($cities)==0)
                                        <tr>
                                            <td colspan="4" class="text-center">No Data Found</td>
                                        </tr>
                                    @endif
                                    @foreach($cities as $key=>$city)
                                        <tr>
                                            <th>{{ $key+1 }}</th>
                                            <td>{{ $city->name }}</td>
                                            <td>{{ $city->countryName }}</td>

                                            <td>
                                                <!-- shortby  -->
                                                <div class="dropdown CRM_dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                                            id="dropdownMenu2" data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false">
                                                        {{ __('common.Select') }}
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right"
                                                         aria-labelledby="dropdownMenu2">

                                                        <a href="#" data-toggle="modal" data-target="#Item_Edit"
                                                           class="dropdown-item edit_brand"
                                                           onclick="edit_city_modal({{ $city->id }})">{{__('common.Edit')}}</a>


                                                        <a onclick="confirm_modal('{{route('city.destroy', $city->id)}}');"
                                                           class="dropdown-item edit_brand">{{__('common.Delete')}}</a>

                                                    </div>
                                                </div>
                                                <!-- shortby  -->
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>

                            </div>
                            <div class="mt-3">
                                {{ $cities->appends(Request::all())->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="edit_form">

    </div>
    <div id="add_city_modal">
        <div class="modal fade admin-query" id="city_add">
            <div class="modal-dialog modal_800px modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('common.Add New') }} {{ __('common.City') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <i class="ti-close "></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form action="{{ route('city.store') }}" method="POST" id="city_addForm">
                            @csrf
                            <div class="row">

                                <div class="col-xl-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label" for="">{{ __('common.Country') }} <strong
                                                class="text-danger">*</strong></label>
                                        <select class="primary_select" name="country" id="country">
                                            <option data-display="{{__('common.Select')}} {{__('common.Country')}}"
                                                    value="">{{__('common.Select')}} {{__('common.Country')}}</option>
                                            @foreach($countries as $country)
                                                <option
                                                    value="{{$country->id}}">{{@$country->name}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label" for="">{{ __('common.Name') }} <strong
                                                class="text-danger">*</strong></label>
                                        <input name="name" class="primary_input_field name" placeholder="City Name"
                                               type="text" required>
                                    </div>
                                </div>

                                <div class="col-lg-12 text-center">
                                    <div class="d-flex justify-content-center pt_20">
                                        <button type="submit" class="primary-btn semi_large2  fix-gr-bg"
                                                id="save_button_parent"><i
                                                class="ti-check"></i>{{ __('common.Save') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="city_edit" class="city_edit" value="{{ route('city.edit_modal') }}">

    @include('backend.partials.delete_modal')
@endsection
@push('scripts')
    <script src="{{asset('public/backend/js/city.js')}}"></script>
@endpush
