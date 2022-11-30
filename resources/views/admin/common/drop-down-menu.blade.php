<?php
$drop_down_display = false;
if( ! empty( $links ) ) {
   foreach( $links as $dlink ) {
      if ( ! empty( $dlink['permission_key'] ) && Gate::allows( $dlink['permission_key'] ) ) {
         $drop_down_display = true;
      }
   }
}
?>
@if( $drop_down_display )
<div class="btn-group">
   <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
   <span class="caret"></span>
   </a>
   <ul class="dropdown-menu dropdown-menu-right dropdown-menu-contact">
      @foreach( $links as $dlink )
         @if( 'delete' === $dlink['type'] && ! empty( $dlink['permission_key'] ) && Gate::allows( $dlink['permission_key'] ) )
            <li>
            {!! Form::open(array(
            'style' => 'display: inline-block;',
            'method' => 'DELETE',
            'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
            'id' => 'form_' . $record->id,
            'route' => [ $dlink['route'], $record->id ])) !!}
            {!!$dlink['icon']!!}&nbsp;{!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-link',)) !!}
            <input type="hidden" name="redirect_url" value="{{$dlink['redirect_url'] ?? ''}}">
            {!! Form::close() !!}
            </li>
         @elseif( ! empty( $dlink['permission_key'] ) && Gate::allows( $dlink['permission_key'] ) )
         <li>
         <a href="{{route($dlink['route'], $record->id)}}" target="_blank">
         {!!$dlink['icon']!!}{{$dlink['title']}}</a>
         </li>
         @endif
      @endforeach
   </ul>
</div>
@endif