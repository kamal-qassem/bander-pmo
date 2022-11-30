<?php

namespace Modules\SiteThemes\Http\Controllers\Admin;

use Modules\SiteThemes\Entities\SiteTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\SiteThemes\Http\Requests\Admin\StoreSiteThemesRequest;
use Modules\SiteThemes\Http\Requests\Admin\UpdateSiteThemesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\ImageSettings;
use Input;
use Image;
use File;
use Storage;
use Zip;
use Illuminate\Support\Str;

class SiteThemesController extends Controller
{   

    public function __construct() {
      $this->middleware('plugin:sitethemes');
    }
    /**
     * Display a listing of SiteTheme.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('site_theme_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = SiteTheme::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('site_theme_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'site_themes.id',
                'site_themes.title',
                'site_themes.slug',
                'site_themes.theme_title_key',
                'site_themes.settings_data',
                'site_themes.description',
                'site_themes.is_active',
                'site_themes.theme_color',
            ]);
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'site_theme_';
                $routeKey = 'admin.site_themes';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('slug', function ($row) {
                return $row->slug ? $row->slug : '';
            });
            $table->editColumn('theme_title_key', function ($row) {
                return $row->theme_title_key ? $row->theme_title_key : '';
            });
            $table->editColumn('settings_data', function ($row) {
                $settings_data = $row->settings_data ? $row->settings_data : '';
				$str = '';
				if ( ! empty( $settings_data ) ) {
					$settings_data = json_decode( $settings_data );
				
					foreach( $settings_data as $key => $data ) {
						$str .= '<b>' . Str::title( $key ) . '</b>: ' . $data->value . '<br>';
					}
				}
				return $str;
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('is_active', function ($row) {
                return $row->is_active ? trans('sitethemes::global.site-themes.yes') : trans('sitethemes::global.site-themes.no');
            });
            $table->editColumn('theme_color', function ($row) {
                return $row->theme_color ? $row->theme_color : '';
            });

            $table->rawColumns(['actions','massDelete', 'settings_data']);

            return $table->make(true);
        }

        return view('sitethemes::admin.site_themes.index');
    }

    /**
     * Show the form for creating new SiteTheme.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('site_theme_create')) {
            return prepareBlockUserMessage();
        }        $enum_is_active = SiteTheme::$enum_is_active;
            
        return view('sitethemes::admin.site_themes.create', compact('enum_is_active'));
    }

    /**
     * Store a newly created SiteTheme in storage.
     *
     * @param  \App\Http\Requests\StoreSiteThemesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('site_theme_create')) {
            flashMessage( 'danger', 'not-allowed');
            return back();
        }
        
        $is_valid = Zip::check($request->theme);

        if ( ! $is_valid ) {
            flashMessage( 'danger', 'create', trans('sitethemes::global.site-themes.not-valid-zip'));
            return back();
        }

        $zip = Zip::open( $request->theme );
        
        $files = $zip->listFiles();
        $is_valid = false;
        $file_path = '';
        if ( ! empty( $files ) ) {
            foreach ($files as $key => $value) {
                if (strpos($value, 'theme.json') !== false) {
                    $is_valid = true;
                    $file_path = $value;
                }
            }
        }

        if ( ! $is_valid ) {
            flashMessage( 'danger', 'create', trans('sitethemes::global.site-themes.not-valid-theme'));
            return back();
        }


        $imageObject = new \App\ImageSettings();   
        $destinationPath      = public_path() . $imageObject->getSettingsImagePath();
     
        $zip->extract($destinationPath . 'theme.json', $file_path);

        $themejson = File::get($destinationPath . 'theme.json/' . $file_path );

        $details = json_decode( $themejson, true );
        
        if ( ! empty( $details ) ) {
            if ( empty( $details['title'] && $details['name'])) {
                flashMessage( 'danger', 'create', trans('sitethemes::global.site-themes.not-valid-theme'));
                return back();
            } else {
                $check = SiteTheme::where('slug', '=', $details['name'])->first();
                if ( $check ) {
                    flashMessage( 'danger', 'create', trans('sitethemes::global.site-themes.theme-already-exists'));
                    return back();
                } else {
                    
                    $zip->extract('Themes');

                    $data = array(
                        'title' => $details['title'],
                        'slug' => $details['name'],
                        'theme_title_key' => $details['name'],
                        'settings_data' => json_encode( $details['settings_data'] ),
                        'description' => '',
                    );
                    $site_theme = SiteTheme::create($data);

                    flashMessage( 'success', 'create', trans('sitethemes::global.site-themes.theme-install-success'));
                }
            }
        } else {
            flashMessage( 'danger', 'create', trans('sitethemes::global.site-themes.not-valid-theme'));
            return back();
        }

        return redirect()->route('admin.site_themes.index');
    }


    /**
     * Show the form for editing SiteTheme.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('site_theme_edit')) {
            return prepareBlockUserMessage();
        }        
        $enum_is_active = SiteTheme::$enum_is_active;
            
        $site_theme = SiteTheme::findOrFail($id);

        if ($site_theme->slug === 'default') {
            flashMessage( 'danger', 'not-allowed', trans( 'sitethemes::global.site-themes.not-allowed-default' ) );
            return redirect()->back();
        }

        return view('sitethemes::admin.site_themes.edit', compact('site_theme', 'enum_is_active'));
    }

    /**
     * Update SiteTheme in storage.
     *
     * @param  \App\Http\Requests\UpdateSiteThemesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSiteThemesRequest $request, $id)
    {
        if (! Gate::allows('site_theme_edit')) {
            return prepareBlockUserMessage();
        }
        $site_theme = SiteTheme::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $site_theme->update($request->all());
        
        flashMessage( 'success', 'update' );
        return redirect()->route('admin.site_themes.index');
    }


    /**
     * Display SiteTheme.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('site_theme_view')) {
            return prepareBlockUserMessage();
        }
        $site_theme = SiteTheme::findOrFail($id);

        return view('sitethemes::admin.site_themes.show', compact('site_theme'));
    }


    /**
     * Remove SiteTheme from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('site_theme_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $site_theme = SiteTheme::findOrFail($id);

        Storage::deleteDirectory( 'Themes/' . $site_theme->slug);

        $site_theme->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.site_themes.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
      }
    }

    /**
     * Delete all selected SiteTheme at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('site_theme_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        flashMessage( 'success', 'deletes' );

        if ($request->input('ids')) {
            $entries = SiteTheme::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore SiteTheme from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('site_theme_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $site_theme = SiteTheme::onlyTrashed()->findOrFail($id);
        $site_theme->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete SiteTheme from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('site_theme_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $site_theme = SiteTheme::onlyTrashed()->findOrFail($id);
        $site_theme->forceDelete();

        return back();
    }

    /**
    * make as default theme
    * @param  [type] $id [description]
    * @return [type]     [description]
    */
    public function makeDefault($id)
    {
       
       if (! Gate::allows('site_theme_settings')) {
            flashMessage( 'danger', 'not-allowed' );
            return redirect()->back();
        }

       $record   = SiteTheme::find($id);

        $theme = 'default';
        $color_theme = 'default';
        $color_skin = 'skin-blue';

        if ( $theme ) {
          $theme = $record->theme_title_key;
          $settings = getArrayFromJson( $record->settings_data );
          if ( ! empty( $settings['theme_color']->value ) ) {
            $color_theme = $settings['theme_color']->value;
          }
          if ( ! empty( $settings['color_skin']->value ) ) {
            $color_skin = $settings['color_skin']->value;
          }
        }

       $other_themes  = SiteTheme::where('id','!=',$id)->get();

       foreach ($other_themes as $theme_single) {          
          $theme_single->is_active  = 0; 
          $theme_single->save();
        }

       $record->is_active  = 1;
       $record->save();

      $user = Auth()->user();
      $user->theme = $theme;
      $user->color_theme = $color_theme;
      $user->color_skin = $color_skin;
      $user->save();

     
        flashMessage('success','create',trans('sitethemes::global.site-theme.make-default'));
        return redirect()->route('admin.site_themes.index')
          ->withCookie(cookie()->forever('color_theme', $color_theme))
          ->withCookie(cookie()->forever('color_skin', $color_skin))
          ->withCookie(cookie()->forever('theme', $theme));
    }

    public function viewSettings($slug)
    { 

        if (! Gate::allows('site_theme_settings')) {
            flashMessage( 'danger', 'not_allowed' );
            return back();
        }


        $record   = SiteTheme::where('slug', $slug)->first();

        $settings_data      = getArrayFromJson($record->settings_data);

        return view('sitethemes::admin.site_themes.sub-list', compact('settings_data', 'record', 'slug'));
    }

    /**
      * [check_isdemo description]
      * @return [type] [description]
      */
    public function check_isdemo()
    {
       if (env('DEMO_MODE')) {
          flashMessage( 'info', 'create', trans('custom.settings.crud_disabled') );
          return back();
      }
    else {
          return false;
      }
    }

    public function addSubSettings($slug)
    {

        if (! Gate::allows('site_theme_settings')) {
            flashMessage( 'danger', 'not_allowed' );
            return back();
        }
        
        $record               = SiteTheme::where('slug', $slug)->get()->first();
      
        
        if ( ! $record ) {
            return redirect()->back();
        }
        $data['record']             = $record;
        $data['active_class']       = 'master_settings';
        $data['title']              = get_text($record->key);
        
        return view('sitethemes::admin.site_themes.sub-list-add-edit', $data);
    }

    public function storeSubSettings(Request $request, $slug)
    {
       
      if (! Gate::allows('site_theme_settings')) {
            flashMessage( 'danger', 'not_allowed' );
            return back();
        }

      $record  = SiteTheme::where('slug', $slug)->get()->first();
        
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
        
       if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
       $record->save();

       flash('success','record_updated_successfully', 'success');
       return redirect()->route( 'admin.site_themes.viewsettings', $slug );
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
     * Update the theme settings
     * @param  Request $request [description]
     * @param  [type]  $slug    [description]
     * @return [type]           [description]
     */
    public function updateSubSettings(Request $request, $slug )
    {
      
       if (! Gate::allows('site_theme_settings')) {
            flashMessage( 'danger', 'not_allowed' );
            return back();
        }
        
          $record  = SiteTheme::where('slug', $slug)->first();
        

        $input_data = Input::all();

      if($request->has('theme_color') && $slug == 'theme_one'){

        $record->theme_color   = $request->theme_color['value'];
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $record->save();

      }

        
     
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
                $extra = $old_values->$key->extra;
           }
            
            $data[$key] = array('value'=>$value, 'type'=>$submitted_value->type, 'extra'=>$extra, 'tool_tip'=>$submitted_value->tool_tip);
           
        }    
       
       $record->settings_data = json_encode($data);
      if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
       $record->save();

       flashMessage('success','update'); 
        return redirect()->route('admin.site_themes.viewsettings', $record->slug);

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
}
