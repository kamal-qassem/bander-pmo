@extends('layouts.app')

@section('content')
    <link href="{{ url('css/cdn-styles-css/calendar/3.1.0/fullcalendar.min.css') }}" rel="stylesheet">  
    
    <h3 class="page-title">@lang('custom.tasks.statuswise-tasks')</h3>
   
    @foreach( $statuses as $status )
        <div class="col-md-3">
            <div class="panel {{$status->color}}">
              <div class="panel-heading">{{$status->name}}</div>
              <div class="panel-body digi-droppable-area" data-status_id="{{$status->id}}">                  
                  @foreach( $events as $event )
                    @if ( $event->status_id == $status->id )
                        <div class="label label-info label-many col-md-12" data-item_id="{{$event->id}}">{{$event->name}}</div>
                    @endif
                  @endforeach
              </div>
            </div>
        </div>
    @endforeach
@stop

@section('javascript')
    @parent
    <script src="{{ url('js/dragula.js') }}"></script>

    <script>
        

        $(function () {

            var a = dragula($(".digi-droppable-area").toArray()), r = a.containers, o = r.length;

            
            for ( var l = 0; l < o; l++) { 
                $(r[l]).addClass("dragula dragula-vertical");
            }
            a.on("drop", function (a, t, o, l) {
                // a - Item moved
                // t - target
                // o - Origin / Source

                var source = $( o ).data('status_id');
                var target = $( t ).data('status_id');
                var item_id = $( a ).data('item_id');

                

                $.ajax({
                    url: '{{route("admin.calendartasks.calendar.updatetaskstatus")}}',
                    dataType: "json",
                    method: 'post',
                    data: {                            
                        '_token': crsf_hash,
                        source: source,
                        target: target,
                        item_id: item_id
                    },
                    success: function (data) {
                        notifyMe(data.status, data.message);
                    }
                });
            });

        });

        
    </script>
    
@stop
