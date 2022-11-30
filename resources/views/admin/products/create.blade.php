@extends('layouts.app')
@section('content')
   @include('admin.products.create-form')
   @include('admin.common.modal-loading-submit')
@stop
@section('javascript')
@parent

@include('admin.common.standard-ckeditor')

<script>
   $("#selectbtn-category").click(function(){
       $("#selectall-category > option").prop("selected","selected");
       $("#selectall-category").trigger("change");
   });
   $("#deselectbtn-category").click(function(){
       $("#selectall-category > option").prop("selected","");
       $("#selectall-category").trigger("change");
   });
</script>
<script>
   $("#selectbtn-tag").click(function(){
       $("#selectall-tag > option").prop("selected","selected");
       $("#selectall-tag").trigger("change");
   });
   $("#deselectbtn-tag").click(function(){
       $("#selectall-tag > option").prop("selected","");
       $("#selectall-tag").trigger("change");
   });
</script>

<script>
   $('.fillprices').click(function() {
       var actual_price = $('#actual_price').val();
       var sale_price = $('#sale_price').val();
       if ( actual_price == '' ) {
           alert('{{trans("global.product.enter-actual-price")}}');
           $('#actual_price').focus();
           return false;
       }
       
       if ( sale_price == '' ) {
           alert('{{trans("global.product.enter-sale-price")}}');
           $('#sale_price').focus();
           return false;
       }
       
       $.ajax({
           method: 'POST',
           url: $(this).data('target'),
           data: {
               _token: _token,
               actual_price: actual_price,
               sale_price: sale_price
           }
       }).done(function ( data ) {
           var data = $.parseJSON( data );
           $.each(data, function( key, val ) {
               //console.log( key );
               //console.log( val );
               $('#' + key).val( val );
           });
       });
   });
</script>

@include('admin.common.modal-scripts')
@stop