@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.contacts.title_profile')</h3>
    @can('delivery_address_edit')
    <p>
        <a href="{{ route('admin.contacts.delivery-address.edit') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.contacts.update-delivery-address')</a>        
    </p>
    @endcan
    
    {!! Form::model($contact, ['method' => 'POST', 'route' => ['admin.contacts.profile.update'], 'files' => true,]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                                            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('first_name', trans('global.contacts.fields.first-name').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('first_name', old('first_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('first_name'))
                        <p class="help-block">
                            {{ $errors->first('first_name') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('last_name', trans('global.contacts.fields.last-name').'', ['class' => 'control-label']) !!}
                    {!! Form::text('last_name', old('last_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('last_name'))
                        <p class="help-block">
                            {{ $errors->first('last_name') }}
                        </p>
                    @endif
                </div>
                </div>                
            
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('language', trans('global.contacts.fields.language').'', ['class' => 'control-label']) !!}
                    <button type="button" class="btn btn-primary btn-xs" id="selectbtn-language">
                        {{ trans('global.app_select_all') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-language">
                        {{ trans('global.app_deselect_all') }}
                    </button>
                    <?php
                    $contact_languages = ! empty($contact->language) ? $contact->language->pluck('id')->toArray() : array();
                    ?>
                    {!! Form::select('language[]', $languages, old('language') ? old('language') : $contact_languages, ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-language' ]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('language'))
                        <p class="help-block">
                            {{ $errors->first('language') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('phone1_code', trans('global.contacts.fields.phone1_code').'', ['class' => 'control-label']) !!}
                    {!! Form::select('phone1_code', $countries_code, old('phone1_code'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('phone1_code'))
                        <p class="help-block">
                            {{ $errors->first('phone1_code') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('phone1', trans('global.contacts.fields.phone1').'', ['class' => 'control-label']) !!}
                    {!! Form::text('phone1', old('phone1'), ['class' => 'form-control number', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('phone1'))
                        <p class="help-block">
                            {{ $errors->first('phone1') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('phone2_code', trans('global.contacts.fields.phone2_code').'', ['class' => 'control-label']) !!}
                    {!! Form::select('phone2_code', $countries_code, old('phone2_code'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('phone2_code'))
                        <p class="help-block">
                            {{ $errors->first('phone2_code') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('phone2', trans('global.contacts.fields.phone2').'', ['class' => 'control-label']) !!}
                    {!! Form::text('phone2', old('phone2'), ['class' => 'form-control number', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('phone2'))
                        <p class="help-block">
                            {{ $errors->first('phone2') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
            
            <div class="row">
                @if ( isAdmin() )
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('email', trans('global.contacts.fields.email').'', ['class' => 'control-label']) !!}
                     <?php
                    
                    $email = $contact->email;
                    if ( empty( $email ) ) {
                        $email = Auth::User()->email;
                    }                  
                    ?>
                    {!! Form::text('email', old('email', $email), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('email'))
                        <p class="help-block">
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                 </div>
                </div>
                @else
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                          <?php
                    
                    $email = $contact->email;
                    if ( empty( $email ) ) {
                        $email = Auth::User()->email;
                    }                  
                    ?>
                <input type="text" name="email" class="form-control" value="{{old('email',$email)}}" disabled>
               </div>
            </div>
                @endif

            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('skype', trans('global.contacts.fields.skype').'', ['class' => 'control-label']) !!}
                    {!! Form::text('skype', old('skype'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('skype'))
                        <p class="help-block">
                            {{ $errors->first('skype') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('address', trans('global.contacts.fields.address').'', ['class' => 'control-label']) !!}
                    {!! Form::text('address', old('address'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('address'))
                        <p class="help-block">
                            {{ $errors->first('address') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('city', trans('global.contacts.fields.city').'', ['class' => 'control-label']) !!}
                    {!! Form::text('city', old('city'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('city'))
                        <p class="help-block">
                            {{ $errors->first('city') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('state_region', trans('global.contacts.fields.state-region').'', ['class' => 'control-label']) !!}
                    {!! Form::text('state_region', old('state_region'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('state_region'))
                        <p class="help-block">
                            {{ $errors->first('state_region') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('zip_postal_code', trans('global.contacts.fields.zip-postal-code').'', ['class' => 'control-label']) !!}
                    {!! Form::text('zip_postal_code', old('zip_postal_code'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('zip_postal_code'))
                        <p class="help-block">
                            {{ $errors->first('zip_postal_code') }}
                        </p>
                    @endif
                </div>
                </div>

                  <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('country_id', trans('global.contacts.fields.country').'', ['class' => 'control-label']) !!}
                    {!! Form::select('country_id', $countries, old('country_id'), ['class' => 'form-control select2 show-tick']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('country_id'))
                        <p class="help-block">
                            {{ $errors->first('country_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('tax_id', trans('global.contacts.fields.tax-id').'', ['class' => 'control-label']) !!}
                    {!! Form::text('tax_id', old('tax_id'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('tax_id'))
                        <p class="help-block">
                            {{ $errors->first('tax_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
              

                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    <?php
                    $themes_arr = Theme::all(); // return Array
                    $themes = [];
                    foreach ($themes_arr as $key => $value) {
                        $themes[ $key ] = $value->get('name');
                    }

                    $current_theme  = \Modules\SiteThemes\Entities\SiteTheme::where('is_active',1)->first();
                    $selected = Auth()->user()->theme;
                    if ( empty( $selected ) ) {
                        $selected = $current_theme->slug;
                    }
                    ?>
                    {!! Form::label('theme', trans('global.contacts.fields.theme').'', ['class' => 'control-label']) !!}
                    {!! Form::select('theme', $themes, $selected, ['class' => 'form-control select2 show-tick']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('theme'))
                        <p class="help-block">
                            {{ $errors->first('theme') }}
                        </p>
                    @endif
                </div>
                </div>

                <?php
                $theme = 'default';
                if (\Cookie::get('theme')) { 
                    $theme = \Cookie::get('theme');
                }
                ?>
                @if( 'default' === $theme )
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('color_theme', trans('global.users.fields.color-theme').'', ['class' => 'control-label']) !!}
                    {!! digi_get_help(trans('custom.messages.default-color-theme-help')) !!}
                    <?php
                    $themes = [
                        'default' => trans( 'global.users.default' ),
                        'darkgray theme.css' => trans( 'global.users.darkgray-theme' ),
                        'gradient blue theme.css' => trans( 'global.users.gradient-blue-theme' ),
                        'light blue theme.css' => trans( 'global.users.light-blue-theme' ),
                        'darkgray theme.css' => trans( 'global.users.white' ),
                    ];
                    ?>
                    {!! Form::select('color_theme', $themes, old('color_theme'), ['class' => 'form-control select2', 'id' => 'color_theme']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('color_theme'))
                        <p class="help-block">
                            {{ $errors->first('color_theme') }}
                        </p>
                    @endif
                </div>
                </div>

                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('color_skin', trans('global.users.fields.color-skin').'', ['class' => 'control-label']) !!}
                    {!! digi_get_help(trans('custom.messages.default-color-skin-help')) !!}
                    <?php
                    $skins = [
                        'skin-blue' => trans( 'global.users.default' ),
                        'skin-blue-light' => trans( 'global.users.skin-blue-light' ),
                        'skin-yellow' => trans( 'global.users.skin-yellow' ),
                        'skin-yellow-light' => trans( 'global.users.skin-yellow-light' ),
                        'skin-green' => trans( 'global.users.skin-green' ),

                        'skin-green-light' => trans( 'global.users.skin-green-light' ),
                        'skin-purple' => trans( 'global.users.skin-purple' ),
                        'skin-purple-light' => trans( 'global.users.skin-purple-light' ),
                        'skin-red' => trans( 'global.users.skin-red' ),
                        'skin-red-light' => trans( 'global.users.skin-red-light' ),

                        'skin-black' => trans( 'global.users.skin-black' ),
                        'skin-black-light' => trans( 'global.users.skin-black-light' ),
                    ];
                    ?>
                    {!! Form::select('color_skin', $skins, old('color_skin'), ['class' => 'form-control select2', 'id' => 'color_skin']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('color_skin'))
                        <p class="help-block">
                            {{ $errors->first('color_skin') }}
                        </p>
                    @endif
                </div>
                </div>
                @endif

                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    @if (!empty($contact->thumbnail) && file_exists(public_path() . '/thumb/' . $contact->thumbnail) )
                        <a href="{{ asset(env('UPLOAD_PATH').'/'.$contact->thumbnail) }}" target="_blank"><img src="{{ asset(env('UPLOAD_PATH').'/thumb/'.$contact->thumbnail) }}"></a>
                    @endif
                    {!! Form::label('thumbnail', trans('global.products.fields.thumbnail').'', ['class' => 'control-label']) !!}
                    {!! Form::file('thumbnail', ['class' => 'form-control', 'style' => 'margin-top: 4px;']) !!}
                    {!! Form::hidden('thumbnail_max_size', 10) !!}
                    {!! Form::hidden('thumbnail_max_width', 4096) !!}
                    {!! Form::hidden('thumbnail_max_height', 4096) !!}
                    <p class="help-block"></p>
                    @if($errors->has('thumbnail'))
                        <p class="help-block">
                            {{ $errors->first('thumbnail') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>


            
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
    <script>
        $("#selectbtn-language").click(function(){
            $("#selectall-language > option").prop("selected","selected");
            $("#selectall-language").trigger("change");
        });
        $("#deselectbtn-language").click(function(){
            $("#selectall-language > option").prop("selected","");
            $("#selectall-language").trigger("change");
        });
    </script>
@stop