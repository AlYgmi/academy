@extends(theme('layouts.dashboard_master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{__('frontendmanage.My Profile')}} @endsection
@section('css')
    <link href="{{asset('public/frontend/infixlmstheme/css/select2.min.css')}}" rel="stylesheet"/>
    <link href="{{asset('public/frontend/infixlmstheme/css/checkout.css')}}" rel="stylesheet"/>
    <link href="{{asset('public/frontend/infixlmstheme/css/myProfile.css')}}" rel="stylesheet"/>
@endsection
@section('js')
    <script src="{{asset('public/frontend/infixlmstheme/js/select2.min.js')}}"></script>
    <script src="{{ asset('public/frontend/infixlmstheme/js/my_profile.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('.select2').select2();
            $('.select2').css('width', '100%');
        });
        let city = $('#city');
        $('#select_country').on('change', function (e) {
            e.preventDefault();
            var country = this.value;
            $.ajax({
                type: 'GET',
                url: '{{route('ajaxCounterCity')}}',
                data: {id: country},
                success: function (data) {
                    city.empty();
                    $.each(data, function (k, v) {
                        $('<option>').val(v.id).text(v.name).appendTo(city);
                    });
                }
            });
        });
    </script>
@endsection

@section('mainContent')

    <x-my-profile-page-section/>

@endsection
