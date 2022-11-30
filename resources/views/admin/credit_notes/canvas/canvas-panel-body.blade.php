<!-- summary -->
<?php
$statistics_type = getSetting( 'statistics-type', 'site_settings', 'default');

if ( 'progress' === $statistics_type ) {
?>
 @include('admin.credit_notes.canvas.canvas-progress', ['currency_id' => $currency_id])
<?php
} elseif ( 'circle' === $statistics_type ) {
 ?>
 <!-- summary -->
<div class="panel panel-default">
	<div class="panel-body table-responsive">
		@include('admin.credit_notes.canvas.canvas-circle', ['currency_id' => $currency_id])  
	</div>
</div>          
<?php } else {
  ?>
	@include('admin.credit_notes.canvas.canvas-default', ['currency_id' => $currency_id])  
  <?php
} ?>