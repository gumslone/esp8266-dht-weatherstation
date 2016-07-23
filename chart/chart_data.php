<?php
	
if(!empty($_REQUEST['sensor']))
{
	$sensor_dir = $_REQUEST['sensor'].'/';
}

$file = "../sensors/logs/".$sensor_dir.date("Y-m-d")."_sensor_data.txt";
if(!file_exists($file))
{
	$file = "../sensors/logs/".$sensor_dir.date("Y-m-d",strtotime("-1 days"))."_sensor_data.txt";
}

$contents = file_get_contents($file);
$lines = explode(PHP_EOL, $contents);

foreach($lines as $line)
{
	if(!empty($line))
	{
		$values = explode('&', $line);
		for($i=0;$i<3;$i++)
		{
			
			$data = explode('=', $values[$i]);
		
			if($i==0) 
				$out_data[$i][] = $data[1] * 1000;
			else
				$out_data[$i][] = $data[1];
		}
	}
}

echo json_encode($out_data,JSON_NUMERIC_CHECK);

?>