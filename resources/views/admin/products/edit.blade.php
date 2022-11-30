@extends('layouts.app')
@section('content')
<h3 class="page-title">@lang('global.products.title')</h3>
{!! Form::model($product, ['method' => 'PUT', 'route' => ['admin.products.update', $product->id], 'files' => true,'class'=>'formvalidation']) !!}
<div class="panel panel-default">
   <div class="panel-heading">
      @lang('global.app_edit')
   </div>
   <div class="panel-body">
      @include('admin.products.form-fields')
   </div>
</div>
{!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
{!! Form::close() !!}

@include('admin.common.modal-loading-submit')
@stop
@section('javascript')
@parent

@include('admin.common.standard-ckeditor')

<script src="{{ asset('adminlte/plugins/fileUpload/js/jquery.iframe-transport.js') }}"></script>
<script src="{{ asset('adminlte/plugins/fileUpload/js/jquery.fileupload.js') }}"></script>
<script>
   $(function () {
       $('.file-upload').each(function () {
           var $this = $(this);
           var $parent = $(this).parent();
   
           $(this).fileupload({
               dataType: 'json',
               formData: {
                   model_name: 'Product',
                   bucket: $this.data('bucket'),
                   file_key: $this.data('filekey'),
                   accept: $this.data('accept'),
                   _token: '{{ csrf_token() }}'
               },
               add: function (e, data) {
                   data.submit();
               },
               done: function (e, data) {
                   $.each(data.result.files, function (index, file) {
                       if ( file.size > 0 ) {
                           var $line = $($('<p/>', {class: "form-group"}).html(file.name + ' (' + file.size + ' bytes)').appendTo($parent.find('.files-list')));
                           $line.append('<a href="#" class="btn btn-xs btn-danger remove-file">Remove</a>');
                           $line.append('<input type="hidden" name="' + $this.data('bucket') + '_id[]" value="' + file.id + '"/>');
                           if ($parent.find('.' + $this.data('bucket') + '-ids').val() != '') {
                               $parent.find('.' + $this.data('bucket') + '-ids').val($parent.find('.' + $this.data('bucket') + '-ids').val() + ',');
                           }
                           $parent.find('.' + $this.data('bucket') + '-ids').val($parent.find('.' + $this.data('bucket') + '-ids').val() + file.id);
                       } else {
                           var $line = $($('<p/>', {class: "form-group"}).html(file.name).appendTo($parent.find('.files-list')));
                           $line.append('<a href="#" class="btn btn-xs btn-danger remove-file">Not accepted</a>');
                       }
                   });
                   $parent.find('.progress-bar').hide().css(
                       'width',
                       '0%'
                   );
               }
           }).on('fileuploadprogressall', function (e, data) {
               var progress = parseInt(data.loaded / data.total * 100, 10);
               $parent.find('.progress-bar').show().css(
                   'width',
                   progress + '%'
               );
           });
       });
       $(document).on('click', '.remove-file', function () {
           var $parent = $(this).parent();
           $parent.remove();
           return false;
       });
   });
</script>
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
            
            $('#' + key).val( val );
        });
          });
   });
</script>
<style>
   .rowspecial {
   margin-right: 0px;
   margin-left: 0px;
   }
</style>
@include('admin.common.modal-scripts')
@stop