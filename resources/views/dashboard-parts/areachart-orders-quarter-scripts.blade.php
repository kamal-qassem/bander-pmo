<script type="text/javascript">
function initializeareaChartQuarter() {
	Morris.Area({
            element: 'InvoicesYearsDataAreaChart',
            padding: 10,
            behaveLikeLine: true,
            gridEnabled: false,
            gridLineColor: '#dddddd',
            axes: true,
            fillOpacity: .7,
            data: [{
                period: '2010 Q1',
                iphone: 10,
                ipad: 10,
                itouch: 10
            }, {
                period: '2010 Q2',
                iphone: 1778,
                ipad: 7294,
                itouch: 18441
            }, {
                period: '2010 Q3',
                iphone: 4912,
                ipad: 12969,
                itouch: 3501
            }, {
                period: '2010 Q4',
                iphone: 3767,
                ipad: 3597,
                itouch: 5689
            }, {
                period: '2011 Q1',
                iphone: 6810,
                ipad: 1914,
                itouch: 2293
            }, {
                period: '2011 Q2',
                iphone: 5670,
                ipad: 4293,
                itouch: 1881
            }, {
                period: '2011 Q3',
                iphone: 4820,
                ipad: 3795,
                itouch: 1588
            }, {
                period: '2011 Q4',
                iphone: 25073,
                ipad: 5967,
                itouch: 5175
            }, {
                period: '2012 Q1',
                iphone: 10687,
                ipad: 34460,
                itouch: 22028
            }, {
                period: '2012 Q2',
                iphone: 1000,
                ipad: 5713,
                itouch: 1791
            }],
            lineColors: ['#ED5D5D', '#D6D23A', '#32D2C9'],
            xkey: 'period',
            ykeys: ['iphone', 'ipad', 'itouch'],
            labels: ['iPhone', 'iPad', 'iPod Touch'],
            pointSize: 0,
            lineWidth: 0,
            hideHover: 'auto'
        });
}
initializeareaChartQuarter();
</script>