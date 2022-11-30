@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('sitethemes::global.site-themes.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('sitethemes::global.site-themes.fields.title')</th>
                            <td field-key='title'>{{ $site_theme->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('sitethemes::global.site-themes.fields.slug')</th>
                            <td field-key='slug'>{{ $site_theme->slug }}</td>
                        </tr>
                        <tr>
                            <th>@lang('sitethemes::global.site-themes.fields.theme-title-key')</th>
                            <td field-key='theme_title_key'>{{ $site_theme->theme_title_key }}</td>
                        </tr>
                        <tr>
                            <th>@lang('sitethemes::global.site-themes.fields.settings-data')</th>
                            <td field-key='settings_data'>{!! clean($site_theme->settings_data) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('sitethemes::global.site-themes.fields.description')</th>
                            <td field-key='description'>{{ $site_theme->description }}</td>
                        </tr>
                        <tr>
                            <th>@lang('sitethemes::global.site-themes.fields.is-active')</th>
                            <td field-key='is_active'>{{ $site_theme->is_active }}</td>
                        </tr>
                            <?php

                              $settings_data = $site_theme->settings_data;

                                if ( ! empty( $settings_data ) ) {
                                $settings_data = json_decode( $settings_data );
                                foreach( $settings_data as  $data ) {
                                $str =  $data->value ;
                                }
                              }
                            ?>

                        <tr>
                            <th>@lang('sitethemes::global.site-themes.fields.theme-color')</th>
                            <td field-key='theme_color'>{{ $str ?? '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.site_themes.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


