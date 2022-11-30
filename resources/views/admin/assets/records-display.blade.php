<table class="table table-bordered table-striped {{ count($assets) > 0 ? 'datatable' : '' }} @can('asset_delete_multi') dt-select @endcan">
                <thead>
                    <tr>
                        @can('asset_delete_multi')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>@lang('global.assets.fields.category')</th>
                        <th>@lang('global.assets.fields.serial-number')</th>
                        <th>@lang('global.assets.fields.title')</th>
                        <th>@lang('global.assets.fields.photo1')</th>
                        <th>@lang('global.assets.fields.status')</th>
                        <th>@lang('global.assets.fields.location')</th>
                        
                        <th>&nbsp;</th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($assets) > 0)
                        @foreach ($assets as $asset)
                            <tr data-entry-id="{{ $asset->id }}">
                                @can('asset_delete_multi')
                                    <td></td>
                                @endcan

                                <td field-key='category'>{{ $asset->category->title ?? '' }}</td>
                                <td field-key='serial_number'>{{ $asset->serial_number }}</td>
                                <td field-key='title'>{{ $asset->title }}</td>


                                <td field-key='photo1'>
                                    @if( $asset->photo1 && file_exists(public_path() . '/thumb/' . $asset->photo1)) 
                                    <a href="{{ route('admin.home.media-file-download', ['model' => 'Asset', 'field' => 'photo1', 'record_id' => $asset->id]) }}" ><img src="{{ asset(env('UPLOAD_PATH').'/thumb/' . $asset->photo1) }}"/></a>
                                  @else  
                                   <img src="{{ asset ('images/product-50x50.jpg') }}" width="50" height="50"/> 
                                @endif
                            </td>
                              
                                <td field-key='status'>{{ $asset->status->title ?? '' }}</td>
                                <td field-key='location'>{{ $asset->location->title ?? '' }}</td>
                                
                                <td>
                                    @can('asset_view')
                                    <a href="{{ route('admin.assets.show',[$asset->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('asset_edit')
                                    <a href="{{ route('admin.assets.edit',[$asset->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('asset_delete_multi')
                              {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.assets.destroy', $asset->id])) !!}
                                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">@lang('global.app_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>