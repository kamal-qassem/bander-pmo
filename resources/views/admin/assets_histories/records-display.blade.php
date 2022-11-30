<table class="table table-bordered table-striped {{ count($assets_histories) > 0 ? 'datatable' : '' }} ">
                <thead>
                    <tr>
                        
                        <th>@lang('global.assets-history.created_at')</th>
                        <th>@lang('global.assets-history.fields.asset')</th>
                        <th>@lang('global.assets-history.fields.status')</th>
                        <th>@lang('global.assets-history.fields.location')</th>
                        <th>@lang('global.assets-history.fields.assigned-user')</th>
                        
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($assets_histories) > 0)
                        @foreach ($assets_histories as $assets_history)
                            <tr data-entry-id="{{ $assets_history->id }}">
                                
                                <td>{{ digiDate( $assets_history->created_at, true ) }}</td>
                                <td field-key='asset'>{{ $assets_history->asset->title ?? '' }}</td>
                                <td field-key='status'>{{ $assets_history->status->title ?? '' }}</td>
                                <td field-key='location'>{{ $assets_history->location->title ?? '' }}</td>
                                <td field-key='assigned_user'>{{ $assets_history->assigned_user->name ?? '' }}</td>
                                
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">@lang('global.app_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>