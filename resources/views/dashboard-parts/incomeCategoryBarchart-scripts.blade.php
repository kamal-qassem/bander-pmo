<script>
    
$(function() {
    function initializeincomeCategoryBarchart() {
        Morris.Bar({
            element: 'incomeCategoryBarchart',
            data: [
                @foreach( $yearly_data['income'] as $income_category => $income )
                    {y: "{{$income_category}}", income: {{$income}}},
                @endforeach
            ],
            xkey: 'y',
            ykeys: ['income'],
            barColors: ['#95D7BB', '#79D1CF'],
            labels: ['{{trans("global.income.title")}}']
        });
    }
    initializeincomeCategoryBarchart();

    $(window).resize(function() {        
        setTimeout(function() {
            $("#incomeCategoryBarchart").empty();
            initializeincomeCategoryBarchart();
        }, 200);
    });
});
</script>