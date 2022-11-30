@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.navigation-menues.title')</h3>
    
    <p>
        {!! Menu::render() !!}
      
    </p>
@stop

@section('javascript') 
	@parent
	{!! Menu::scripts() !!}
@endsection