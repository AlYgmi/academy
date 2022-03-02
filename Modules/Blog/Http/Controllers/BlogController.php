<?php

namespace Modules\Blog\Http\Controllers;


use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Modules\Blog\Entities\Blog;


class BlogController extends Controller
{


    /**
     * Display a listing of the resource.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {

        try {
            $user = Auth::user();
            $query = Blog::with('user');
            if ($user->role_id != 1) {
                $query->where('user_id', $user->id);
            }

            $blogs = $query->latest()->get();

            return view('blog::index', compact('blogs'));

        } catch (\Exception $e) {

            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        //return view('blog::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'title' => 'required|unique:blogs,title',
            'slug' => 'required|unique:blogs,slug',
            'description' => 'required',
            'image' => 'required',
        ];
        $this->validate($request, $rules, validationMessage($rules));

        try {
            $blog = new Blog;
            $blog->title = $request->title;
            $blog->slug = $request->slug;
            $blog->description = $request->description;
            $blog->user_id = Auth::id();
            $blog->authored_date = !empty($request->publish_date) ? $request->publish_date : date('m/d/y');

            if ($request->image) {

                $strpos = strpos($request->image, ';');
                $sub = substr($request->image, 0, $strpos);
                $name = md5($request->title . rand(0, 1000)) . '.' . 'png';
                $img = Image::make($request->image);
                $upload_path = 'public/uploads/blogs/';
                $img->save($upload_path . $name);
                $blog->image = 'public/uploads/blogs/' . $name;

                $strpos = strpos($request->image, ';');
                $sub = substr($request->image, 0, $strpos);
                $name = md5($request->title . rand(0, 1000)) . '.' . 'png';
                $img = Image::make($request->image);
                $upload_path = 'public/uploads/blogs/';
                $img->save($upload_path . $name);
                $blog->thumbnail = 'public/uploads/blogs/' . $name;


            }
            $blog->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('blog::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('blog::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        $rules = [
            'title' => 'required|unique:blogs,title,' . $request->id,
            'slug' => 'required|unique:blogs,slug,' . $request->id,
            'description' => 'required',
            'id' => 'required',

        ];
        $this->validate($request, $rules, validationMessage($rules));


        try {
            if (appMode()) {
                Toastr::success('In demo version you cant not change this!', 'Success');
                return redirect()->back();

            }

            $blog = Blog::findOrFail($request->id);
            $blog->title = $request->title;
            $blog->slug = $request->slug;
            $blog->description = $request->description;
            $blog->user_id = Auth::id();
            $blog->authored_date = !empty($request->publish_date) ? $request->publish_date : date('m/d/y');


            if ($request->image) {


                $name = md5($request->title . rand(0, 1000)) . '.' . 'png';
                $img = Image::make($request->image);
                $upload_path = 'public/uploads/blogs/';
                $img->save($upload_path . $name);
                $blog->image = 'public/uploads/blogs/' . $name;


                $name = md5($request->title . rand(0, 1000)) . '.' . 'png';
                $img = Image::make($request->image);
                $upload_path = 'public/uploads/blogs/';
                $img->save($upload_path . $name);
                $blog->thumbnail = 'public/uploads/blogs/' . $name;


            }


            $blog->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    /**
     * Remove the specified resource from storage.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'id' => 'required',
        ];
        $this->validate($request, $rules, validationMessage($rules));

        $blog = Blog::findOrFail($request->id);

        try {
            $blog->delete();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }
}
