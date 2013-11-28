<?php

/*! \class MBClass class.mbclass.php
 *  Basisklasse aller MBKlassen. Stellt grundlegede Debug Funktionen bereit. 
 */
class MBClass{
	public $debug;	/*!< Flag für Debug Modus. Standard ist false. */
	const VERSION = "0.5.1";	/*!< Verssionsnummer der Klasse. */
	
	/*! \brief Konstruktor für das Object.
	*/
	function __construct()
	{
		$this->debug = false;
	}
	
	/*! \brief Destruktor für das Object.
	*/
	function __destruct()
	{
		$this->debug = NULL;
	}
	
	/*! \brief Setzen des Debugmodus mit bool $b.
	*  	\param $d bool
	*/
	public function setDebugMode($d)
	{
		if(!is_bool($d)) return;
		$this->debug = $d;
	}
	
	/*! \brief Gibt den gesetzten Debugmodus zurück.
	*/
	public function getDebugMode()
	{
		return $this->debug;
	}
	
	/*! \brief Erzeugt eine Debugausgabe des übergebenen Objekts.
	*  	\param $v mixed var
	*/
	public function printDebug($v)
	{
		echo "<pre>";
		var_dump($v);
		echo "</pre>";
	}
	
	/*! \brief Kurzform für printDebug.
	*  	\param $v mixed var
	*/
	public function deb($v)
	{
		$this->printDebug($v);
	}
	
	/*! \brief Zerlegt eine Zeichenkette in ein Array, ähnlich wie explode().
	*  	\param $del string
	*	\brief der Demiliter für explode()
	*	\param &$val string
	*	\brief die zu zerlegende Zeichenkette
	*	\param $count int (Optional)
	*	\brief Wenn gesetzt wird das Array auf die Anzahl $count geprüft. Sollte dies fehlschlagen wird false zurückgegeben.
	*	\return array
	*	\brief die zerlegte Zeichenkette
	*/
	public function expl($del,&$val,$count=-1)
	{
		if($val=="" || $val==NULL) return false;
		if($del=="" || $del==NULL || !is_string($del)) return false;
		
		$v = explode($del, &$val);
		
		if(!is_array($v)) return false;
		
		if($count>0){
			if(count($v)!=$count) return false;
			else return $v;
		}
		return $v;
	}
	
	/*! \brief Fügt führende Null(en) vor dem übergebenen numerichen Wert.
	*  	\param $str string
	*	\brief die Zeichenkette
	*	\return string
	*	\brief die geänderte Zeichenkette
	*/
	public function nullDavor($str,$count=2)
	{
		if(strlen($str)==0){
			return "00";
			
		}else if(strlen($str)==1){
			return "0".$str;
			
		}
		return $str;
	}
	
	/*! \brief Klassische Clip-Funktion. Wenn $val kleiner als $min dann wird der Inhalt von $min zurückgegeben.<br /><br />Falls einer der übbergebenen Werte nicht Numerisch ist wird der Wert in $ret (standard [bool] false) zurückgegeben.
	*  	\param $min number
	*	\brief der Mindestwert den $val haben darf
	*  	\param $val number
	*	\brief die zu prüfende Zahl
	*  	\param $ret mixed (optional)
	*	\brief der Wert der zurückgegeben wird falls keine numerischen Werte übergeben wurde
	*	\return number
	*	\brief der "geclipte" Wert oder $ret false $val nicht numerisch ist.
	*/
	public function clipMin($min, $val, $ret=false)
	{
		if(!is_numeric($min)) return $ret;
		if(!is_numeric($val)) return $ret;
		
		if($val<$min) return $min;
		return $val;
	}
	
		/*! \brief Klassische Clip-Funktion. Wenn $val größer als $max dann wird der Inhalt von $max zurückgegeben.<br /><br />Falls einer der übbergebenen Werte nicht Numerisch ist wird der Wert in $ret (standard [bool] false) zurückgegeben.
	*  	\param $max number
	*	\brief der Maximalwert den $val haben darf
	*  	\param $val number
	*	\brief die zu prüfende Zahl
	*  	\param $ret mixed (optional)
	*	\brief der Wert der zurückgegeben wird falls keine numerischen Werte übergeben wurde
	*	\return number
	*	\brief der "geclipte" Wert oder $ret false $val nicht numerisch ist.
	*/
	public function clipMax($max, $val,$ret=false)
	{
		if(!is_numeric($max)) return $ret;
		if(!is_numeric($val)) return $ret;
		
		if($val>$max) return $max;
		return $val;
	}
	
	/*! \brief Klassische Clip-Funktion. Wenn $val größer als $max oder kleiner als $min wird entsprechend der Inhalt von $min oder $max zurückgegeben.<br /><br />Falls einer der übbergebenen Werte nicht Numerisch ist wird der Wert in $ret (standard [bool] false) zurückgegeben.
	*  	\param $min number
	*	\brief der Mindestwert den $val haben darf
	*  	\param $max number
	*	\brief der Maximalwert den $val haben darf
	*  	\param $val number
	*	\brief die zu prüfende Zahl
	*  	\param $ret mixed (optional)
	*	\brief der Wert der zurückgegeben wird falls keine numerischen Werte übergeben wurde
	*	\return number
	*	\brief der "geclipte" Wert oder $ret false $val nicht numerisch ist.
	*/
	public function clipMinMax($min, $max, $val,$ret=false)
	{
		//wir brauchen keine Überprüfung, da in den Funktionen geprüft wird.
		$val = $this->clipMin($min,$val);
		if($val===false) return;
		
		$val = $this->clipMax($min,$val);
		return $val;
	}
	
	
	/*! \brief Filter für Zeichenketten. Entfernt alle Zeichen im String die ein ASCII-Wert kleiner als 32 bzw. größer als 127 sind. Entfernt
	*  	\param $string string
	*	\brief der zu prüfende String.
	*	\return string
	*	\brief der gefillterete String.
	*/
	public function filterString($string)
	{
		$str = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$str = filter_var($str, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		$str = mysql_real_escape_string($string);
		return $str;
	}

	
	/* Validation stuff... */
	public function testInt($int,$min=INT_MIN,$max=INT_MAX)
	{
		if(!is_numeric($int)) return false;
		if($int<$min) return false;
		if($int>$max) return false;
		
		return true;
	}
	
	public function valInt($int, $min=INT_MIN, $max=INT_MAX, $clip=false, $ret=NULL)
	{
		if(!$this->testInt($int, $max, $min))
		{
			if(($ret!=NULL) && (is_numric($ret)) return $ret;
			return false;
		}
		
		if($clip===true) return $this->clipMinMax($int,$min,$max);
		
		return $int;
	}

	public function testString($string, $allowNoteNull=false)
	{
		if(!is_string($string)) return false;
		if(($string=="") && ($allowNotNull===true)) return true;
		if($string<>"") return true;
		return false;
		
	}
	
	public function saveSQLString($string)
	{
		if(!$this->testString($string)) return false;
		if(function_exists('mysql_real_escape_string'))
		{
			return mysql_real_escape_string($string);
		}
		else
		{
			//needed help for preg_replace expression ;)
			return false;
		}
		return false;
	}
}
?>
