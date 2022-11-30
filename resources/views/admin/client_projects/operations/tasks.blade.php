
<div class="row">
	<div class="col-md-12">
		<div class="col-md-6">

			<p class="text-uppercase bold text-dark font-medium">
			  <strong><?php echo $tasks_not_completed; ?> / <?php echo $total_project_tasks; ?> {{trans( 'global.client-projects.open-tasks' )}}</strong>

			  <i class="fa fa-check-circle<?php if($tasks_not_completed_progress >= 100){echo ' text-success';} ?>" aria-hidden="true" style="float:right;"></i>
			</p>
			<p class="text-muted bold"><?php echo $tasks_not_completed_progress; ?>%</p>
			<div class="col-md-12 mtop5">
				<div class="progress no-margin progress-bar-mini">
				  <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $tasks_not_completed_progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $tasks_not_completed_progress; ?>">
				  </div>
				</div>
			</div>			
		</div>
		<?php if($project->due_date){ ?>
		<div class="col-md-6">
		  
		     
		        <p class="text-uppercase bold text-dark font-medium">
		           <strong><?php echo $project_days_left; ?> / <?php echo $project_total_days; ?> @lang('global.client-projects.days-left')</strong>

		           <i class="fa fa-calendar-check-o <?php if($project_time_left_percent >= 100){echo ' text-success';} ?>" aria-hidden="true" style="float:right;"></i>
		        </p>
		        <p class="text-muted bold"><?php echo $project_time_left_percent; ?>%</p>
		     
		     
		     <div class="col-md-12 mtop5">
		        <div class="progress no-margin progress-bar-mini">
		           <div class="progress-bar<?php if($project_time_left_percent == 0){echo ' progress-bar-warning ';} else { echo ' progress-bar-success ';} ?>no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $project_time_left_percent; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $project_time_left_percent; ?>">
		           </div>
		        </div>
		     </div>
		  
		</div>
		<?php } ?>
	
	<?php
	$projectObj = new \App\ClientProject();
	$currency_id = getDefaultCurrency('id');
	if ( ! empty( $project->currency_id ) ) {
	    $currency_id = $project->currency_id;
	} elseif ( ! empty( $project->client->currency_id ) ) {
	    $currency_id = $project->client->currency_id;
	}
	?>
	@if($project->billing_type_id == PROJECT_BILLING_TYPE_PROJECT_HOURS || $project->billing_type_id == PROJECT_BILLING_TYPE_TASK_HOURS)
	<br/>
	<hr>
	<div class="col-md-12 project-overview-logged-hours-finance">
		<div class="col-md-3 logged-time-sec" style="border:1px solid lightgray; border-radius: 3px;">
		<?php
		$data = $projectObj->total_logged_time_by_billing_type( $project->id );
		
		?>
		<p class="text-uppercase text-muted">@lang('global.client-projects.logged-hours')<span class="bold"><br/><?php echo $data['logged_time']; ?></span></p>
		<p class="bold font-medium">{{digiCurrency($data['total_money'], $currency_id)}}</p>
		</div>

		<div class="col-md-3 logged-time-sec" style="border:1px solid lightgray; border-radius: 3px;">
	        <?php
	        $data = $projectObj->data_billable_time($project->id);
	        ?>
	        <p class="text-uppercase text-info">@lang('global.client-projects.billable-hours')<span class="bold"><br/><?php echo $data['logged_time'] ?></span></p>
	        <p class="bold font-medium"><?php echo digiCurrency($data['total_money'], $currency_id); ?></p>
	    </div>

	    <div class="col-md-3 logged-time-sec" style="border:1px solid lightgray; border-radius: 3px;">
			<?php
			$data = $projectObj->data_billed_time($project->id);
			?>
			<p class="text-uppercase text-success">@lang('global.client-projects.billed-hours') <span class="bold"><br/><?php echo $data['logged_time']; ?></span></p>
			<p class="bold font-medium"><?php echo digiCurrency($data['total_money'], $currency_id); ?></p>
	    </div>

		<div class="col-md-3 logged-time-sec" style="border:1px solid lightgray; border-radius: 3px;">
			<?php
			$data = $projectObj->data_unbilled_time($project->id);
			?>
			<p class="text-uppercase text-danger">@lang('global.client-projects.unbilled-hours') <span class="bold" ><br/><?php echo $data['logged_time']; ?></span></p>
			<p class="bold font-medium"><?php echo digiCurrency($data['total_money'], $currency_id); ?></p>
		</div>
	
	<div class="row" >
	   <div class="col-md-12 project-overview-expenses-finance">
	      <div class="col-md-3 logged-time-sec" style="border:1px solid lightgray; border-radius: 3px;height:130px;">
	         <p class="text-uppercase text-muted">@lang('global.client-projects.expenses')</p>
	         <?php
	         $sum = \App\Expense::where('project_id', $project->id)->sum('amount');
	         ?>
	         <p class="bold font-medium">{{digiCurrency($sum, $currency_id)}}</p>
	      </div>
	      <div class="col-md-3 logged-time-sec"  style="border:1px solid lightgray; border-radius: 3px;height:130px;">
	         <p class="text-uppercase text-info">@lang('global.client-projects.expenses-billable')</p>
	         <?php
	         $sum = \App\Expense::where('project_id', $project->id)->where('billable', 'yes')->sum('amount');
	         ?>
	         <p class="bold font-medium">{{digiCurrency($sum, $currency_id)}}</p>
	      </div>
	      <div class="col-md-3 logged-time-sec"  style="border:1px solid lightgray;border-radius: 3px; height:130px;">
	         <p class="text-uppercase text-success">@lang('global.client-projects.expenses-billed')</p>
	         <?php
	         $sum = \App\Expense::where('project_id', $project->id)->where('billable', 'yes')->where('billed', 'yes')->sum('amount');
	         ?>
	         <p class="bold font-medium">{{digiCurrency($sum, $currency_id)}}</p>
	      </div>
	      <div class="col-md-3 logged-time-sec"  style="border:1px solid lightgray; border-radius: 3px; height:130px;">
	         <p class="text-uppercase text-danger">@lang('global.client-projects.expenses-unbilled')</p>
	         <?php
	         $sum = \App\Expense::where('project_id', $project->id)->where('billable', 'yes')->where('billed', 'no')->sum('amount');
	         ?>
	         <p class="bold font-medium">{{digiCurrency($sum, $currency_id)}}</p>
	      </div>
	   </div>
	</div>
	@endif
	</div>
</div>
	</div>