<!-- summary -->
<?php
$statistics_type = getSetting( 'statistics-type', 'site_settings', 'default');

if ( 'progress' === $statistics_type ) {
?>
 @include('admin.tasks.canvas.canvas-progress')
<?php
} elseif ( 'circle' === $statistics_type ) {
 ?>
 <!-- summary -->
<div class="panel panel-default">
	<div class="panel-body table-responsive">
		@include('admin.tasks.canvas.canvas-circle')  
	</div>
</div>          
<?php } else {
  ?>
	@include('admin.tasks.canvas.canvas-default')  
  <?php
} ?>