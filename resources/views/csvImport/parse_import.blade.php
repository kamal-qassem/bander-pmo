@extends('layouts.app')

@section('content')

    <div class='row'>
        <div class='col-md-12'>
            <div class="panel panel-default">
                <div class="panel-heading">
                    @lang('global.app_csvImport')
                </div>

                <div class="panel-body table-responsive">
                    <form class="form-horizontal" method="POST" action="{{ route('admin.csv_process') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="filename" value="{{ $filename }}"/>
                        <input type="hidden" name="hasHeader" value="{{ $hasHeader }}"/>
                        <input type="hidden" name="modelName" value="{{ $modelName }}"/>
                        <input type="hidden" name="redirect" value="{{ $redirect }}"/>
                        <input type="hidden" name="operation" value="{{ $operation ?? '' }}"/>
                        <input type="hidden" name="duplicatecheck" value="{{ $duplicatecheck ?? '' }}"/>
                        <input type="hidden" name="contact_type" value="{{ $contact_type ?? '' }}"/>
                        
                        <table class="table">
                            @if (isset($headers))
                                <tr>
                                    @foreach ($headers as $field)
                                        <th>{{ $field }}</th>
                                    @endforeach
                                </tr>
                            @endif
                            @if($lines)
                                @foreach ($lines as $line)
                                    <tr>
                                        @foreach ($line as $field)
                                            <td>{{ $field }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endif
                            
                            <?php
                            $import_fields_fillable = getOption( 'import_fields_fillable', 'display' );
                            ?>
                            @if( 'select' === $import_fields_fillable )
                            <tr>
                                @foreach ($headers as $key => $header)
                                    <td>
                                        <select name="fields[{{ $key }}]">
                                            <option value=''>Please select</option>
                                            @foreach ($fillables as $k => $fillable)
                                                <option value="{{ $fillable }}"
                                                        @if (strtolower($header) === strtolower($fillable)) selected @endif>{{ $fillable  }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endforeach
                            </tr>
                            @else
                            <tr>
                                @foreach ($headers as $key => $header)
                                    <td>
                                        @foreach ($fillables as $k => $fillable)
                                            @if (strtolower($header) === strtolower($fillable))
                                                <b>{{$fillable}}</b>
                                                <input type="hidden" name="fields[{{ $key }}]" value="@if (strtolower($header) === strtolower($fillable)){{$fillable}}@endif">
                                            @endif                                            
                                        @endforeach
                                        
                                    </td>
                                @endforeach
                            </tr>
                            @endif
                        </table>

                        <button type="submit" class="btn btn-primary">
                            @lang('global.app_import_data')
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection