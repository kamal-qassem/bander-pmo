<ul class="nav" role="tablist">
<li role="presentation" class="active"><a href="#progress" aria-controls="progress" role="tab" data-toggle="tab">@lang('others.statistics.progress')</a></li>
<li role="presentation" class=""><a href="#circle" aria-controls="circle" role="tab" data-toggle="tab">@lang('others.statistics.circle')</a></li>
</ul>
<div class="tab-content">

<div role="tabpanel" class="tab-pane active" id="progress">
	@include('admin.client_projects.canvas.canvas-progress')
</div>
<div role="tabpanel" class="tab-pane" id="circle">
	@include('admin.client_projects.canvas.canvas-circle') 
</div>
</div>