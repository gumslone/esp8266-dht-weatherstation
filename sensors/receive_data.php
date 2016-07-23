<?php
#error_reporting(1);
#file_put_contents('./logs/'.time().'.txt', print_r($_SERVER,true));
echo 'OK';
#print_r($_SERVER);

if($_REQUEST['key']=='blabla')
{
	if(!empty($_REQUEST['sensor']))
	{
		if(!file_exists('./logs/'.$_REQUEST['sensor'].''))
		{
			mkdir('./logs/'.$_REQUEST['sensor'].'');
		}
		$sensor_dir = $_REQUEST['sensor'].'/';
	}
	
	file_put_contents('./logs/'.$sensor_dir.date("Y-m-d").'_sensor_data.txt', 'time='.time().'&'.$_SERVER['QUERY_STRING']."\n", FILE_APPEND | LOCK_EX);
}
?>