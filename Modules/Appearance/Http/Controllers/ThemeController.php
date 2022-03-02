<?php

namespace Modules\Appearance\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Setting\Model\GeneralSetting;
use ZipArchive;
use App\Traits\UploadTheme;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Appearance\Entities\Theme;
use Modules\Appearance\Services\ThemeService;
use Modules\SystemSetting\Entities\GeneralSettings;

class ThemeController extends Controller
{
    use UploadTheme;

    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    public function index()
    {
        try {
            $activeTheme = $this->themeService->activeOne();
            $ThemeList = $this->themeService->getAllActive();
            return view('appearance::theme.index', compact('ThemeList', 'activeTheme'));
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function create()
    {
        try {
            return view('appearance::theme.components.create');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function active(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $this->themeService->isActive($request->only('id'), $request->id);

            $settings = GeneralSettings::first();
            $settings->frontend_active_theme = $this->themeService->showById($request->id)->name;
            $settings->save();

            $notification = array(
                'messege' => 'Theme Change Successfully.',
                'alert-type' => 'success'
            );

            $path = Storage::path('settings.json');
            file_put_contents($path, GeneralSetting::first());
            Cache::forget('frontend_active_theme');

            return redirect(route('appearance.themes.index'))->with($notification);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function store(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            if ($request->hasFile('themeZip')) {

                $dir = 'theme';
                if (!is_dir($dir))
                    mkdir($dir, 0777, true);

                $path = $request->themeZip->store('theme');

                $request->themeZip->getClientOriginalName();

                $zip = new ZipArchive;
                $res = $zip->open(storage_path('app/' . $path));

                $random_dir = Str::random(10);


                $dir = explode('/', $zip->getNameIndex(0))[0] ?? $zip->getNameIndex(0);

                if ($res === true) {
                    $zip->extractTo(storage_path('app/temp/' . $random_dir . '/theme'));
                    $zip->close();
                } else {
                    dd('could not open');
                }

                $str = @file_get_contents(storage_path('app/temp/') . $random_dir . '/theme/' . $dir . '/config.json', true);

                $json = json_decode($str, true);
                if (!empty($json['files'])) {
                    foreach ($json['files'] as $key => $directory) {
                        if ($key == 'asset_path') {
                            if (!is_dir($directory)) {
                                mkdir(base_path($directory), 0777, true);
                            }
                        }
                        if ($key == 'view_path') {
                            if (!is_dir($directory)) {
                                mkdir(base_path($directory), 0777, true);
                            }
                        }
                    }
                }

                // Create/Replace new files.
                if (!empty($json['files'])) {
                    foreach ($json['files'] as $key => $file) {

                        if ($key == 'asset_path') {
                            $src = base_path('storage/app/temp/' . $random_dir . '/theme' . '/' . $json['folder_path'] . '/asset');
                            $dst = base_path($file);
                            $this->recurse_copy($src, $dst);

                        }
                        if ($key == 'view_path') {
                            $src = base_path('storage/app/temp/' . $random_dir . '/theme' . '/' . $json['folder_path'] . '/view');
                            $dst = base_path($file);
                            $this->recurse_copy($src, $dst);
                        }
                    }
                }
                $alreadyHas = Theme::where('name', $json['name'])->first();

                if (!$alreadyHas) {
                    Theme::create([
                        'user_id' => Auth::user()->id,
                        'name' => $json['name'],
                        'title' => $json['title'],
                        'image' => $json['image'],
                        'version' => $json['version'],
                        'folder_path' => $json['folder_path'],
                        'live_link' => $json['live_link'],
                        'description' => $json['description'],
                        'is_active' => $json['is_active'],
                        'status' => $json['status'],
                        'tags' => $json['tags']
                    ]);
                }
            }
            if (is_dir('theme') || is_dir('temp')) {

                $this->delete_directory(storage_path('app/theme'));
                $this->delete_directory(storage_path('app/temp'));
            }


            Toastr::success("New Theme Upload Successfully.", 'Success');

            return redirect(route('appearance.themes.index'));
        } catch (Exception $e) {
            if (is_dir('theme') || is_dir('temp')) {

                $this->delete_directory(storage_path('app/theme'));
                $this->delete_directory(storage_path('app/temp'));
            }

            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function show($id)
    {
        try {
            $theme = $this->themeService->showById($id);
            return view('appearance::theme.components.show', compact('theme'));
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function destroy(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $this->themeService->delete($request->id);

            $notification = array(
                'messege' => 'Theme Deleted Successfully.',
                'alert-type' => 'success'
            );
            return redirect(route('appearance.themes.index'))->with($notification);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
