<table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.dynamic-options.fields.title')</th>
                            <td field-key='title'>{{ $dynamic_option->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.dynamic-options.fields.description')</th>
                            <td field-key='description'>{!! clean($dynamic_option->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.dynamic-options.fields.module')</th>
                            <td field-key='module'>{{ ucfirst($dynamic_option->module) }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.dynamic-options.fields.color')</th>
                            <td field-key='module'>
                                @if( ! empty( $dynamic_option->color ) )
                                <span style="color:{{ $dynamic_option->color }}">{{ $dynamic_option->color }}</span>
                                @else
                                {{ $dynamic_option->color }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>@lang('global.dynamic-options.fields.type')</th>
                            <td field-key='type'>{{ ucfirst($dynamic_option->type) }}</td>
                        </tr>
                    </table>     