<?php

require_once('class.mbclass.php');

// Wrapper for standard TCP connections
class MBDB extends MBClass{
	// !!! TODO: secure secure secure !!!
	private $port;	
	private $host;
	private $user;		
	private $pass;	
	private $dbname;
	private $linkid;
	
	public function __construct(){
		parent::__construct();
		
		//set to defaults
		$this->port = 3306;
		$this->host = "localhost";
		$this->user = NULL;
		$this->pass = NULL;
		$this->dbname = NULL;
		$this->linkid = NULL;
	}
	
	public function __destruct(){
		parent::__destruct();
		
	}
	
	// SET-FUNKTIONEN ---------------------
	/*! \brief setzen des Namens der Datenbank und versucht sich mit ihr zu verbinden.
	*  	\param $dbname string\n
	*	\brief der Name der Datenbank
	*	\return bool\n
	*	\brief True wenn die Operation erfolgreich war. Ansonten false.
	*/
	function setDBName($dbname){
		if($dbname==$this->dbname) return false;
		
		$this->close();
		$temp = $this->dbname;
		$this->dbname = $dbname;
		
		if($this->connect()){
			$this->selectDB();
			return true;
		}else{
			$this->dbname= $temp;
			return false;
		}
	}
	
	// Haupthilfsfunktionen ---------------
	/*! \brief schließen der Datenbank bzw. beenden der SQL-Connection.
	*	\return bool\n
	*	\brief True wenn die Operation erfolgreich war. Ansonten false.
	*/
	function close(){
		try{
			if(@mysql_close($this->linkid)){
				$this->linkid = 0;
				return true;
			}else{
				throw new Exception(mysql_errno());
			}
		}catch(Exception $e){
			echo $this->err($e->getMessage(),mysql_error());
			return false;
		}
	}
	
	// connect-----------------------
	function connect(){
		try{
			if($lk = @mysql_connect($this->host.":".$this->port,$this->user,$this->pass,false)){
				$this->linkid = $lk; 
				return true;
			}else{
				throw new Exception(mysql_errno());
			}
		}catch(Exception $e){
			echo $this->err($e->getMessage(),mysql_error());
			return false;
		}
	}

// select------------------------	
	private function selectDB(){
		try{
			if(@mysql_select_db($this->dbname,$this->linkid)){
				return true;
			}else{
				throw new Exception(mysql_errno());
			}
		}catch(Exception $e){
			echo $this->err($e->getMessage(),mysql_error());
			return false;
		}
	}
	
	// query -----------------------
//			if_error = wenn fehlerausgabe erwuenscht (Oeberlagert $this->debug)
	function query($statement,$if_error=false){
		if($this->debug==true) $if_error=true;
		
		try{
			if($res = @mysql_query($statement,$this->linkid)){
				$this->nrows = @mysql_num_rows($res);
				$this->nfield = @mysql_num_fields($res);
				$this->result = $res;
				return $this->result;
			}else{
				throw new Exception(mysql_errno($this->linkid));
			}
		}catch(Exception $e){
			if($if_error){
				echo $this->err($e->getMessage(),mysql_error($this->linkid),$statement);
				$this->nrows = 0;
				$this->nfield = 0;
				$this->result = 0;
				return 0;
			}else{ 
				return 0;
			}
		}
	}

	
	function fQuery($statement,$if_error=false){
		if($this->debug==true) $if_error=true;
		try{
			if($res = @mysql_query($statement,$this->linkid)){
				$this->result = $res;
				return $this->result;
			}else{
				throw new Exception(mysql_errno($this->linkid));
			}
		}catch(Exception $e){
			if($if_error){
				echo $this->err($e->getMessage(),mysql_error($this->linkid),$statement);
				$this->result = 0;
				return 0;
			}else{ 
				return 0;
			}
		}
	
	}
	
	function getLastId(){
		try{
			if($iid = @mysql_insert_id($this->linkid)){
				$this->lastid = $iid;
				return $this->lastid;
			}else{
				throw new Exception(mysql_errno($this->linkid));
			}
		}catch(Exception $e){
			echo $this->err($e->getMessage(),mysql_error($this->linkid),"");
			$this->lastid = 0;
			return 0;
		}
	}

	function countResult(){
		try{
			if($row = @mysql_fetch_object($this->result)){
				$this->nrows = $row;
				return $this->nrows;
			}else{
				throw new Exception(mysql_errno($this->linkid));
			}
		}catch(Exception $e){
			echo $this->err($e->getMessage(),mysql_error($this->linkid),"");
			$this->nrows = 0;
			return 0;
		}
	}
	
	function countRows(){
		try{
			if($row = @mysql_num_rows($this->result)){
				$this->nrows = $row;
				return $this->nrows;
			}else{
				throw new Exception(mysql_errno($this->linkid));
			}
		}catch(Exception $e){
			echo $this->err($e->getMessage(),mysql_error($this->linkid),"");
			$this->nrows = 0;
			return 0;
		}
	}

	function countFields(){
		try{
			if($row = @mysql_num_fields($this->result)){
				$this->nfield = $row;
				return $this->nfield;
			}else{
				throw new Exception(mysql_errno($this->linkid));
			}
		}catch(Exception $e){
			echo $this->err($e->getMessage(),mysql_error($this->linkid),"");
			$this->nfield = 0;
			return 0;
		}
	}

	function move($offset){
		if(($offset<0) || !is_int($offset)) return false;
		
		try{
			if($bol = @mysql_data_seek($this->result,$offset)){
				$this->lastid = $this->getLastId();
				return $bol;
			}else{
				throw new Exception(mysql_errno($this->linkid));
			}
		}catch(Exception $e){
			echo $this->err($e->getMessage(),mysql_error($this->linkid),"");
			return false;
		}
	}
	
	function step(){
		try{
			if($bol = @mysql_data_seek($this->result,$this->getLastId())){
				$this->lastid = $this->getLastId();
				return $bol;
			}else{
				throw new Exception(mysql_errno($this->linkid));
			}
		}catch(Exception $e){
			echo $this->err($e->getMessage(),mysql_error($this->linkid),"");
			return false;
		}
	
	}

	function saveFetchArray(){
		try{
			if($res = @mysql_fetch_array($this->result)){
				$this->lastid = $this->getLastId();
				$this->nrows = $this->countRows();
				$this->nfield = $this->countFields();
				return $res;
			}else{
				throw new Exception(mysql_errno($this->linkid));
			}
		}catch(Exception $e){
			echo $this->err($e->getMessage(),mysql_error($this->linkid),"");
			return false;
		}
	}
	
	private function err($e,$sql_text,$statement=""){
		
	}
	
	
	//wrapper functions for serv_db() statements
	
	
	public function close_db(){
		return $this->close();
	}
	
	public function connect_db(){
		return $this->connect();
	}
	
	public function query_db($statement,$if_error=false){
		return $this->query($statement, $if_error);
	}
	
	public function fast_query($statement,$if_error=false){
		return $this->fQuery($statement, $if_error);
	}
	
	public function get_last_id(){
		return $this->getLastId();
	}
	
	function count_result(){
		return $this->countResults();
	}
	
	function count_rows(){
		return $this->countRows();
	}
	
	function count_fields(){
		return $this->countFields();
	}
	
	function save_fetch_array(){
		return $this->saveFetchArray();
	}
	
}

?>
