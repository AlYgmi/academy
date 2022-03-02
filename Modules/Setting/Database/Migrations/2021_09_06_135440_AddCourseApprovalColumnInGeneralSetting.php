<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;
use Modules\Setting\Model\GeneralSetting;

class AddCourseApprovalColumnInGeneralSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'course_approval')) {
                $table->integer('course_approval')->nullable();
            }
        });

        $path = Storage::path('settings.json');
        file_put_contents($path, GeneralSetting::first());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
