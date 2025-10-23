<?php
// Mohammad Hassan
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\BusinessSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the setting already exists
        $existingSetting = BusinessSetting::where('type', 'preorder_system_activation')->first();
        
        if (!$existingSetting) {
            // Create the preorder system activation setting
            $setting = new BusinessSetting();
            $setting->type = 'preorder_system_activation';
            $setting->value = '1'; // Enable by default
            $setting->lang = null;
            $setting->save();
        } else {
            // Update existing setting to ensure it's enabled
            $existingSetting->value = '1';
            $existingSetting->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the preorder system activation setting
        BusinessSetting::where('type', 'preorder_system_activation')->delete();
    }
};