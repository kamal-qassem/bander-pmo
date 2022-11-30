<div class="col-md-{{$widget->columns}}">
    <div class="media state-media box-ws bg-4">
        <div class="media-left">
            <a href="{{ route('admin.products.index') }}"><div class="state-icn bg-icon-info"><i class="fa fa-database"></i></div></a>
        </div>
        <div class="media-body">
            <?php
                $products_count = \App\Product::count();
            ?>
            <h4 class="card-title">{{ $products_count }}</h4>
            <a href="{{ route('admin.products.index') }}">@lang('custom.dashboard.products')</a>
        </div>
    </div>
    <br/>
</div>
