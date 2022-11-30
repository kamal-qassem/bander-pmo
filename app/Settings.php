<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = "master_settings";

    public static function getRecordWithSlug($slug)
    {
        return Settings::where('slug', '=', $slug)->first();
    }

    public static function getLocalSetting($key, $setting_module)
    {
        return getLocalSetting( $setting_module, $key );
    }


    /**
     * This method validates and sends the setting value
     * @param  [type] $setting_type [description]
     * @param  [type] $key          [description]
     * @return [type]               [description]
     */
    public static function getSetting($key, $setting_module)
    {
        $setting_module     = strtolower($setting_module);

        if ( 'local_settings' == $setting_module ) {
            return Settings::getLocalSetting($key, $setting_module);
        }
        
        return Settings::isSettingAvailable($key, $setting_module);
    }

    /**
     * This method validates and sends the setting value
     * @param  [type] $setting_type [description]
     * @param  [type] $key          [description]
     * @return [type]               [description]
     */
    public static function getSettings( $setting_module, $submodule = '' )
    {
        $setting_module     = strtolower($setting_module);
        $settings =(array) json_decode(session('settings'));
        
        if ( empty( $settings ) ) {
            Settings::loadSettingsModule( $setting_module );
            $settings =(array) json_decode(session('settings'));
        }

        if( array_key_exists($setting_module, $settings) ) {
            if ( ! empty( $submodule ) ) {
                if ( ! empty( $settings[ $setting_module ][ $submodule ] ) ) {
                    return $settings[ $setting_module ][ $submodule ];
                } else {
                    return 'invalid_setting';
                }
            } else {
                return $settings[ $setting_module ];
            }
        } else {
            Settings::loadSettingsModule( $setting_module );
            $settings =(array) json_decode(session('settings'));
            return $settings[ $setting_module ];
        }
        return 'invalid_setting';
    }

    /**
     * This method finds the key is available in module or not
     * If available, It will return the value of that setting_module[key]
     * If not available, it will fetch from db and stores in session and returns the value
     * @param  [type]  $key            [description]
     * @param  [type]  $setting_module [description]
     * @return boolean                 [description]
     */
    public static function isSettingAvailable($key, $setting_module)
    {
      $settings =(array) json_decode(session('settings'));


      /**
       * Check if key exists in specified module settings data
       * If not exists return invalid setting
       */
      if(!array_key_exists($setting_module, $settings)) {

            if(!Settings::loadSettingsModule($setting_module))
            {
                return 'invalid_setting';
            }

         $settings =(array) json_decode(session('settings'));
        }

        $sub_settings = (array) $settings[$setting_module];



        if(!array_key_exists($key, $sub_settings))
        {
            return 'invalid_setting';
        }
            return $sub_settings[$key]->value;
    }

    /**
     * This method fetches the setting module and 
     * Get the record with the sent key from DB
     * Validate the record, if not valid return false
     * Append the record to existing setting varable
     * @param  [type] $setting_module [description]
     * @return [type]                 [description]
     */
    public static function loadSettingsModule($setting_module)
    {

        $setting_record = Settings::where('key', $setting_module)->first();
        
        $data = [];

        if ( $setting_record ) {
            $data = json_decode($setting_record->settings_data);
        }
        $global_settings =(array) json_decode(session('settings'));
        if ( isset( $global_settings[ $setting_module ] ) ) {
            unset( $global_settings[ $setting_module ] );
        }

        $global_settings[ $setting_module ] = $data;
        
        session()->put('settings', json_encode($global_settings));
        
        
        return TRUE;

    }


    /**
     * [getSettingRecord description]
     * @param  [type] $setting_module [description]
     * @return [type]                 [description]
     */
    public static function getSettingRecord($setting_module)
    {
       $setting_module = Settings::where('key', '=', $setting_module)->first();
       if ($setting_module)
       $setting_module = json_decode($setting_module->settings_data);
        
       return $setting_module;
    } 
}
