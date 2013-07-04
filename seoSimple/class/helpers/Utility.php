<?php 
class Utility{
	public static function getTime(){
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		return $time;
	}
	
	public static function getEnd($start){
		$time = self::getTime();
		return round(($time - $start), 4);
	}
}
?>