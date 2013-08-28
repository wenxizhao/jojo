		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        var time=[<?php 
        		$times=array_keys($value);
		    	foreach ($times as $val)
		    	{
		    		echo '\''.$val.'\',';
		    	} 
        		?>]
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
                renderTo: 'container',
                type: 'line',
                marginRight: 130,
                marginBottom: 25
            },
            title: {
                text: '<?php echo $pname?>',
                x: -20 //center
            },
            subtitle: {
                text: 'Dolphin VS UC',
                x: -20
            },
            xAxis: {
                categories: time
            },
            yAxis: {
                title: {
                    text: 'Seconds (s)'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        this.x +': '+ this.y +'s';
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                borderWidth: 0
            },
            series: [{
                name: 'dolhpin',
                data: dolphin
            }, {
                name: 'uc',
                data: uc
            }]
        });
    });
    
});
		</script>
	</head>
	<body>
	<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>

	</body>