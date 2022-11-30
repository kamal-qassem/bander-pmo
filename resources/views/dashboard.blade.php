@extends('layouts.app')

@section('content')
<link type="text/css" href="{{ url('css/dashboard.css') }}" rel="stylesheet" >

<div id="page-wrapper">
            <div class="container-fluid">
            <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">                             
                            <li><i class="fa fa-home"></i> @lang('custom.dashboard.title')</li>
                        </ol>
                    </div>
                </div>
<?php
$OrdersYearsDataAreaChart = ! empty( $yearly_data['areachart'] ) ? $yearly_data['areachart'] : array();
$InvoicesYearsDataAreaChart = ! empty( $yearly_data['areachart_invoices'] ) ? $yearly_data['areachart_invoices'] : array();
?>  
       

             @if( ! empty( $widgets ) )
                    
                     @foreach( $widgets as $widgetSingle )
                        @if ( ! empty( $widgetSingle->slug ) )
                            @include('dashboard-parts.' . $widgetSingle->slug, ['widget' => $widgetSingle])
                        @endif                            
                    @endforeach 
                
                @endif
            

        </div>

</div>
@endsection

@section('javascript')

@include('dashboard-parts.dashboard-scripts')

<script src="{{ url('js/cdn-js-files/chartjs250/raphael-min.js') }}"></script>
<script src="{{ url('js/cdn-js-files/chartjs250/morris.min.js') }}"></script>
@endsection