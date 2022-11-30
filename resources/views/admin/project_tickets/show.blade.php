@extends('layouts.app')

@section('content')
    @include('admin.client_projects.operations.menu', array( 'client_project' => $project))
    
    <h3 class="page-title">@lang('global.client-projects.title-tickets')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">

                    <p> <strong>{{ trans('ticketit::lang.subject') }}</strong>{{ trans('ticketit::lang.colon') }}{{ $ticket->subject }}</p>

                    <p> <strong>{{ trans('ticketit::lang.owner') }}</strong>{{ trans('ticketit::lang.colon') }}{{ $ticket->user->name }}</p>
                    <p>
                        <strong>{{ trans('ticketit::lang.status') }}</strong>{{ trans('ticketit::lang.colon') }}
                        @if( $ticket->isComplete() && ! $ticket->status_id )
                            <span style="color: blue">Complete</span>
                        @else
                            <span style="color: {{ $ticket->status->color }}">{{ $ticket->status->name }}</span>
                        @endif

                    </p>
                    <p>
                        <strong>{{ trans('ticketit::lang.priority') }}</strong>{{ trans('ticketit::lang.colon') }}
                        <span style="color: {{ $ticket->priority->color }}">
                            {{ $ticket->priority->name }}
                        </span>
                    </p>

                     <p>
                        <strong>{{ trans('ticketit::lang.description') }}</strong>{{ trans('ticketit::lang.colon') }}{{ $ticket->content }}
                       
                    </p>
                </div>
                <div class="col-md-6">
                    <p> <strong>{{ trans('ticketit::lang.agent') }}</strong>{{ trans('ticketit::lang.colon') }}{{ $ticket->agent->name }}</p>
                    <p>
                        <strong>{{ trans('ticketit::lang.category') }}</strong>{{ trans('ticketit::lang.colon') }}
                        <span style="color: {{ $ticket->category->color }}">
                            {{ $ticket->category->name }}
                        </span>
                    </p>
                    <p> <strong>{{ trans('ticketit::lang.created') }}</strong>{{ trans('ticketit::lang.colon') }}{{ $ticket->created_at->diffForHumans() }}</p>
                    <p> <strong>{{ trans('ticketit::lang.last-update') }}</strong>{{ trans('ticketit::lang.colon') }}{{ $ticket->updated_at->diffForHumans() }}</p>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.project_tickets.index', $project->id) }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop
