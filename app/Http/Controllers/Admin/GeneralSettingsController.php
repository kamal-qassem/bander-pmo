<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Input;
use Image;
use File;

class GeneralSettingsController extends Controller
{
    
    public function index()
    {
        if (! Gate::allows('general_setting_access')) {
            return prepareBlockUserMessage();
        }
        return view('admin.general_settings.index');
    }

    public function viewSettings($slug)
    {
        
        if (! Gate::allows('general_setting_access')) {
            return prepareBlockUserMessage();
        }

        $record                 = \App\Settings::where('slug', $slug)->get()->first();

        if (! $record ) {
        	return redirect()->back();
        }
        
        $settings_data = json_decode( $record->settings_data );

        
    	return view('admin.general_settings.sub-list', compact( 'record', 'settings_data' ) );
    }

    public function storeSubSettings(Request $request, $slug)
    {
       
      if (! Gate::allows('general_setting_access')) {
            return prepareBlockUserMessage();
        }

      $record  = \App\Settings::where('slug', $slug)->get()->first();
        
      if (! $record ) {
        	return prepareBlockUserMessage();
        }



        $validation_rules['key'] = 'bail|required|max:150';
        $validation_rules['type'] = 'bail|required';

        if($request->type=='file')
        {
            $validation_rules['value'] = 'bail|mimes:png,jpg,jpeg|max:2048';
        }

        if($request->type=='select')
        {
            $validation_rules['value'] = 'bail|required|integer';
        }

    	$this->validate($request, $validation_rules);


       if ($redirect = $this->check_isdemo()) {
            flashMessage( 'info', 'create', trans('custom.settings.crud_disabled') );
            return redirect()->back();
        }

       $settings_data = (array) json_decode($record->settings_data);
       
       $value = '';
     
       $processed_data = (object)$this->processSettingValue($request);
        
       $values = array(
                        'type'=>$request->type, 
                        'value'=>$processed_data->value, 
                        'extra'=>$processed_data->extra,
                        'tool_tip'=>$processed_data->tool_tip
                       );
       $settings_data[$request->key] = $values;
       $record->settings_data = json_encode($settings_data);
      
       $record->save();

       $this->savePaymentGateway();

       flashMessage('success', 'update');
       return redirect()->route( 'admin.master_settings.index' );
    }

    public function savePaymentGateway() {
        
		$payment_gateways = \App\Settings::where('moduletype', 'payment')->get();
        if ( ! empty( $payment_gateways ) ) {
            foreach ($payment_gateways as $payment_gateway) {
               $gateway = \App\PaymentGateway::firstOrNew( ['key' => $payment_gateway->key ] );
               $gateway->key = $payment_gateway->key;
               $gateway->name = $payment_gateway->module;
			   $gateway->status = $payment_gateway->status;
               $gateway->description = $payment_gateway->description;
               $gateway->save();
            }
        }
    }

     /**
     * This method is used to update the subsettings of the settings module
     * 
     * @param  Request $request [description]
     * @param  [type]  $slug    [description]
     * @return [type]           [description]
     */
    public function updateSubSettings(Request $request, $slug)
    {
         
     if (! Gate::allows('general_setting_access')) {
            return prepareBlockUserMessage();
        }
      $record                 = \App\Settings::where('slug', $slug)->get()->first();
    
       if (! $record ) {
        	flashMessage('danger','create', 'custom.settings.not_found');
        	return redirect()->back();
        }


        if ($this->check_isdemo()) {
            flashMessage( 'info', 'create', trans('custom.settings.crud_disabled') );
            return redirect()->back();
        }

    $input_data = Input::all();

    
 
    $extra = '';
    
    foreach ($input_data as $key => $value) {

            if($key=='_token' || $key=='_method' || $value=='')
                continue;
            $submitted_value = (object)$value;
            $value = 0;
            if(isset($submitted_value->value))
                $value = $submitted_value->value;
            
             $old_values = json_decode($record->settings_data);

             
            
            /**
             * For File type of settings, first check if the file is changed or not
             * If not changed just keep the old values as it is
             * If file changed, first upload the new file and delete the old file
             * @var [type]
             */
            if($submitted_value->type=='file')
            {
                if($request->hasFile($key)) {
                    $isNew = false;
                        $value = $this->processUpload($request, $key, $isNew);
                        
                         $this->deleteFile($old_values->$key->value, IMAGE_PATH_SETTINGS);
                }
                else
                {
                    $value = $old_values->$key->value;
                }
            }

            //*** File Answer type end **//

           if($submitted_value->type == 'select')
           {
                $extra = ! empty( $old_values->$key->extra ) ? $old_values->$key->extra : '';
           }
            
            $data[$key] = array('value'=>$value, 'type'=>$submitted_value->type, 'extra'=>$extra, 'tool_tip'=>$submitted_value->tool_tip);
           
        }	 
       
       
       $record->settings_data = json_encode($data);
       if(!env('DEMO_MODE')) {
       $record->save();

        if($this->isEnvSetting($request))
        {
            $data = $this->prepareEnvData($request);
          
            $this->updateEnvironmentFile($data);
        }
      }
       
    $this->savePaymentGateway();

    // Let us forget about previous data in session, so that it will fetch fresh data from database.
    session()->forget('settings');
    session()->forget('local_settings');

    flashMessage('success','record_updated');
    return redirect()->route('admin.master_settings.index');

    }

    /**
      * [check_isdemo description]
      * @return [type] [description]
      */
    public function check_isdemo()
    {
       if (env('DEMO_MODE'))
          return redirect()->route('admin.master_settings.index');
       else
          return false;
    }

    /**
     * This method finds the value of the setting type
     * The value may be of file or any single field entity
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function processSettingValue(Request $request)
    {
        $value = '';
        $extra = '';
        $tool_tip = '';

         if( $request->type == 'text'
            || $request->type=='number'
            || $request->type=='email'
            || $request->type=='textarea'
            || $request->type=='checkbox'
        ) {

            $value = 0;
        }
        if( $request->has('value') ) {
         $value = $request->value;
        }

        if ($request->type=='file') {
            if( $request->hasFile('value') ) {
                $value = $this->processUpload($request);
            }
        } elseif ($request->type=='select') {
            $extra = array();
            $value = '';
            $extra['total_options'] = $request->total_options;

            $options = [];
            for($index=0; $index<$request->total_options; $index++)
            {
                $options[$request->option_value[$index]] = $request->option_text[$index];
            }

            $extra['options'] = $options;
            $value = $request->option_value[$request->value];
        }

        $tool_tip = $request->tool_tip;
        
        return array( 'value' => $value, 'extra' => $extra, 'tool_tip' => $tool_tip );
    }


    /**
     * This method verifies if the request is the type of enverionment varable
     * @param  Request $request [description]
     * @return boolean          [description]
     */
    public function isEnvSetting(Request $request)
    {
         $env_keys = array( 'site_title',
                            'system_timezone',
                            'facebook_client_id',
                            'facebook_client_secret',
                            'facebook_redirect_url',
                            'google_client_id',
                            'google_client_secret',
                            'google_redirect_url',
                            'payu_merchant_key',
                            'payu_salt',
                            'payu_working_key',
                            'payu_testmode',
                            'mail_driver',
                            'mail_host',
                            'mail_port',
                            'mail_username',
                            'mail_password',
                            'mail_encryption',
                            'stripe_key',
                            'stripe_secret',
                            'sms_driver',
                            'plivo_auth_id',
                            'plivo_auth_token',
                            'twilio_sid',
                            'twilio_token',
                            'date_format'
                            );

        foreach ($env_keys as $key => $value) 
        {
            if($request->has($value))            
                return TRUE;
        } 

        return FALSE;       
    }

    /**
     * [prepareEnvData description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function prepareEnvData(Request $request)
    {
        $request_data = Input::all();
        $data = array();

        foreach ($request_data as $key => $value) {
            if($key=='_token' || $key=='_method' || $value=='')
                continue;
            if(isset($value['value'])) {
                $data[strtoupper($key)] = $value['value'];
                if ( 'date_format' === $key ) {
                    $data[ strtoupper($key) ] = str_replace(['dd', 'mm', 'yyyy'], ['d', 'm', 'Y'], $value['value']);
                    $data[ strtoupper($key) . '_MOMENT' ] = str_replace(['dd', 'mm', 'yyyy'], ['DD', 'MM', 'YYYY'], $value['value']);
                }
            }
        }
        return $data;
    }

    /**
     * This method updates the Environment File which contains all master settings
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function updateEnvironmentFile($data = array())
    {
      if(count($data)>0) {
       $env = file_get_contents(base_path() . '/.env');
       $env = preg_split('/\s+/', $env);
       
        foreach((array)$data as $key => $value){

                // Loop through .env-data
                foreach($env as $env_key => $env_value){

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if($entry[0] == $key){
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }
             $env = implode("\n", $env);
              file_put_contents(base_path() . '/.env', $env);
              return TRUE;
            }
            else
            {
              return FALSE;
            }

    }

     /**
     * [processUpload description]
     * @param  Request $request [description]
     * @param  string  $sfname  [description]
     * @param  boolean $isNew   [description]
     * @return [type]           [description]
     */
    public function processUpload(Request $request, $sfname='value', $isNew = true)
    {
        
         if ($request->hasFile($sfname)) {
          
          $imageObject = new \App\ImageSettings();
          
          $destinationPath      = public_path() . $imageObject->getSettingsImagePath();
          
          $random_name = str_random(15);
          $fileName = '';
          if($isNew){
              $path = $_FILES[$sfname]['name'];
          $ext = pathinfo($path, PATHINFO_EXTENSION);

       
              $fileName = $random_name.'.'.$ext; 
              $request->file($sfname)->move($destinationPath, $fileName);
          }
          else {
              
              $path = $_FILES[$sfname]['name'];
        
              $ext = pathinfo($path['value'], PATHINFO_EXTENSION);

            $fileName = $random_name.'.'.$ext;
            
            move_uploaded_file($_FILES[$sfname]['tmp_name']['value'], $destinationPath.$fileName);
        }
          
          return $fileName;
 
        }
     }

     /**
     * [deleteFile description]
     * @param  [type]  $record   [description]
     * @param  [type]  $path     [description]
     * @param  boolean $is_array [description]
     * @return [type]            [description]
     */
    public function deleteFile($record, $path, $is_array = FALSE)
    {
        $imageObject = new \App\ImageSettings();
        $destinationPath      = public_path() . $imageObject->getSettingsImagePath();
        $files = array();
        $files[] = $destinationPath.$record;
        File::delete($files);
    }

    public function addSubSettings($slug)
    {

        if (! Gate::allows('general_setting_access')) {
            return prepareBlockUserMessage();
        }
        
        $record               = \App\Settings::where('slug', $slug)->get()->first();
        
        
        if ( ! $record ) {
            return redirect()->back();
        }
        $data['record']             = $record;
        $data['active_class']       = 'master_settings';
        $data['title']              = get_text($record->key);
        
        return view('admin.general_settings.sub-list-add-edit', $data);
    }
}
