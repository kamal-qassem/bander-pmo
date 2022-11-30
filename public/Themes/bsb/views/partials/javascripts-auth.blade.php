<script>
    window.deleteButtonTrans = '{{ trans("global.app_delete_selected") }}';
    window.copyButtonTrans = '{{ trans("global.app_copy") }}';
    window.csvButtonTrans = '{{ trans("global.app_csv") }}';
    window.excelButtonTrans = '{{ trans("global.app_excel") }}';
    window.pdfButtonTrans = '{{ trans("global.app_pdf") }}';
    window.printButtonTrans = '{{ trans("global.app_print") }}';
    window.colvisButtonTrans = '{{ trans("global.app_colvis") }}';
    window.fieldrequired = '{{ trans("global.this_field_required") }}';
</script>

<!--===============================================================================================-->  
    <script src="{{ themes('login-auth/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
<!--===============================================================================================-->
    <script src="{{ themes('login-auth/vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ themes('login-auth/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<!--===============================================================================================-->
    <script src="{{ themes('login-auth/vendor/select2/select2.min.js') }}"></script>
<!--===============================================================================================-->
    <script src="{{ themes('login-auth/vendor/tilt/tilt.jquery.min.js') }}"></script>

    
    
    <script>
        $('.js-tilt').tilt({
            scale: 1.1
        })
    </script>
<!--===============================================================================================-->
    <script src="js/main.js"></script>
    
<script>

(function ($) {
    "use strict";

    
    /*==================================================================
    [ Validate ]*/
    var input = $('.validate-input .input100');

    $('.validate-form').on('submit',function(){
        var check = true;

        for(var i=0; i<input.length; i++) {
            if(validate(input[i]) == false){
                showValidate(input[i]);
                check=false;
            }
        }

        return check;
    });


    $('.validate-form .input100').each(function(){
        $(this).focus(function(){
           hideValidate(this);
        });
    });

    function validate (input) {
        if($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
            if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
                return false;
            }
        }
        else {
            if($(input).val().trim() == ''){
                return false;
            }
        }
    }

    function showValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).addClass('alert-validate');
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).removeClass('alert-validate');
    }
    
    

})(jQuery);
</script>

