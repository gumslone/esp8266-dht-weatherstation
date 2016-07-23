<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Temperature / Humidity Graph</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <script type="text/javascript">
	<?php
		$folder = '../sensors/logs/'; //sensor data folder
		$sensor_folders = glob($folder.'/*',GLOB_ONLYDIR|GLOB_BRACE);
		if(count($sensor_folders)>0)
		{
			$sensors = array();
			foreach ($sensor_folders as $folder) 
			{
				array_push($sensors, basename($folder));
			}
		}
		echo 'var sensors = '.json_encode($sensors);
	?>
		
		$(document).ready(function() {
			for (var i = 0; i < sensors.length; i++) {
				$('#charts').append('<div id="sensor_'+sensors[i]+'"><h3>Todays Chart for Sensor: '+sensors[i]+'</h3><div class="row"><div class="col-lg-4"><div class="well last_measurement"></div></div><div class="col-lg-4"><div class="well last_temperature"></div></div><div class="col-lg-4"><div class="well last_humidity"></div></div></div><div id="sensor_chart_'+sensors[i]+'"</div></div>');
				create_chart('sensor_chart_'+sensors[i]+'',sensors[i]);
			}
			
		});
function create_chart(container,sensor)
{
container = typeof container !== 'undefined' ? container : 'chart';

//var chart;
// define the options
var options = {
	chart: {
		renderTo: container,
		defaultSeriesType: 'line',
		marginRight: 45,
		marginTop: 30,
		marginBottom: 40
	},
	colors: ['#AA4643', '#3b6290', '#728a41', '#80699B', '#3D96AE', 
   '#DB843D', '#92A8CD', '#A47D7C', '#B5CA92'],
	credits: {
		enabled: false
	},
	title: {
		text: null,
		x: -20 //center
	},
	subtitle: {
		text: '',
		x: -20
	},
	global: {
                useUTC: false
            },
	xAxis: {
            type: 'datetime',
            tickInterval: 2,
               labels: {
                format: '{value: %H:%M}',
                align: 'right',
                rotation: -45,
			    align: 'right',
            } },
	yAxis: [{
		min: 0,
		title: {
			text: ''
		},
		plotLines: [{
			value: 0,
			width: 1,
		}],
		
		labels: {
			
			formatter: function() {
				return this.value + ' °C';
			},
			style: {
			}
		}
	}, { // Secondary yAxis
		min: 0,
		title: {
			text: '',
			style: {
			}
		},
		labels: {
			
			formatter: function() {
				return this.value;
			},
			style: {
			}
		},
		opposite: true
	}],
	tooltip: {
		formatter: function() {
			var s = '<b>'+ Highcharts.dateFormat('%Y-%m-%d (%H:%M)',this.x) +'</b>';
			var imp = 1;
			var clk = 0;
			$.each(this.points, function(i, point) {
				s += '<br/><span style="color:'+point.series.color+';">'+ point.series.name +'</span>: ';
				
				s += point.y +'';
				if(point.series.name == 'Temperature') s += ' °C';
				
			});
 			//if(clk > 1 && imp > 1)s += '<br/>CTR: '+(Math.round((clk/imp)*1000)/1000)*100+'%';
			return s;
		},
		crosshairs: {
				width: 30,
				color: '#eeeeee',
				zIndex: 0.5
		},
		shared: true
	},
	legend: {
		align: 'right',
		verticalAlign: 'top',
		y: -15,
		floating: false,
		borderWidth: 0
	},
	series: [{
			name: 'Temperature',
			yAxis: 0,
			data: [0,0,0,0,0,0,0]
		},
		{
			name: 'Humidity',
			yAxis: 1,
			data: [0,0,0,0,0,0,0]
	}],
	navigation: {
	buttonOptions: {
		verticalAlign: 'bottom',
		y: 0
		}
	}
}

	$.getJSON('./chart_data.php?sensor='+sensor+'', function(json) {
	
		options.xAxis.categories = json[0];
		options.series[0].data = json[1];
		options.series[1].data = json[2];
		chart = new Highcharts.Chart(options);
		
		
		$('#sensor_'+sensor+' .last_measurement').html('<h4>Last measurement</h4>'+Highcharts.dateFormat('%Y-%m-%d (%H:%M) UTC',json[0][json[0].length-1]))+'';
		var last_temp = json[1][json[1].length-1];
		var last_temp_html = '<span style="font-size:50px">'; 
		if(last_temp < 18)
		{
			last_temp_html += '<img src="./images/temperature_low.svg" data-toggle="tooltip" title="Low!" style="height:50px;vertical-align: middle"/> ';
		}
		else if (last_temp > 24)
		{
			last_temp_html += '<img src="./images/temperature_high.svg" data-toggle="tooltip" title="High!" style="height:50px;vertical-align: middle"/> ';
		}
		else
		{
			last_temp_html += '<img src="./images/temperature_middle.svg" data-toggle="tooltip" title="Normal" style="height:50px;vertical-align: middle"/> ';
		}
		last_temp_html += ''+ last_temp +' °C</span>';
		$('#sensor_'+sensor+' .last_temperature').html(last_temp_html);
		
		var last_humi = json[2][json[2].length-1];
		var last_humi_html = '<span style="font-size:50px">';
		if(last_humi < 30)
		{
			last_humi_html += '<img src="./images/humidity_low.svg" data-toggle="tooltip" title="Low!" style="height:50px;vertical-align: middle"/> ';
		}
		else if (last_humi > 50)
		{
			last_humi_html += '<img src="./images/humidity_high.svg" data-toggle="tooltip" title="High!" style="height:50px;vertical-align: middle"/> ';
		}
		else
		{
			last_humi_html += '<img src="./images/humidity_middle.svg" data-toggle="tooltip" title="Normal" style="height:50px;vertical-align: middle"/> ';
		}
		last_humi_html += ''+ last_humi +'</span>';
		$('#sensor_'+sensor+' .last_humidity').html(last_humi_html);
		$('[data-toggle="tooltip"]').tooltip(); 
	});
}


        </script>
        <script src="http://code.highcharts.com/highcharts.js"></script>
        <script src="http://code.highcharts.com/modules/exporting.js"></script>
    
    
  </head>
  <body>
    <div class="container">
	  <div class="jumbotron">
	    <h1>Temperature / Humidity Graph</h1>
	    <p>Temperature / Humidity measured with DHT22 and delivered to the server with ESP8266-03</p> 
	  </div>
	  
	  <div class="row">
	    <div class="col-sm-12">
	      
	      <div id="charts">
		  
	      </div>
	    </div>
	  </div>
	  
	 	  
	  
	  
	</div>
		
		
		
		
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>

<?php
	
	
	
?>