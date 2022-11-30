<script>
    
$(function() {
    function initializelast12monthsincome() {
        Morris.Bar({
            element: 'last12monthsincome',
            data: [
            @foreach( $yearly_data['income_monthwise'] as $month => $income )
                {y: "{{$month}}", income: {{$income}}},
            @endforeach
            ],
            xkey: 'y',
            ykeys: [ 'income' ],
            barColors: ['#E67A77', '#79D1CF'],
            labels: [
                @foreach( $yearly_data['income_monthwise'] as $month => $income )
                    '{{$month}}',
                @endforeach
            ]
        });
    }
    initializelast12monthsincome();

    $(window).resize(function() {        
        setTimeout(function() {
            $("#last12monthsincome").empty();
            initializelast12monthsincome();
        }, 200);
    });
});
</script>