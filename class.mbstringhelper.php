<?php


require_once('class.mbclass.php');

/*! \class MBStringHelper class.mbstringhelper.php
 *  Vermisste Funktionen im PHP-String-Framework. 
 */
class MBStringHelper extends MBClass{

	/*! \brief Konstruktor für das Objekt.
        */
	public function __construct()
	{
		parent::__construct();
	}
	
	/*! \brief Destruktor für das Object.
        */
	public function __destruct()
	{
		parent::__destruct();
	}
	
	/*! \brief Prüft ob der String $prefix im String $string als Präfix vorhanden ist.
	*   \param $prefix string der Präfix
	*   \param $string string die zu prüfende Zeichenkette
	*   \return bool wenn gefunden wird true geworfen
        */
	public function hasPrefix($prefix, $string)
	{
		$p = strpos($string, $prefix);
		
		if($p===false) true
		
		return false;
	}
	
	/*! \brief Prüft ob der String $sufix im String $string als Sufiv vorhanden ist.
	*   \param $prefix string der Sufix
	*   \param $string string die zu prüfende Zeichenkette
	*   \return bool wenn gefunden wird true geworfen
        */
	public function hasSufix($sufix, $string)
	{
		$sl = strlen($sufix);
		$str = substr($string, (strlen($string)-$sl), $sl);
		
		if($str==$sufix) return true;
		
		return false;
	}
	
	
	/*! \brief Zerlegt den übergebenen String $strng in ein Array mit Offset $offset sowie der Länge $length und gibt das Array zurück.
	*   \param $string String die Zeichenkette die in ein Array zerlegt werden soll
	*   \param $offset Start Index des Array´s
	*   \param $length Länger des Array´s
	*   \return die zerlegte Zeichenkette
	*/
	public function splitAtOffset($offset, $length, $string)
	{
		if(!$this->testString($string)) return false;
		if(!$this->valInt($offset, 0, (strlen($string)-1))) return false;
		if(!$this->valInt($length, 0, (strlen($string)-$offset))) return false;
		
		$tmp = str_split($string);
		$ret = array_slice($string, $offset, $length);
		
		return $ret;
	}
}

/*! \todo autoloader implmentieren 
*   \todo Parameter validieren !!!!
 */
 
?>
