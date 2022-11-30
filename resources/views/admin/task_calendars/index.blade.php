@extends('layouts.app')

@section('content')

    <h3 class="page-title">@lang('custom.tasks.calendar')</h3>

    <link href="{{ url('css/cdn-styles-css/calendar/3.1.0/fullcalendar.min.css') }}" rel="stylesheet">  

     

    <div id='calendar'></div>

    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                                class="sr-only">@lang('global.app_close')</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="error"></div>
                    <form class="form-horizontal" id="crud-form">
                        <input type="hidden" id="start">
                        <input type="hidden" id="end">
                        <div class="row form-group">
                            <label class="col-md-4 control-label" for="title">{!! Form::label('name', trans('global.tasks.fields.name').'*', ['class' => 'control-label']) !!}</label>
                            <div class="col-md-8">
                                {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                            </div>
                        </div>
                        <br>
                        <div class="row form-group">
                            <label class="col-md-4 control-label" for="description">{!! Form::label('description', trans('global.tasks.fields.description').'*', ['class' => 'control-label']) !!}</label>
                            <div class="col-md-8">
                                {!! Form::textarea('description', old('description'), ['class' => 'form-control ', 'placeholder' => '']) !!}
                            </div>
                        </div>
                        <br>
                        <div class="row form-group">
                            <label class="col-md-4 control-label" for="color">{!! Form::label('status_id', trans('global.tasks.fields.status').'*', ['class' => 'control-label']) !!}</label>
                            <div class="col-md-4">
                                {!! Form::select('status_id', $statuses, old('status_id', 1), ['class' => 'form-control select2', 'required' => '']) !!}
                            </div>
                        </div>
                        <br>
                        @if ( ! isCustomer() )
                        <div class="row form-group">
                            <label class="col-md-4 control-label" for="color">{!! Form::label('user_id', trans('global.tasks.fields.user').'', ['class' => 'control-label']) !!}</label>
                            <div class="col-md-4">
                                {!! Form::select('user_id', $users, old('user_id'), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('global.app_cancel')</button>
                </div>
            </div>
        </div>
    </div>
    
@stop

@section('javascript')
    @parent
    <script src="{{ url('js/cdn-js-files') }}/moment.min.js"></script>
    
    <script src="{{ url('js/cdn-js-files/calendar/3.1.0') }}/fullcalendar.min.js"></script>
    
    <?php
    
    ?>
    <script>
        var currentEvent;
        $(document).ready(function() {
            // page is now ready, initialize the calendar...
            $('#calendar').fullCalendar({
                // put your options and callbacks here
                events : [
                    @foreach($events as $event)
                        @if($event->due_date)
                        {
                            title : '{{ $event->name }}',
                            name : '{{ $event->name }}',
                            @if( $event->start_date )
                            start : '{{ $event->start_date }}',
                            @endif
                            end : '{{ $event->due_date }}T23:59:00',
                            description: "{!! trim(preg_replace('/\s\s+/', ' ', clean($event->description))) !!}",
                            start_date: '{{$event->start_date}}',
                            due_date: '{{$event->due_date}}',
                            status_id: '{{$event->status_id}}',
                            user_id: '{{$event->user_id}}',
                            record_id: '{{$event->id}}'
                        },
                        @endif
                    @endforeach
                ],
                eventLimit: true, // allow "more" link when too many events
                selectable: true,
                selectHelper: true,
                editable: true, // Make the event resizable true
                select: function(start, end) {
                
                    $('#start').val(moment(start).format('DD-MM-YYYY'));
                    $('#end').val(moment(end).format('DD-MM-YYYY'));
                     // Open modal to add event
                    modal({
                        // Available buttons when adding
                        buttons: {
                            add: {
                                id: 'add-event', // Buttons id
                                css: 'btn-success', // Buttons class
                                label: 'Add' // Buttons label
                            }
                        },
                        title: 'Add Event' // Modal title
                    });
                },
                eventClick: function(calEvent, jsEvent, view) {
                    // Set currentEvent variable according to the event clicked in the calendar
                    currentEvent = calEvent;
                    
                    // Open modal to edit or delete event
                    modal({
                        // Available buttons when editing
                        buttons: {
                            delete: {
                                id: 'delete-event',
                                css: 'btn-danger',
                                label: 'Delete'
                            },
                            update: {
                                id: 'update-event',
                                css: 'btn-success',
                                label: 'Update'
                            }
                        },
                        title: 'Edit Task "' + calEvent.title + '"',
                        event: calEvent
                    });
                },
                eventResize: function(event,dayDelta,minuteDelta,revertFunc) {                    
                    currentEvent = event;

                    start = event.start.format('YYYY-MM-DD HH:mm:ss');
                    if(event.end){
                        end = event.end.format('YYYY-MM-DD HH:mm:ss');
                    }else{
                        end = start;
                    }         
                               
                    updateTask( start, end );
                },
                eventDrop: function(event, delta, revertFunc,start,end) {            
                    currentEvent = event;

                    start = event.start.format('YYYY-MM-DD HH:mm:ss');
                    if(event.end){
                        end = event.end.format('YYYY-MM-DD HH:mm:ss');
                    }else{
                        end = start;
                    }

                    

                    updateTask( start, end );
                    
                  }
            })

            // Handle Click on Add Button
            $('.modal').on('click', '#add-event',  function(e){
                if(validator(['name', 'description', 'status_id'])) {                    
                    $.ajax({
                        url: '{{route("admin.tasks.calendar.addtask")}}',
                        dataType: "json",
                        method: 'post',
                        data: {                            
                            '_token': crsf_hash,                            
                            name: $('#name').val(),
                            description: $('#description').val(),
                            status_id: $('#status_id').val(),
                            start: $('#start').val(),
                            end: $('#end').val(),
                            user_id: $('#user_id').val()
                        },
                        success: function (data) {
                            notifyMe(data.status, data.message);
                            $('.modal').modal('hide');
                            
                            location.reload();
                        }
                    });
                }
            });
        });

        // Handle click on Update Button
        $('.modal').on('click', '#update-event',  function(e){
            
            if(validator(['name', 'description', 'status_id'])) {                   
                $.ajax({
                    url: '{{route("admin.tasks.calendar.updatetask")}}',
                    dataType: "json",
                    method: 'post',
                    data: {                            
                        '_token': crsf_hash,
                        record_id: currentEvent.record_id,
                        name: $('#name').val(),
                        description: $('#description').val(),
                        status_id: $('#status_id').val(),
                        start: currentEvent.start_date,
                        end: currentEvent.due_date,
                        user_id: $('#user_id').val()
                    },
                    success: function (data) {
                        notifyMe(data.status, data.message);

                        if ( 'success' ==data.status ) {
                            $('.modal').modal('hide');
                            location.reload();
                        }
                    }
                });
            }
        });

        $('.modal').on('click', '#delete-event',  function(e){
            
            $.ajax({
                url: '{{route("admin.calendartasks.calendar.deletetask")}}',
                dataType: "json",
                method: 'post',
                data: {                            
                    '_token': crsf_hash,
                    record_id: currentEvent.record_id
                },
                success: function (data) {
                    notifyMe(data.status, data.message);

                    if ( 'success' ==data.status ) {
                        $('.modal').modal('hide');
                        location.reload();
                    }
                }
            });
        });

    function updateTask( start, end ) {
        
        $.ajax({
            url: '{{route("admin.tasks.calendar.updatetask")}}',
            dataType: "json",
            method: 'post',
            data: {                            
                '_token': crsf_hash,
                record_id: currentEvent.record_id,
                name: currentEvent.name,
                description: currentEvent.description,
                status_id: currentEvent.status_id,
                start: start,
                end: end,
                user_id: currentEvent.user_id
            },
            success: function (data) {
                notifyMe(data.status, data.message);

                
            }
        });
    }
    // Prepares the modal window according to data passed
    function modal(data) {
        
        // Set modal title
        $('.modal-title').html(data.title);
        // Clear buttons except Cancel
        $('.modal-footer button:not(".btn-default")').remove();
        // Set input values
        $('#name').val(data.event ? data.event.title : '');        
        $('#description').val(data.event ? data.event.description : '');
        $('#due_date').val(data.event ? data.event.due_date : '');
        $('#status_id').val(data.event ? data.event.status_id : '');
        $('#user_id').val(data.event ? data.event.user_id : '');
        if ( data.event  ) {
            $('#user_id option[value="'+data.event.user_id+'"]').prop('selected', true)
        }

        $('#status_id').trigger('change.select2');
        $('#user_id').trigger('change.select2');

        
        // Create Butttons
        $.each(data.buttons, function(index, button){
            $('.modal-footer').prepend('<button type="button" id="' + button.id  + '" class="btn ' + button.css + '">' + button.label + '</button>')
        })
        //Show Modal
        $('.modal').modal('show');
    }

    // Dead Basic Validation For Inputs
    function validator(elements) {
        var errors = 0;
        $.each(elements, function(index, element){
            if($.trim($('#' + element).val()) == '') errors++;
        });
        if(errors) {
            $('.error').html('Please insert title, description and status');
            return false;
        }
        return true;
    }
    </script>
@stop
