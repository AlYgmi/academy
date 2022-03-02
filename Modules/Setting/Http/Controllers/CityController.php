<?php

namespace Modules\Setting\Http\Controllers;

use App\Country;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{

    public function index(Request $request)
    {
        if ($request->country) {
            $country_search = $request->country;
        } else {
            $country_search = '';
        }
        if ($request->name) {
            $city_search = $request->name;
        } else {
            $city_search = '';
        }

        $countries = Country::all();
        $query = DB::table('spn_cities')
            ->select('spn_cities.name', 'spn_cities.id', 'countries.name as countryName')
            ->join('countries', 'spn_cities.country_id', 'countries.id');

        if ($request->country) {
            $query->where('spn_cities.country_id', $request->country);
        }

        if ($request->name) {
            $query->where('spn_cities.name', 'LIKE', '%' . $request->name . '%');
        }
        $cities = $query->paginate(20);
        return view('setting::city.index', [
            "cities" => $cities,
            "countries" => $countries,
            "country_search" => $country_search,
            "city_search" => $city_search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            DB::table('spn_cities')->insert([
                'name' => $request->name,
                'country_id' => $request->country,
            ]);
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function update(Request $request, $id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $city = DB::table('spn_cities')
                ->where('id', $id)
                ->update([
                    'country_id' => $request->country,
                    'name' => $request->name,
                ]);
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function destroy($id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            DB::table('spn_cities')->where('id', $id)->delete();
            Toastr::success(__('setting.City has been deleted Successfully'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function edit_modal(Request $request)
    {
        try {
            $city = DB::table('spn_cities')->where('id', $request->id)->first();
            $countries = Country::all();
            return view('setting::city.edit_modal', [
                "city" => $city,
                "countries" => $countries
            ]);
        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return false;
        }
    }
}
