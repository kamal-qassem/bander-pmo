@extends('layouts.app')

@section('content')
    @include('admin.client_projects.operations.menu', compact('client_project'))

    <h3 class="page-title">@lang('global.client-projects.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">        
        <div class="row">
            <div class="col-lg-4">
                <?php
                if ( Route::current()->getName() == 'admin.client_projects.show' ) {
                    ?>
                    @include('admin.client_projects.operations.details', compact('client_project'))
                    <?php            
                }
                ?>
            </div>
            <div class="col-lg-2">
                @include('admin.client_projects.operations.proress', compact('client_project'))
            </div>
            <div class="col-lg-6">
                @include('admin.client_projects.operations.tasks', compact('client_project'))
            </div>
        </div>



            <p>&nbsp;</p>

            <a href="{{ route('admin.client_projects.index', $client_project->id) }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript')
    @parent
    
   @include('admin.common.standard-ckeditor')

    <script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ url('js/cdn-js-files/circle-progress') }}/circle-progress.js"></script>
    
    <script>

        function appProgressBar() {
            var progress_bars = $('body').find('.progress div.progress-bar');
            if (progress_bars.length) {
                progress_bars.each(function() {
                    var bar = $(this);
                    var perc = bar.attr("data-percent");
                    bar.css('width', (perc) + '%');
                    if (!bar.hasClass('no-percent-text')) { bar.text((perc) + '%'); }
                });
            }
        }

        appProgressBar();

        $(function(){
            moment.updateLocale('{{ App::getLocale() }}', {
                week: { dow: 1 } // Monday is the first day of the week
            });
            
            $('.date').datetimepicker({
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}",
            });
            
        });

        $(function(){
            var project_progress_color = '#84c529';
            var circle = $('.project-progress').circleProgress({fill: {
             gradient: [project_progress_color, project_progress_color]
           }}).on('circle-animation-progress', function(event, progress, stepValue) {
             $(this).find('strong.project-percent').html(parseFloat(100 * stepValue).toFixed(2) + '<i>%</i>');
           });
           });
    </script>
            
@stop
