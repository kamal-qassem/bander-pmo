<ul class="nav " role="tablist">
<li role="presentation" class=" nav-item" ><a  class="nav-link active" href="#progress" aria-controls="progress" role="tab" data-toggle="tab">@lang('others.statistics.progress')</a></li>
<li role="presentation" class="nav-item"><a  class="nav-link" href="#circle" aria-controls="circle" role="tab" data-toggle="tab" data-bs-target="#circle">@lang('others.statistics.circle')</a></li>
</ul>
<div class="tab-content">

<div role="tabpanel" class="tab-pane active" id="progress">
	@include('admin.client_projects.canvas.canvas-progress')
</div>
<div role="tabpanel" class="tab-pane" id="circle">
	@include('admin.client_projects.canvas.canvas-circle') 
</div>
</div>