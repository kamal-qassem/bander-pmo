<script>
    
$(function() {
    function initializeexpensecategorydonutChart() {
        Morris.Donut({
            element: 'expensecategorydonutChart',
            data: [
            @foreach( $yearly_data['expenses'] as $expense_category => $expense_chart )
                {label: "{{$expense_category}}", value: {{$expense_chart}}},
            @endforeach
            ],
            labelColor: '#23AE89',
            colors: ['#E67A77', '#D9DD81', '#79D1CF', '#95D7BB']
        });     
    }
    initializeexpensecategorydonutChart();

    $(window).resize(function() {        
        setTimeout(function() {
            $("#expensecategorydonutChart").empty();
            initializeexpensecategorydonutChart();
        }, 200);
    });
});
</script>