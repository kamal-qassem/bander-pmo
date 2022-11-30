<script>
var d_csrf=crsf_token+'='+crsf_hash;

$('#customer_id').change( function () {
    $.ajax({
        url: '{{url('admin/search_products')}}/products',
        dataType: "json",
        method: 'post',
      data: 'customer_id='+$('#customer_id').val()+'&type=customer&row_num=1&'+d_csrf,
        success: function (data) {
            $('#address').val( data.address );
        }
    });
});
</script>