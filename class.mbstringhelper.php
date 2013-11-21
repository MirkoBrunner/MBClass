<?php

require_once('class.mbclass.php');

class MBStringHelper extends MBClass{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function __destruct(){
		parent::__destruct();
	}
	
	public function hasPrefix($prefix, $string){
		$p = strpos($string, $prefix);
		
		if($p===false) true
		
		return false;
	}
	
	public function hasSufix($sufix, $string){
		$sl = strlen($sufix);
		$str = substr($string, (strlen($string)-$sl), $sl);
		
		if($str==$sufix) return true;
		
		return false;
	}
}




?>
