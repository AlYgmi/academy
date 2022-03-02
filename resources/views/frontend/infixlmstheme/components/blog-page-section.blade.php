<div>
    <div class="blog_page_wrapper">
        <div class="container">
            <div class="row">
                @if(isset($blogs))
                    @foreach($blogs as $blog)
                        <div class="col-lg-6">
                            <div class="single_blog">
                                <a href="{{route('blogDetails',[$blog->slug])}}">
                                    <div class="thumb">

                                        <div class="thumb_inner lazy"
                                             data-src="{{getBlogImage($blog->thumbnail)}}">
                                        </div>
                                    </div>
                                </a>
                                <div class="blog_meta">
                                    <span>{{$blog->user->name}} . {{ showDate(@$blog->authored_date ) }}</span>

                                    <a href="{{route('blogDetails',[$blog->slug])}}">
                                        <h4>{{$blog->title}}</h4>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            {{ $blogs->appends(Request::all())->links() }}
        </div>
    </div>
</div>
