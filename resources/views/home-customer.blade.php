@extends('layouts.app')

@section('content')
    <div class="row">   

        @if( ! empty( $widgets ) )
            
             @foreach( $widgets as $widgetSingle )
                @if ( ! empty( $widgetSingle->slug ) )
                    @include('dashboard-parts.' . $widgetSingle->slug, ['widget' => $widgetSingle])
                @endif                            
            @endforeach 

        @endif
    </div>
@endsection

