@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('header_scripts')
    <link href="{{ url('css/comments.css') }}" rel="stylesheet"/>
@endsection

@section('content')
    @include('admin.client_projects.operations.menu', array( 'client_project' => $project))

    <h3 class="page-title">@lang('global.project-discussions.title')</h3>    

    <div class="panel panel-default">
        <div class="panel-heading">

            @lang('global.app_list')
        </div>

        <div class="panel-body">
         <div class="col-lg-12">
                  <h3>{!! clean($topic->subject) !!}</h3>
                <p class="no-margin">@lang('global.project-discussions.posted-on', ['date' => digiDate($topic->created_at, true)])</p>
                <p class="no-margin">@lang('global.project-discussions.posted-by', ['name' => $topic->created_name->name])</p>
                <p>@lang('global.project-discussions.total-comments'): {{\App\ProjectDiscussionComment::where('project_id', '=', $project->id)->where('discussion_id', '=', $topic->id)->count()}}</p>
                <p class="text-muted">{!! clean($topic->description) !!}</p>
                <hr>

                @if ( $comment && 'answer' === $operation )
                <p><i>{!! clean($comment->description) !!}</i></p>
                @endif

        </div>  
        <h3>@lang('global.project-discussions.fields.description')</h3>
        @if ( $comment )
        {!! Form::model($comment, ['method' => 'POST', 'route' => ['admin.project_discussions.comments-store', $project->id, $comment->discussion_id, $operation, $comment->id], 'files' => true]) !!}
        @else
        {!! Form::open(['method' => 'POST', 'route' => ['admin.project_discussions.comments-store', $project->id, $topic->id], 'files' => true]) !!}
        @endif        
       
            <div class="row">
            <?php
            $description = '';
            if ( $comment && 'edit' === $operation ) {
                $description = $comment->description;
            }
            ?>
            <div class="col-xs-4">
            {!! Form::textarea('description', old('description', $description), ['class' => 'form-control','rows'=>'5', 'placeholder' => trans('global.project-discussions.add-comment')]) !!}
            </div>
            <div class="col-xs-4">
            {!! Form::label('attachment', trans('global.project-discussions.attachment').'', ['class' => 'control-label']) !!}
            {!! Form::file('attachment', ['class' => 'form-control', 'style' => 'margin-top: 4px;']) !!}            
            <br/>
            @if($errors->has('attachment'))
                <p class="help-block">
                    {{ $errors->first('attachment') }}
                </p>
            @endif
             

            @if (!empty( $comment ) && !empty($comment->attachment))
                <a href="{{ asset(env('UPLOAD_PATH').'/'.$comment->attachment) }}" target="_blank">
                @lang('global.app_show')</a><br>
            @endif
            <?php
            $title = trans('global.project-discussions.comment');
            if ( $comment ) {
                if ( 'edit' === $operation ) {
                    $title = trans('global.app_update');
                } elseif( 'answer' === $operation ) {
                    $title = trans('global.project-discussions.answer');
                }
            }
            ?>
            {!! Form::submit($title, ['class' => 'btn btn-danger']) !!}
            </div>
            </div>
            <br/><br/>
        </div>
        
        {!! Form::close() !!}


        @include('admin.project_discussions.comments')


</div>
@stop
