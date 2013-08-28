<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        var url=[<?php
                $urls=array_keys($value);
		    	foreach ($urls as $val)
		    	{
		    		echo '\''.$val.'\',';
		    	} 
		    	?>];
        var dolphin=[<?php
            	foreach ($value as $key=>$val)
		    	{
		    		echo $val['dolphin'].',';
		    	} 
		    	?>];
    	var uc=[<?php
            	foreach ($value as $key=>$val)
		    	{
		    		echo $val['uc'].',';
		    	} 
		    	?>];
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'totallymobile',
                type: 'bar',
                marginTop: 40,
                marginBottom: 80,
                height: url.length * 30 + 120// 20px per url item plus top and bottom margins
            },
            title: {
                text: 'Totally Loaded for Mobile Sites'
            },
            subtitle: {
                text: 'Dolphin VS UC'
            },
            xAxis: {
                //categories: ['Africa', 'America', 'Asia', 'Europe', 'Oceania'],
                categories: url,
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: '(seconds)',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            tooltip: {
                formatter: function() {
                    return ''+
                        this.series.name +': '+ this.y +' seconds';
                }
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -100,
                y: 100,
                floating: true,
                borderWidth: 1,
                backgroundColor: '#FFFFFF',
                shadow: true
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'dolphin',
                //data: [107, 31, 635, 203, 2]
                data: dolphin
            }, {
                name: 'uc',
                data: uc
            }]
        });
    });
    
});
</script>
<body id="dt_example">
<div id="container">
	<div class="full_width big"><?php echo $bname?>::Detail Info</div>
	<h1>Totally Loaded of Mobile Sites</h1>
	<div id="totallymobile"></div>
	<h1>Totally Loaded of PC Sites</h1>
</div>
</body>