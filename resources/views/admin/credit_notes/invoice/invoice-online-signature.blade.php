<div class="hr-line-dashed"></div>
<div class="row">
    <div class="col-md-12">
        <div id="signaturePadArea"></div>
    </div>
</div>
<div class="hr-line-dashed"></div>

<div id="invoice-template">
<div class="row">
    <div class="col-md-6">
        <h4>@lang('custom.invoices.sign-above')</h4>
    </div>
    <div class="col-md-6 text-right">
        <button type="button" id="clearSignature" class="btn btn-danger btn-sm">@lang('custom.invoices.clear-signature')</button>
    </div>
</div>
</div>


@section('javascript')
    @parent
   <script src="{{ url('js/jSignature.min.js') }}"></script>
   <script>
    $(document).ready(function() {
        var $signaturePadArea = $("#signaturePadArea");
        $signaturePadArea.jSignature({
            color:"#000"
        });

        @if( ! empty( $invoice->signature ) )
            $signaturePadArea.jSignature("setData", "{{$invoice->signature}}");
        @endif
        
        
            $signaturePadArea.on('change', function(e){
                var signData = $signaturePadArea.jSignature("getData");
                var signDataOld = '';
                @if( ! empty( $invoice->signature ) )
                    signDataOld = "{{$invoice->signature}}";
                @endif
                
                
                if ( $signaturePadArea.jSignature("isModified") ) {
                    $.ajax({
                        url: "{{route('admin.invoice.save-invoice-signature')}}",
                        dataType: "json",
                        method: 'post',
                        data: {
                            invoice_id: '{{$invoice->id}}',
                            '_token': crsf_hash,
                            'signData': signData
                        },
                        success: function (data) {
                            
                            notifyMe(data.status, data.message);
                            
                        }
                    });
                }
                
            });
        

        $('#clearSignature').on('click',function () {
            $signaturePadArea.jSignature("reset");
        });
       
    });
    </script>
@stop