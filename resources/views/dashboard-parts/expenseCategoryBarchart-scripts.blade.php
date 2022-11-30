<script>
    
$(function() {
    function initializeexpenseCategoryBarchart() {
        Morris.Bar({
            element: 'expenseCategoryBarchart',
            data: [
                @foreach( $yearly_data['expenses'] as $expense_category_chart => $expense_chartsingle )
                    {y: '{{$expense_category_chart}}', expense: {{$expense_chartsingle}}},
                @endforeach
            ],
            xkey: 'y',
            ykeys: ['expense'],
            barColors: ['#D9DD81', '#79D1CF', '#95D7BB'],
            labels: ['{{trans("global.expense.title")}}']
        });
    }
    initializeexpenseCategoryBarchart();

    $(window).resize(function() {        
        setTimeout(function() {
            $("#expenseCategoryBarchart").empty();
            initializeexpenseCategoryBarchart();
        }, 200);
    });
});
</script>