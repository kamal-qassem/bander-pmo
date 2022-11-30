<div class="col-md-{{$widget->columns}}">
    <div class="media state-media box-ws bg-1">
        <div class="media-left">
            <a href="{{ route('admin.users.index') }}"><div class="state-icn bg-icon-info"><i class="fa fa-users"></i></div></a>
        </div>
        <div class="media-body">
            <?php
                $users_count = \App\User::count();

            ?>
            <h4 class="card-title">{{ $users_count }}</h4>
            <a href="{{ route('admin.users.index') }}">@lang('custom.dashboard.users')</a>
        </div>
    </div>
    <br/>
</div>

