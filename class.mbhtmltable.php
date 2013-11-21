<?php

class MBHTMLTable extends MBClass{
	public $rows;
	public $cols;
	public $tableClass;
	public $rowClass;
	public $cellClass;
	public $headerRowCass;
	public $headerCellClass;
	
	public $makeHeader;
	private $temp;
	private $keys;
	
	
	public function __construct(){
		parent::__construct();
		
		$this->makeHeader = true;
		$this->rows = 0;
		$this->cols = 0;
		$this->tableClass = "";
		$this->rowClass = "";
		$this->cellClass = "";
	}
	
	public function __destruct(){
		parent::__destruct();
	}
	
	public function build($array){
		$this->temp = $array;
		unset($array);
		
		$cont = "";
		
		$this->parseKeys();
		$this->computeRowsCount();
		$this->computeColsCount();
		
		
		$cont.= $this->beginnTable();
		$cont.= $this->renderHeaderRow();
		
		for($i=0;$i<$this->rows+1;$i++){
			$cont.=$this->renderRow();
		}
		
		$cont.= '</table>';
		
		return $cont
	}
	
	private function parseKeys(){
		$_keys = array_keys($this->temp[0]);
		$k;
		$keys = array();
		
		foreach($_keys as $k){
			if(!is_numeric($k))
				array_push($keys, $k);
		}
		
		$this->keys = $keys;
		
		unset($keys);
		unset($_keys);
	}
	
	private function computeColsCount(){
		$this->cols = count($this->keys);
	}
	
	private function computeRowsCount(){
		$this->rows = ($this->makeHeader==true) ? (count($this->temp)+1) : count($this->temp);
	}
	
	private function renderHeaderRow(){
		$cont = "";
		
		$c = $this->cols;
		$cl = ($this->headerRowClass!="") ? ('class="'.$this->headerRowClass.'"') : '';
		$cr = ($this->headerCellClass!="") ? ('class="'.$this->headerCellClass.'"') : '';
		
		$cont.='<tr '.cl.'`>';
		
		$df = function_exists('MBHTMLTable_renderHEaderCells', $args);
		
		for($i=0;$i<$count;$j++){
			$cont.='<th '.$cr.'>';
			
			if($df!==false){
				// oki kein Plan wie ich das aufrufen soll... ;/
			}
			
			$cont.= $this->keys[$i];
			
			$cont.='</th>';
			
		}
		
		$cont.= '</tr>';
		
		unset($c);
		unset($cl);
		unset($cr);
		unset($df);
			
		return $cont;
	}
	
	private function renderRow($row){
		
		$cont = "";
		$cc = ($this->cellClass!="") ? ('class="'.$this->cellClass.'"') : '';
		$cr = ($this->rowClass!="") ? ('class="'.$this->rowClass.'"') : '';
		
		$cont.= '<tr '.$cc.'>';
		
		for($i=0;$i<$this->cols;$i++){
			
			$cont.= '<td '.$cr.'>';
			
			$cont.= $row[$i];
			
			$cont.= '</td>';
		}
		
		$cont.= '</tr>';
		
		return $cont;
	}
	
	private function beginnTable(){
		$cont = "";
		
		$cr = ($this->tableClass!="") ? ('class="'.$this->tableClass.'"') : '';
		
		$cont.= '<table '.$cr.' border="02 cellspacing="0" cellpading="0">';
		
		return $cont;
	}
	
	
}




?>
