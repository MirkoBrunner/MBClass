<?php
/*
Copyright 2013 Mirko Brunner for fiveandfriends GmbH
mirko.brunner@googlemail.com
*/
error_reporting(E_ALL & ~E_NOTICE);

class MBPHPDebug{
	
	function __construct(){
		
	}
	
	function __destruct(){
		
	}
	
	function turnErrorReportingOn(){

	}
	
	function d(&$t){
		echo '<pre>';
		var_dump($t);
		echo '<pre>';
	}
	
	function btrace(){
		//needed helperfunction for human reading (output)
    	$this->d(debug_backtrace());
	}
	
	function btraceIgnoreARG(){
		//needed helperfunction for human reading (output)
		$this->d(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
	}
	
	function btraceFormated($ignore = 2){ 
    	array_walk( debug_backtrace(), create_function( '$a,$b', 'print "<br /><b>". basename( $a[\'file\'] ). "</b> &nbsp; <font color=\"red\">{$a[\'line\']}</font> &nbsp; <font color=\"green\">{$a[\'function\']} ()</font> &nbsp; -- ". dirname( $a[\'file\'] ). "/";' ) ); 
    }
    
    function lastErr(){
	    $this->d(error_get_last());
    }
    
    function triggerErr($message=null){
	    trigger_error($message, E_USER_ERROR);
    }	
    
    function memUsage(){
    	echo memory_get_usage();
    }
}

?>
