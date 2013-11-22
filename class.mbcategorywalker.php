<?php
/*
Copyright 2013 Mirko Brunner for me
mirko.brunner@googlemail.com

Category-Walker for TYPO3 tables

*/


class MBCategoryWalker{
	private $oldPid;
	private $pidFieldName;
	private $uidFieldName;
	private $tableName;
	private $arr;
	private $mainQuery;
	private $where;	
	
	function GetCategoriesClass(){
		$this->oldPid = -1;
		$this->pidFieldName = null;
		$this->uidFieldName = null;
		$this->tableName = null;
		$this->arr = null;
		$this->mainQuery = null;
		$this->where = null;
	}
	
	function _destruct(){
		$this->oldPid = -1;
		$this->pidFieldName = null;
		$this->uidFieldName = null;
		$this->tableName = null;
		$this->arr = null;
		$this->mainQuery = null;
		$this->where = null;
	}
	
	public function setPIDFieldName($name){
		if(is_string($name)){
			$this->pidFieldName = $name;
		}else{
			echo __CLASS__."::".__METHOD__." no string given<br />";
		}
	}
	
	public function setUIDFiedName($name){
		if(is_string($name)){
			$this->uidFieldName = $name;
		}else{
			echo __CLASS__."::".__METHOD__." no string given<br />";
		}
	}
	
	public function setTableName($name){
		if(is_string($name)){
			$this->tableName = $name;
		}else{
			echo __CLASS__."::".__METHOD__." no string given<br />";
		}
	}
	
	public function setWhere($where){
		if(is_string($where)){
			$this->where = $where;
		}else{
			echo __CLASS__."::".__METHOD__." no string given<br />";
		}
	}
	
	public function resetMe(){
		$this->oldPid = -1;
		$this->arr = null;
	}
	
	public function getCategoriesWith($pID,$table,$pidF,$uidF){
		$this->setPIDFieldName($pidF);
		$this->setUIDFiedName($uidF);
		$this->setTableName($table);
		
		$this->buildQuery();
		
		return $this->getCategoriesWithPID($pID);
	}
	
	public function getCategoriesWithPID($pID){
		if(!is_numeric($pID) || $pID<0){
			echo __CLASS__."::".__METHOD__." pid is not numeric or less then zero<br />";
		 	return;
		}
		if($this->pidFieldName==null){
			echo __CLASS__."::".__METHOD__." pidFieldName is null<br />";
			return;
		}
		if($this->uidFieldName==null){
			echo __CLASS__."::".__METHOD__." uidFieldName is null<br />";
			return;
		}
		if($this->tableName==null){
			echo __CLASS__."::".__METHOD__." tableName is null<br />";
			return;
		}
		
		if($this->oldPid==$pID) return $this->arr;
		
		$this->oldPid = $pID;
		
		$this->buildQuery();
		
		return $this->getAllCatsByParentID($pID);
	}
	
	private function buildQuery(){
		$this->mainQuery = "SELECT ".$this->uidFieldName.",".$this->pidFieldName." FROM ".$this->tableName." WHERE ".$this->pidFieldName." = ";
		if($this->where!=null) $this->where = " AND deleted = '0' ".$this->where;
		else $this->where =  " AND deleted = '0'";
	}
	
	private function takeQuery($pID){
		return $GLOBALS['TYPO3_DB']->sql_query($this->mainQuery.$pID.$this->where);
	}
	
	private function traverseCats($u){
		$res2 = $this->takeQuery($u);
		while($cf = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)){
			$u1 = $cf[$this->uidFieldName];
			if(array_search($u1,$this->arr)===false){ $this->arr[] = $u1; }
			$this->traverseCats($u1);
		}
		return 0;
	}
	
	private function getAllCatsByParentID($pID){
		$res = $this->takeQuery($pID);
		while($cats = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
			$parent_id = $cats[$this->pidFieldName];
			$uid = $cats[$this->uidFieldName];
			if(count($this->arr)==0) $this->arr[] = $uid;
			if(array_search($uid,$this->arr)===false){ $this->arr[] = $uid; }
			$this->traverseCats($uid);
		}
		if($this->arr!=null) sort($this->arr,SORT_NUMERIC);
		return $this->arr;
	}
}


?>
