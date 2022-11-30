@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.product-categories.title')</h3>
    
    {!! Form::model($product_category, ['method' => 'PUT', 'route' => ['admin.product_categories.update', $product_category->id], 'files' => true,'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('name', trans('global.product-categories.fields.name').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => 'Enter category name', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('description', trans('global.product-categories.fields.description').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('description', old('description'), ['class' => 'form-control ', 'placeholder' => 'Description', 'rows' => 2]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('description'))
                        <p class="help-block">
                            {{ $errors->first('description') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-4">
                <div class="form-group">
                    @if( $product_category->photo && file_exists(public_path() . '/thumb/' . $product_category->photo)) 
                        <a href="{{ route('admin.home.media-file-download', [ 'model' => 'ProductCategory', 'field' => 'photo', 'record_id' => $product_category->id ]) }}" ><img src="{{ asset(env('UPLOAD_PATH').'/thumb/'.$product_category->photo) }}"></a>
                    @endif
                    {!! Form::label('photo', trans('global.product-categories.fields.photo').'', ['class' => 'control-label']) !!}
                    {!! Form::file('photo', ['class' => 'form-control', 'style' => 'margin-top: 4px;']) !!}
                    {!! Form::hidden('photo_max_size', 8) !!}
                    {!! Form::hidden('photo_max_width', 6000) !!}
                    {!! Form::hidden('photo_max_height', 6000) !!}
                    <p class="help-block">@lang('global.products.gallery-file-types')</p>
                    @if($errors->has('photo'))
                        <p class="help-block">
                            {{ $errors->first('photo') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

