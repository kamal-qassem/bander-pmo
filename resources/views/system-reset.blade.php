@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-10">
            <div class="panel panel-default">
                <div class="panel-heading">@lang('custom.common.reset-title')</div>

                <div class="alert alert-warning" role="alert">
                  <span style="font-size: 18px;">@lang('custom.messages.alert-msg-reset')<br/>
                  @lang('global.contacts.fields.alert-msg-continue') </span>
                </div>

                <div class="panel-body table-responsive">
                    {!! Form::open(['method' => 'POST', 'route' => ['admin.home.system-reset'],'class'=>'formvalidation', 'id' => 'frmReset']) !!}
                    <div class="row">
                        <?php
                        $tables = [
                            'accounts' => [ 'title' => trans('global.accounts.title'), 'icon' => 'fa fa-anchor'],
                            'articles' => [ 'title' => trans('global.articles.title'), 'icon' => 'fa fa-bookmark-o'],
                            'assets' => [ 'title' => trans('global.assets.title'), 'icon' => 'fa fa-book'],
                            'contacts' => [ 'title' => trans('global.accounts.title'), 'icon' => 'fa fa-phone-square'],
                            'credit_notes' => [ 'title' => trans('global.credit_notes.title'), 'icon' => 'fa fa-file'],
                            'currencies' => [ 'title' => trans('global.currencies.title'), 'icon' => 'fa fa-money'],
                            'database_backups' => [ 'title' => trans('global.database-backup.title'), 'icon' => 'fa fa-database'],
                            'discounts' => [ 'title' => trans('global.discounts.title'), 'icon' => 'fa fa-dollar'],
                            'expenses' => [ 'title' => trans('global.expenses.title'), 'icon' => 'fa fa-arrow-circle-left'],
                            'faq_questions' => [ 'title' => trans('global.faq-questions.title'), 'icon' => 'fa fa-question'],
                            'incomes' => [ 'title' => trans('global.income.title'), 'icon' => 'fa fa-arrow-circle-right'],
                            'invoices' => [ 'title' => trans('global.invoices.title'), 'icon' => 'fa fa-credit-card'],
                            'languages' => [ 'title' => trans('global.languages.title'), 'icon' => 'fa fa-sign-language'],
                            'orders' => [ 'title' => trans('orders::global.orders.title'), 'icon' => 'fa fa-cart-plus'],
                            'products' => [ 'title' => trans('global.products.title'), 'icon' => 'fa fa-shopping-cart'],
                            'proposals' => [ 'title' => trans('global.proposals.title'), 'icon' => ''],
                            'purchase_orders' => [ 'title' => trans('global.purchase-orders.title'), 'icon' => 'fa fa-anchor'],
                            'quotes' => [ 'title' => trans('quotes::custom.quotes.title'), 'icon' => 'fa fa-question-circle'],
                            'roles' => [ 'title' => trans('global.roles.title'), 'icon' => 'fa fa-briefcase'],
                            'users' => [ 'title' => trans('global.users.title'), 'icon' => 'fa fa-user'],
                        ];
                        ?>
                        @foreach( $tables as $table => $arr )
                        <div class="col-md-2">
                            <div class="media state-media box-ws bg-4">
                                <div class="media-left">
                                    <?php
                                    if( 'users' == $table ) {
                                        $table = 'contacts';
                                    }
                                    ?>
                                    <a href="{{route('admin.' . $table . '.index')}}">
                                        @if( ! empty( $arr['icon'] ) )
                                           <div class="state-icn bg-icon-info"><i class="{{$arr['icon']}}"></i></div>
                                        @endif
                                    </a>
                                </div>
                                <div class="media-body">
                                                <h4 class="card-title">{{DB::table( $table )->count('id')}}</h4>
                                    <a href="{{route('admin.' . $table . '.index')}}">{{$arr['title']}}</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    

                    </div>
                    

                    {!! Form::submit(trans('global.app_reset'), ['class' => 'btn btn-danger wave-effect systemReset buttons', 'name' => 'reset']) !!}

                    <a href="{{ route('admin.home.dashboard') }}" class="btn btn-warning buttons">@lang('global.cancel')</a>
                    {!! Form::close() !!}

                    <div class="loadingpage text-center" style="display: none;" id="after_display">            
                        <p>Please Wait...</p>

                        <img width="200" src="{{url('images/loading-small.gif')}}">
                     </div>

                    <p>&nbsp;</p>

                    
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
<script type="text/javascript">
  $(".systemReset").click(function(e){
    e.preventDefault();
    if ( confirm('@lang("custom.messages.alert-reset")') ) {
        $('.buttons').hide();
        $('#after_display').show();
        
        $('#frmReset').submit();
    }
  });
  </script>
@endsection