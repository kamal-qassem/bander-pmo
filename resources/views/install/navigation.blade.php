

<style type="text/css">
        .icon1 {
             align: center;
        }
        .icon2 {
        	  align: center;
        }
   @media(max-width: 767px){
   	      .row {
   	      	    width: 100%;
   	      }
   	      .icon1 {
   	      	 float: left;
   	      	 margin-top: 15px;
   	      	 margin-left: 18px;
   	      }
   	      .icon2 {
   	      	float: right;
   	      	margin-top: 18px;
   	      	margin-right: -5px;
   	      }
   }


</style>

<div class="row" style="margin-top: 60px;">
	<div class="col-md-6">
		<?php if ( ( ! empty( $steps['step1_active'] ) ) ) { ?>
		<a href="{{route('install.index')}}">
		<?php } ?>
	   
		<div class="<?php echo ! empty( $steps['step1_active'] ) ? $steps['step1_active'] : 'stage'; ?>">
			<div class="icon1">
			<div class="icon" style="text-align: center; font-size: 18px;">
				<span style="padding:13px 17px 13px 17px; border-radius: 50px; border: 2px solid #eaeaea; background-color: #fafafa; margin-top: 50px;">
		     1</div>
		<h4 style="text-align: center; margin-top: 20px; font-family: lato;
		font-size: 22px;">Requirements</h4>
	 </div>
	</div>
		<?php if ( ! empty( $steps['step1_active'] ) ) { ?>
		</a>
		<?php } ?>
	</div>
	<!-- <div class="col-md-4"></div> -->
	<div class="col-md-6">
		<?php if ( ! empty( $steps['step2_active'] ) ) { ?>
		<a href="{{route('install.project')}}">
		<?php } ?>
		
		<div class="<?php echo ! empty( $steps['step2_active'] ) ? $steps['step2_active'] : 'stage'; ?>">
			<div class="icon2">
			<div class="icon"style="text-align: center;font-size: 18px;"><span style="padding:13px 17px 13px 17px; border-radius:50px; border: 2px solid #eaeaea; background-color: #fafafa; margin-top: 30px;">2</div>
			<h4 style="text-align: center; margin-top: 20px; font-family: lato; font-size: 22px;">Database Details</h4>
		</div>
		</div>
		<?php if ( ! empty( $steps['step2_active'] ) ) { ?>
		</a>
		<?php } ?>
	</div>
	<!--
	<div class="col-md-4">
		
		<div class="<?php echo ! empty( $steps['step3_active'] ) ? $steps['step3_active'] : 'stage'; ?>">
			<div class="icon" style="text-align: center; font-size: 18px;">
				<span style="padding:13px 17px 13px 17px; border-radius:50px; border: 2px solid #eaeaea; background-color: #fafafa; margin-top: 30px;">3</span></div>
			<h4 style="text-align: center; margin-top: 25px; font-family: lato; font-size: 22px;">Installing</h4>
		</div>
	</div>
-->
</div>