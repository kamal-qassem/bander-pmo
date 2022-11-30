@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.contract_types.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
              
                </div>
            </div>
<ul class="nav nav-tabs" role="tablist">
 <li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>   
</ul>
    
<div class="tab-content">
    
   <div role="tabpanel" class="tab-pane active" id="details">


                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.contract_types.fields.name')</th>
                            <td field-key='name'>{{ $contract_type->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.contract_types.fields.description')</th>
                            <td field-key='description'>{!! clean($contract_type->description) !!}</td>
                        </tr>
                        
                    </table>
                </div>
                
            <!-- Nav tabs -->


<!-- Tab panes -->

    


            <p>&nbsp;</p>

            <a href="{{ route('admin.contract_types.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


