<?php

namespace Modules\ModulesManagement\Http\Controllers\Admin;

use Modules\ModulesManagement\Entities\ModulesManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\ModulesManagement\Http\Requests\Admin\StoreModulesManagementsRequest;
use Modules\ModulesManagement\Http\Requests\Admin\UpdateModulesManagementsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Artisan;

use File;
use Storage;
use Zip;
use Input;
use Image;
use Validator;

class ModulesManagementsController extends Controller
{
    /**
     * Display a listing of ModulesManagement.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('modules_management_access')) {
            return prepareBlockUserMessage();
        }

        if (request()->ajax()) {
            $query = ModulesManagement::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {                
                if (! Gate::allows('modules_management_delete')) {
                    return prepareBlockUserMessage();
                }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'modules_managements.id',
                'modules_managements.name',
                'modules_managements.slug',
                'modules_managements.type',
                'modules_managements.enabled',
                'modules_managements.description',
                'modules_managements.can_inactive',
            ]);
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'modules_management_';
                $routeKey = 'admin.modules_managements';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('slug', function ($row) {
                return $row->slug ? $row->slug : '';
            });
            $table->editColumn('type', function ($row) {
                return $row->type ? $row->type : '';
            });
            $table->editColumn('enabled', function ($row) {
                return $row->enabled ? $row->enabled : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('modulesmanagement::admin.modules_managements.index');
    }

    /**
     * Display a listing of ModulesManagement.
     *
     * @return \Illuminate\Http\Response
     */
    public function availablePlugins()
    {
        if (! Gate::allows('modules_management_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = ModulesManagement::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('modules_management_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'modules_managements.id',
                'modules_managements.name',
                'modules_managements.slug',
                'modules_managements.type',
                'modules_managements.enabled',
                'modules_managements.description',
            ]);
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'modules_management_';
                $routeKey = 'admin.modules_managements';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('slug', function ($row) {
                return $row->slug ? $row->slug : '';
            });
            $table->editColumn('type', function ($row) {
                return $row->type ? $row->type : '';
            });
            $table->editColumn('enabled', function ($row) {
                return $row->enabled ? $row->enabled : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('modulesmanagement::admin.modules_managements.availableplugins');
    }

    /**
     * Show the form for creating new ModulesManagement.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('modules_management_create')) {
            return prepareBlockUserMessage();
        }        
        $enum_type = ModulesManagement::$enum_type;
        if ( ! config('app.debug') ) {
            unset( $enum_type['Core'] );
        } 
        $enum_enabled = ModulesManagement::$enum_enabled;
            
        return view('modulesmanagement::admin.modules_managements.create', compact('enum_type', 'enum_enabled'));
    }

    public function store(Request $request)
    {
        if (! Gate::allows('modules_management_create')) {
            return prepareBlockUserMessage();
        }

        $rules = [
            'name' => 'required|unique:modules_managements,name',
            'slug' => 'required|unique:modules_managements,slug',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ( ! $validator->passes() ) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $plugin = ModulesManagement::create($request->all());

        // Let us update available plugins session variable.
        updatePlugins();

        flashMessage( 'success', 'create' );
        return redirect()->route('admin.modules_managements.index');
    }

    /**
     * Show the form for creating new ModulesManagement.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload()
    {
        if (! Gate::allows('modules_management_upload')) {
            return prepareBlockUserMessage();
        }
            
        return view('modulesmanagement::admin.modules_managements.upload');
    }

    /**
     * Store a newly created ModulesManagement in storage.
     *
     * @param  \App\Http\Requests\StoreModulesManagementsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadStore(StoreModulesManagementsRequest $request)
    {
        if (! Gate::allows('modules_management_upload')) {
            return prepareBlockUserMessage();
        }

        $is_valid = Zip::check($request->plugin);

        if ( ! $is_valid ) {
            flashMessage( 'danger', 'create', trans('modulesmanagement::global.modules-management.not-valid-zip'));
            return back();
        }

        $zip = Zip::open( $request->plugin );

        $files = $zip->listFiles();

        $is_valid = false;
        $file_path = '';
        if ( ! empty( $files ) ) {
            foreach ($files as $key => $value) {
                if (strpos($value, 'module.json') !== false) {
                    $is_valid = true;
                    $file_path = $value;
                }
            }
        }

        if ( ! $is_valid ) {
            flashMessage( 'danger', 'create', trans('modulesmanagement::global.modules-management.not-valid-plugin'));
            return back();
        }

        $imageObject = new \App\ImageSettings();   
        $destinationPath      = public_path() . $imageObject->getSettingsImagePath();

        $zip->extract($destinationPath . 'module.json', $file_path);

        $themejson = File::get($destinationPath . 'module.json/' . $file_path );

        $details = json_decode( $themejson, true );

        if ( ! empty( $details ) ) {
            if ( empty( $details['name'])) {
                flashMessage( 'danger', 'create', trans('modulesmanagement::global.modules-management.not-valid-plugin'));
                return back();
            } else {
                $check = ModulesManagement::where('slug', '=', $details['name'])->first();
                if ( $check ) {
                    flashMessage( 'danger', 'create', trans('modulesmanagement::global.modules-management.plugin-already-exists'));
                    return back();
                } else {
                    
                    $zip->extract( config('modules.paths.modules') );

                    $zip->close();                    

                    $data = array(
                        'name' => $details['name'],
                        'slug' => $details['name'],
                        'type' => 'Custom',
                        'enabled' => 'No',
                        'description' => $details['description'],
                        'settings_data' => ! empty( $details['settings_data'] ) ? json_encode( $details['settings_data'] ) : NULL,
                    );
                    

                    Artisan::call('module:seed', [ 'module' => $details['name'] ]);
                    
                    Artisan::call('module:disable', [ 'module' => $details['name'] ]);

                    flashMessage( 'success', 'create', trans('modulesmanagement::global.modules-management.plugin-install-success'));
                }
            }
        } else {
            flashMessage( 'danger', 'create', trans('modulesmanagement::global.modules-management.not-valid-plugin'));
            return back();
        }

        // Let us update available plugins session variable.
        updatePlugins();

        return redirect()->route('admin.modules_managements.index');
    }


    /**
     * Show the form for editing ModulesManagement.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('modules_management_edit')) {
            return prepareBlockUserMessage();
        }        $enum_type = ModulesManagement::$enum_type;
                    $enum_enabled = ModulesManagement::$enum_enabled;
            
        $modules_management = ModulesManagement::findOrFail($id);

        return view('modulesmanagement::admin.modules_managements.edit', compact('modules_management', 'enum_type', 'enum_enabled'));
    }

    /**
     * Update ModulesManagement in storage.
     *
     * @param  \App\Http\Requests\UpdateModulesManagementsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateModulesManagementsRequest $request, $id)
    {
        if (! Gate::allows('modules_management_edit')) {
            return prepareBlockUserMessage();
        }
        $modules_management = ModulesManagement::findOrFail($id);
        $modules_management->update($request->all());

        // Let us update available plugins session variable.
        updatePlugins();

        flashMessage( 'success', 'update' );
        return redirect()->route('admin.modules_managements.index');
    }


    /**
     * Display ModulesManagement.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('modules_management_view')) {
            return prepareBlockUserMessage();
        }
        $modules_management = ModulesManagement::findOrFail($id);

        return view('modulesmanagement::admin.modules_managements.show', compact('modules_management'));
    }


    /**
     * Remove ModulesManagement from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('modules_management_delete')) {
            return prepareBlockUserMessage();
        }
        $modules_management = ModulesManagement::findOrFail($id);

        if( File::exists(config('modules.paths.modules') . '/' . $modules_management->name) ) {
            Artisan::call('module:disable', [ 'module' => $modules_management->slug ]);
        }

        $modules_management->delete();

        // Let us update available plugins session variable.
        updatePlugins();

        flashMessage( 'success', 'delete' );
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
    }

    /**
     * Delete all selected ModulesManagement at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('modules_management_delete')) {
            return prepareBlockUserMessage();
        }
        if ($request->input('ids')) {
            $entries = ModulesManagement::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
                if( File::exists(config('modules.paths.modules') . '/' . $entry->name) ) {
                    Artisan::call('module:disable', [ 'module' => $entry->name ]);
                }
            }

            flashMessage( 'success', 'deletes' );
        }

        // Let us update available plugins session variable.
        updatePlugins();
    }


    /**
     * Restore ModulesManagement from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('modules_management_delete')) {
            return prepareBlockUserMessage();
        }
        $modules_management = ModulesManagement::onlyTrashed()->findOrFail($id);

        $modules_management->restore();

        // Let us update available plugins session variable.
        updatePlugins();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete ModulesManagement from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('modules_management_delete')) {
            return prepareBlockUserMessage();
        }
        $modules_management = ModulesManagement::onlyTrashed()->findOrFail($id);

        if( File::exists(config('modules.paths.modules') . '/' . $modules_management->name) ) {
            File::deleteDirectory(config('modules.paths.modules') . '/' . $modules_management->name);
        }

        $modules_management->forceDelete();

        // Let us update available plugins session variable.
        updatePlugins();

        flashMessage( 'success', 'delete');

        return back();
    }

    public function changeStatus( $id )
    {
        if (! Gate::allows('modules_management_changestatus')) {
            return prepareBlockUserMessage();
        }

        $modules_management = ModulesManagement::withTrashed()->find($id);
        if (! $modules_management) {
            flashMessage('danger', 'not_found');
            return back();
        }

        if( File::exists(config('modules.paths.modules') . '/' . $modules_management->name) ) {
            if ( 'Yes' === $modules_management->enabled ) {
                $modules_management->enabled = 'No';
                Artisan::call('module:disable', [ 'module' => $modules_management->slug ]);            
            } else {
                $modules_management->enabled = 'Yes';
                Artisan::call('module:enable', [ 'module' => $modules_management->slug ]);
            }
            $modules_management->save();
        } else {
            if ( 'Yes' === $modules_management->enabled ) {
                $modules_management->enabled = 'No';
            } else {
                $modules_management->enabled = 'Yes';
            }
            $modules_management->save();
        }

        // Let us update available plugins session variable.
        
        session()->forget('plugins');
        
        flashMessage('success', 'create', trans( 'modulesmanagement::global.modules-management.status-changed'));
        return redirect()->route('admin.modules_managements.index');
    }

    public function addSubSettings($slug)
    {

        if (! Gate::allows('modules_management_changestatus')) {
            return prepareBlockUserMessage();
        }
        
        $record               = ModulesManagement::where('slug', $slug)->get()->first();
       
        
        if ( ! $record ) {
            return redirect()->back();
        }
        $data['record']             = $record;
        $data['active_class']       = 'master_settings';
        $data['title']              = get_text($record->key);
        
        return view('modulesmanagement::admin.modules_managements.sub-list-add-edit', $data);
    }

    public function storeSubSettings(Request $request, $slug)
    {
       
      if (! Gate::allows('modules_management_access')) {
            return prepareBlockUserMessage();
        }

      $record  = ModulesManagement::where('slug', $slug)->get()->first();
        
      if (! $record ) {
            return prepareBlockUserMessage();
        }



        $validation_rules['key'] = 'bail|required|max:50';
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

       flash('success','record_updated_successfully', 'success');
       return redirect()->route( 'admin.modules_managements.index' );
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

         if($request->type=='text'      || 
            $request->type=='number'    ||
            $request->type=='email'     ||
            $request->type=='textarea'  ||
            $request->type=='checkbox'  
            )

            $value = 0;
        if($request->has('value'))
         $value = $request->value;

        if ($request->type=='file') {
            if($request->hasFile('value'))
                $value = $this->processUpload($request);
        }


        elseif ($request->type=='select') {
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
        
        return array('value'=>$value, 'extra'=>$extra, 'tool_tip'=>$tool_tip);
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

    /**
      * [check_isdemo description]
      * @return [type] [description]
      */
    public function check_isdemo()
    {
       if (env('DEMO_MODE'))
          return redirect()->route('admin.modules_managements.index');
       else
          return false;
    }

    public function viewSettings($slug)
    {
        
        if (! Gate::allows('modules_management_access')) {
            return prepareBlockUserMessage();
        }

        $record                 = ModulesManagement::where('slug', $slug)->get()->first();

        if (! $record ) {
            return redirect()->back();
        }
        
        $settings_data = json_decode( $record->settings_data );

      

        return view('modulesmanagement::admin.modules_managements.sub-list', compact( 'record', 'settings_data' ) );
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
         
     if (! Gate::allows('modules_management_access')) {
            return prepareBlockUserMessage();
        }
      $record     = ModulesManagement::where('slug', $slug)->get()->first();
    
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
       
       flashMessage('success','record_updated');
    return redirect()->route('admin.modules_managements.index');

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
                            'twilio_token'
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
            if(isset($value['value']))
            $data[strtoupper($key)] = $value['value'];
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
}