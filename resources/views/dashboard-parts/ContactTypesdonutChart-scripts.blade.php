<script>
    
$(function() {
    function initializeContactTypesdonutChart() {
        Morris.Donut({
            element: 'ContactTypesdonutChart',
            data: [
            @foreach( $yearly_data['contacts_chart'] as $contact_type => $count )
                {label: "{{$contact_type}}", value: {{$count}}},
            @endforeach
            ],
            labelColor: '#23AE89',
            colors: ['#E67A77', '#D9DD81', '#79D1CF', '#95D7BB']
        });
    }
    initializeContactTypesdonutChart();

    $(window).resize(function() {        
        setTimeout(function() {
            $("#ContactTypesdonutChart").empty();
            initializeContactTypesdonutChart();
        }, 200);
    });
});
</script>