{!! Form::open(array(
'style' => 'display: inline-block;',
'method' => 'DELETE',
'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
'id' => 'form_' . $record->id,
'route' => [ $routeName,$record->id])) !!}
{!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger',)) !!}
<input type="hidden" name="redirect_url" value="{{$redirect_url ?? ''}}">
{!! Form::close() !!}