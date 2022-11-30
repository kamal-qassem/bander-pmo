$(function() {
    $('form').validator({
        ignore:'.date',
		validHandlers: {
            '.customhandler':function(input) {
                //may do some formatting before validating
                input.val(input.val().toUpperCase());
                //return true if valid
                return input.val() === 'JQUERY' ? true : false;
            }
        }
    });

    $('.formvalidation').submit(function(e) {
        e.preventDefault();

        $('form').validator('check')
    })
})