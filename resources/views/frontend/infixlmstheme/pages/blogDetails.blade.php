@extends(theme('layouts.master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{$blog->title??''}} @endsection
@section('css') @endsection
@section('js') @endsection
@section('og_image'){{asset($blog->image)}}@endsection
@section('mainContent')

    <x-blog-details-page-section :blog="$blog"/>

@endsection
