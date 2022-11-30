@extends('layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.income.title')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-12">
                    @include('admin.incomes.receipt-menu', compact('income'))
                    @include('admin.incomes.receipt-content', compact('income'))                    
                </div>
            </div>

            <a href="{{ route('admin.incomes.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>

    
@stop

@section('javascript')
    @parent
    @include('admin.incomes.scripts', compact('income'))

	<script type="text/javascript">
	function printItem( elem ) {
		var mywindow = window.open('', 'PRINT', 'height=400,width=600' );

		var url = '{{themes("plugins/bootstrap/css/bootstrap.css")}}';
		var themecss_url = '{{ themes("css/style.css") }}';
		mywindow.document.write('<html><head>' );
		mywindow.document.write('<link href="' + url + '" rel="stylesheet" type="text/css" media="print">');
		mywindow.document.write('<link href="' + themecss_url + '" rel="stylesheet" type="text/css" media="print">');
		mywindow.document.write('</head><body >' );
		
		mywindow.document.write(document.getElementById(elem).innerHTML);
		mywindow.document.write('</body></html>' );

		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10*/

		mywindow.print();
		mywindow.close();

		return true;
	}
	printItem( 'income' );
	</script>	
@stop