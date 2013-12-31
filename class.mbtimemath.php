<?php
/*
 
 By Mirko Brunner (2013) for fiveandfriends GmbH
 All rights reserved. 
 
 mirko.brunner@googlemail.com

*/

if(function_exists('date_default_timezone_set')){
	date_default_timezone_set("Europe/Berlin"); 
}else{
	throw new Exception('Can´t set timezone. Function date_default_timezone_set() not found.');
}

require_once("class.mbclass.php");


/*! \class MBTimeMathCoreHelper class.timemath.php
 *  Hilfsklasse für Berechungen mit Zeiten und Daten. Wird an die MBTimeMMath Klasse vererbt.\n
 *	Grundlegende Zeit- und Datums-Funktionen werden mit MBTineMathCoreHelper sicher gewrappt. 
 */
class MBTimeMathCoreHelper extends MBClass{
	public $timestamp;	/*!< int temporär zum Speichern des übergebenen Unixtimestamps */
	public $ut_min;		/*!< int Anzahl der Sekunden pro Minute */
	public $ut_hour;	/*!< int Anzahl der Sekunden pro Stunde */
	public $ut_day;		/*!< int Anzahl der Sekunden pro Tag */
	public $ut_week;	/*!< int Anzahl der Sekunden pro Woche */
	
	public $ut_m_hour;	/*!< double Qoutient aus 1/3600 (Stunde) */
	public $ut_m_min;	/*!< double Qoutient aus 1/60 (Minute) */
	
	/*! \brief Konstruktor für das Object.
	*	Berechnet die Werte aller Variabeln die mit ut_ beginnen.
	*/
	function __construct()
	{
		parent::__construct();
		$this->timestamp = 0;	
		$this->ut_min = 60;
		$this->ut_hour = $this->ut_min * $this->ut_min;
		$this->ut_day = $this->ut_hour * 24;
		$this->ut_week = $this->ut_day * 7;
		
		$this->ut_m_hour = 0.000277;
		$this->ut_m_min = 0.016666;
	}
	
	/*! \brief Destruktor für das Object.
	*/
	function __destruct()
	{
		parent::__destruct();
		$this->timestamp = NULL;
	}
	
	/*! \brief Wrapperfunktion für mktime().
	*	
	*	Anders als in mktime() wird hier jeder Parameter auf Plausibilität geprüft. Wie mktime() gibt _mktime() den errechneten UnixTimestamp aus dem übergebenen Datum. Da hier eine genaue Prüfung der Übergabeparameter erfolgt wird false zurückgegeben, falls einer der Werte ungültig ist.
	*
	*	Für weitere Informationen zu mktime() schau hier: [mktime() bei php.net](http://php.net/manual/de/function.mktime.php)
	* 	
	*  	\param $hour int Stunden
	*  	\param $minutes int Minuten
	*  	\param $secound int Sekunden
	*  	\param $month int Monate - beginnend mit 1
	*  	\param $day int Tage - beginnend mit 1
	*  	\param $year int Jahre
	*  	\param $is_dst int Flag für Sommer- oder Winterzeit. Standard ist -1 für automatisch.
	*	\brief Muss ab PHP 5.1.0 nicht mehr gesetzt werden.
	*	\return int UnixTimestamp oder false wenn Berechnung unmöglich
	*	
	*/
	function _mktime($hour,$minute=0,$secound=0,$month=1,$day=1,$year=1970,$is_dst=-1)
	{
		/*
		if(!$this->testIn24($hour)) return false;
		if(!$this->testIn60($minute)) return false;
		if(!$this->testIn60($secound)) return false;
		if(!$this->testInN($month,31)) return false;
		if(!$this->testIn7($day)) return false;
		if(!$this->testInN($year,9999)) return false;
		*/
		if($this->debug==true){
			return mktime($hour,$minute,$secound,$month,$day,$year,$is_dst);
		}else{
			return @mktime($hour,$minute,$secound,$month,$day,$year,$is_dst);
		}
	}
	
	/*!	\brief Wrapperfunktion für date().
	*
	*	Anders als in date() wird hier jeder Parameter auf Plausibilität geprüft. Wie date() gibt _date() das formatiete Datum aus dem übergebenen Unixtimestamp zurück, Da hier eine genaue Prüfung der Übergabeparameter erfolgt wird false zurückgegeben, falls einer der Werte ungültig ist.
	*
	*	Für weitere Informationen zu date() schau hier: [date() bei php.net](http://php.net/manual/de/function.date.php)
	*
	*  	\param string $format gewünschtes Format des Datums.
	*  	\param int $time UnixTimestamp
	*  	\return string das formatierte Datum als String oder false falls Prüfung fehlschlägt
	*/
	function _date($format,$time)
	{
		if(!is_numeric($time) || ($time<0)) return false;
		
		if($this->debug==true)
			return date($format,$time);
		else
			return @date($format,$time);
	}
	
	/*! \brief Tested ob Wert zwischen 0 und 60 ist.
	*	\param $num int Wert
	*	\return bool true wenn Wert im bereich leigt ansonsten falls.
	*	\see testInN()
	*/
	function testIn60($num)
	{
		return $this->testInN($num,60);
	}
	
	/*! \brief Tested ob Wert zwischen 0 und 24 ist.
	*	\param $num int Wert
	*	\return boolean true wenn Wert im bereich leigt ansonsten falls.
	*	\see testInN()
	*/
	function testIn24()
	{
		return $this->testInN($num,24);
	}
	
	/*! \brief Tested ob Wert zwischen 0 und 7 ist.
	*	\param $num int Wert
	*	\return bool true wenn Wert im bereich leigt ansonsten falls.
	*	\see testInN()
	*/
	function testIn7()
	{
		return $this->testinN($num,7);
	}
	
	/*! \brief Tested ob Wert zwischen 0 und N ist.
	*	\param $num int Wert
	*	\return bool true wenn Wert im bereich leigt ansonsten falls.
	*/
	function testInN($num,$n)
	{
		if(!is_numeric($num)) return false;
		if(!is_numeric($n)) return false;
		if($num<0) return false;
		if($n<0) return false;
		
		if($num<=$n) return true;
		return false;
	}
}



/*! \class MBTimeMath class.timemath.php
 *  Klasse zum berechnen und Umrechen von Zeiten und Daten.\n
 *	Besonders häufig genutzte Funktionen, insbesondere im Hinblick auf das deutsche Datums- und Zeitformat, werden gebündelt bereitgesellt. Dazu sind noch Umrechnuingsfunktionen vorhanden.
 */
class MBTimeMath extends MBTimeMathCoreHelper{
	
	/*! \brief Konstruktor für das Object.
	*	Berechnet die Werte aller Variabeln die mit ut_ beginnen.
	*/
	function __construct()
	{
		parent::__construct();
	}
	
	/*! \brief Destruktor für das Object.
	*/
	function __destruct()
	{
		parent::__destruct();
	}
	
	/*! \brief Konvertiert ein übergebenes Datum (deutsches Format) in ein UnixTimestamp.
	*
	*	Das übergebene Datum muss die deutsche Notation dd.mm.yy (o. yyyy) aufweisen. Sollte dieses format nicht vorliegen wird false zurückgegeben.
	*
	*	\param $date string deutsch formatiertes Datum
	*	\return int UnixTimestamp oder false wenn ungültige Werte vorliegen
	*/
	public function dateToUnixtime($date)
	{
	
		$v = $this->expl(".", $date, 3);
		if($v===false){ 
			$v = $this->expl("-", $date, 3);
			if($v===false) return false;
			$v = array_reverse($v);
				
		}
		if($v===false) return false;
	
		return $this->_mktime(0, 0, 1, $v[1], $v[0], $v[2]);  	
	}
	
	/*! \brief Konvertiert eine übergebenes Uhrzeit (deutsches Format) in ein UnixTimestamp.
	*
	*	Die übergebene Zeit muss die deutsche Notation hh:mm aufweisen. Der UnixTimestamp wird mit dem Datum 01.01.1970 generiert also darauf Achten. Sollte dieses format nicht vorliegen wird false zurückgegeben.
	*
	*	\param $time string deutsch formatierte Uhrzeit
	*	\return int UnixTimestamp oder false wenn ungültige Werte vorliegen
	*/
	public function timeToUnixtime($time)
	{
	
		$v = $this->expl(":", $time, 2);
		if($v===false) return false;
		
		return $this->_mktime($v[0], $v[1], 0, 1, 1, 1970); 
	}
	
	
	/*!	\brief Konvertiert die übergeben Uhrzeit zu Sekunden.
	*
	*	Die übergebene deutsch formatierte Uhrzeit wird in volle Sekunden umgerechnet.
	*	\param $time string deutsch formatierte Uhrzeit
	*	\return	int volle Anzahl der Sekunden der Uhrzeit.
	*/
	public function timeToInteger($time)
	{
	
		if($time=="" || $time==NULL) return false;
			
		$a = $this->expl(":", $time, 2);

		if($a===false) return false;
		
		$t = 0;
		
		$t+=$a[1] * $this->ut_min;
		$t+=$a[0] * $this->ut_hour;
			
		return $t;
		
	}
	
	
	/*!	\brief Gibt den UnixTimestamp des Beginns des Monats $month im Jahr $year zurück
	*
	*	\param $month int Monat
	*	\param	$year int Jahr
	*	\return UnixTimestamp oder false bei ungültige Werte
	*/
	public function getMonthStartUnixtime($month,$year=2013)
	{
	
		return $this->_mktime(0, 0, 0, $month, 1, $year);
	}
	
	/*!	\brief Gibt die Anzahl der Tage im Monat zurück.
	*
	*	Berücksicht werden auch Schaltjahre. Die Angabe des Jahres ist Optional, wenn kein Wert übergeben wird 2013 genutzt.
	*
	*	\param $month int Monat
	*	\param	$year int Jahr
	*	\return UnixTimestamp oder false bei ungültige Werte
	*/
	public function getDayCountInMonth($month,$year=2013)
	{
	
		return ($this->_mktime(0, 0, 0, $month+1, 0, $year) - mktime(0, 0, 0, $month, 1, $year)) / $this->ut_day;
	}
	
	/*!	\brief Gibt den UnixTimestamp des Ende des Monats $month im Jahr $year zurück
	*
	*	\param $month int Monat
	*	\param	$year int (optional) Jahr
	*	\return UnixTimestamp oder false bei ungültige Werte
	*/
	public function getMonthEndUnixtime($month,$year=2013)
	{
	
		return $this->_mktime(0, 0, 0, $month+1, 0, $year);
	}
	
	/*!	\brief Gibt ein deutsch formatiertes Datum aus dem übergebenen UnixTimestamp zurück
	*
	*	\param $tstamp int (optional) UnixTimestamp
	*	\brief Wenn die Methode ohne Parameter aufgerufen wird, gibt die Methode das heutige Datum zurück.
	*	\return string deutsch formatiertes Datum oder false bei ungültige Werte
	*/
	public function getGermanFormatedDate($tstamp=-1)
	{
		
		if(is_numeric($tstamp)){
			if($tstamp==-1) $tstamp = time();
			return $this->_date('d.m.Y', $tstamp);
		}else
			return false;
	}
	
	/*!	\brief Gibt eine deutsch formatierte Uhrzeit aus dem übergebenen UnixTimestamp zurück
	*
	*	\param $tstamp int UnixTimestamp
	*	\param $nullDavor bool wenn true wird eine führende Null vorangestellt
	*	\return string deutsch formatierte Uhrzeit oder false bei ungültige Werte
	*/
	public function getGermanFormatedTime($tstamp,$nullDavor=false)
	{
	
		if(is_numeric($tstamp) && $tstamp>0)
			return ($nullDavor!=true) ? $this->_date('G:i', $tstamp) :  $this->_date('H:i', $tstamp);
		else
			return false;
	}
	
	/*!	\brief Konvertiert ein UnixTimestamp in ein HTML5 konformes Format.
	*
	*	Seit HTML5 gibt es den Form-Input-Type date diesen wird ein englisch formatiertes Datum übergeben.
	*
	*	\param $tstamp int UnixTimestamp
	*	\return string formatietres Datum oder false bei ungültige Werte
	*/
	public function getHTMLFormFormatedDate($tstamp)
	{
		
		if(is_numeric($tstamp))
			return $this->_date('Y-m-d', $tstamp);
		else
			return false;
	}
	
	/*!	\brief Konvertiert die übergebenen vollen Sekunden in ein Zeitformat Stunden:Minuten.
	*
	*	Es findet keine Konvertierung im Sinne von date() statt vielmehr wird als erstes die Anzahl der Stunden berechnet und der Rest bildet dann die Minuten. Der optionale Wert $nullDavor gibt an ob eine führende Null bei Werten kleiner 10 vorrangestellt werden soll. Standard ist false.
	*
	*	\param $ti int Sekunden
	*	\param $nullDavor bool Flag ob führende Null gesetzt werden soll
	*	\return string formatierte Zeit oder false bei ungültige Werte
	*/
	public function getUnixtimeAs_Hi($ti,$nullDavor=false)
	{
		
		if($ti==0) return ($nullDavor==false) ? "0:00" : "00:00";
		
		$h = $this->superRoundTime($ti / $this->ut_hour);
		$m = floor(($ti - ($h*$this->ut_hour)) / $this->ut_min);
		
		$n = "";
		
		if($m<0) $m = $m*-1;
		
		if($ti<0 && $h==0) $n = "-";
		
		if($h<0){
			if($nullDavor==true){
				$h=$h*-1;
				
				$h = "-".$this->nullDavor($this->superRoundTime($h));
				
				return $h.":".$this->nullDavor($m);
			
			}else{
				return $this->superRoundTime($ti / $this->ut_hour).":".$this->nullDavor($m);
			}
		}else{
			
			$h = ($nullDavor==true) ? $this->nullDavor($h) : $h;
			return $n.$h.":".$this->nullDavor($m);
		}
		
	}
	
	/*!	\brief Gibt den Wert des Wochentages (1..7) errechnet aus dem UnixTimestmp zurück.
	*
	*	\param	$tstamp int (optional) UnixTimestamp
	*	\brief Falls kein Parameter übergeben wird, wird intern time() verwendet.
	*	\return int Wochentag als Zahl
	*/
	public function getDayNumOfWeek($tstamp=-1)
	{
	
		if($tstamp==0) return false;
		if($tstamp==-1)
			return $this->_date('N',time());
		else
			return $this->_date('N',$tstamp);
	}
	
	/*!	\brief Gibt den Tag des Monats (1..31) errechnet aus dem UnixTimestmp zurück.
	*
	*	\param	$tstamp int UnixTimestamp
	*	\return int Monatstag als Zahl
	*/
	public function getDayNumOfMonth($tstamp)
	{
	
		if($tstamp==0) return false;
		return $this->_date('j',$tstamp);
	}
	
	/*!	\brief Gibt den Tag des Jahres (1..365) errechnet aus dem UnixTimestmp zurück.
	*
	*	\param	$tstamp int UnixTimestamp
	*	\return int Jahrestag als Zahl
	*/
	public function getDayNumOfYear($tstamp)
	{
	
		if($tstamp==0) return false;
		return $this->_date('z',$tstamp);
	}
	
	/*!	\brief Gibt die Wochezahl (1..52) errechnet aus dem UnixTimestmp zurück.
	*
	*	\param	$tstamp int UnixTimestamp
	*	\return int Woche als Zahl
	*/
	public function getWeekNumOfYear($tstamp)
	{
	
		if($tstamp==0) return false;
		return $this->_date('W',$tstamp);
	}
	
	/*!	\brief Gibt den Monat im Jahr (1..12) errechnet aus dem UnixTimestmp zurück.
	*
	*	\param	$tstamp int UnixTimestamp
	*	\return int Monat als Zahl
	*/
	public function getMonthNumOfYear($tstamp)
	{
	
		if($tstamp==0) return false;
		return $this->_date('n',$tstamp);
	}
	
	/*!	\brief Gibt Status errechnet aus dem UnixTimestamp des Wochentages zurück.
	*
	*	Wenn es sich um den Anfanf der Woche handelt (1. Tag also Montag) wird 1 zurückgegeben. Sollte es sich um den letzten Tag Sonntag in der Woche handeln wird 2 für alle dazwischen liegenden Tage 0 zurückgegeben.\n Diese Funktion ist interessant für kalendarische Funktionen.
	*
	*	\param	$time int UnixTimestamp
	*	\return int Status des Wochentages bei ungültige Werte false
	*/
	public function getWeekStartEndState($time)
	{
	
		if($time!=0){	
			$dn = $this->getDayNumOfWeek($time);
			
			if($dn==1){
				return 1;
				
			}else if($dn==7){
				return 2;
				
			}else{
				return 0;
				
			}
		}
		return false;
	}
	
	/*!	\brief Gibt Status errechnet aus dem UnixTimestamp des Monatstages zurück.
	*
	*	Wenn es sich um den Anfanf des Monats handelt (1. Tag) wird 1 zurückgegeben. Sollte es sich um den letzten Tag im Monat handeln wird 2 für alle dazwischen liegenden Tage 0 zurückgegeben. Schaltjagre werden berücksichtigt.\n Diese Funktion ist interessant für kalendarische Funktionen.
	*
	*	\param	$time int UnixTimestamp
	*	\return int Status des Monatstages bei ungültige Werte false
	*	\see getWeekStartEndState()
	*/
	public function getMonthStartEndState($time)
	{
	
		if($time!=0){
			$mdc = $this->getDayCountInMonth($this->getMonthNumOfYear($time),date('Y',$time));
			$md = $this->getDayNumOfMonth($time);
			$mdc = $this->superRoundTime($mdc);


			if($md==1){
				return 1;
				
			}else if($md==($mdc+1)){
				return 2;
				
			}else{
				return 0;
				
			}
		}
		return false;
	}
	
	/*!	\brief Gibt Status errechnet aus dem UnixTimestamp des Jahrestages zurück.
	*
	*	Wenn es sich um den Anfanf des Jahres handelt (1. Tag) wird 1 zurückgegeben. Sollte es sich um den letzten Tag im Jahr handeln wird 2 für alle dazwischen liegenden Tage 0 zurückgegeben. Schaltjagre werden berücksichtigt.\n Diese Funktion ist interessant für kalendarische Funktionen.
	*
	*	\param	$time int UnixTimestamp
	*	\return int Status des Jahrestage bei ungültige Werte false
	*	\see getWeekStartEndState()
	*/
	public function getYearStartEndState($time)
	{
		
		$dayCount = $this->_date('z',$time);
		
		if($dayCount==0){
			return 1;
			
		}else if($dayCount==365){
			return 2;
			
		}else{
			return 0;
			
		}
		
	}
	
	/*!	\brief Gibt die auf eine Stunde gerundete deutsche Formatierte Zeit zurück
	*
	*	Es wird nicht nahc der klassichen Rundungsregel gearbeitet sondern die Minuten abgetrennt und nur noch die Stunden zurückgegeben.
	*
	*	\param	$e string fomratierte Zeit
	*	\return int Stunde bei ungültige Werte false
	*/
	private function superRoundTime($e)
	{
	
		$e = $e."";
		$d = $this->expl(".",$e);
		
		return ($d!==false) ? (int)$d[0] : false; 
		
	}
	
	/*!	\brief Rundet die Minuten auf mindestens den übergebenen Wert falls kleiner. 
	*
	*	Falls die Minuten in $time unter dem Wert in $min liegt wird der Wert in $min gesetzt.
	*
	*	\param $time int UnixTimestamp
	*	\param $min int Mindestwert 
	*	\return int gerundete Minuten
	*/
	public function minRoundTime($time,$min)
	{
		return $this->clipMin($min,$time,$min);
	}
	
	
	
	/*!	\brief Rundet die übergebene Zeit $time auf angebebene Sekunden $rN_sec.
	*
	*	Diese Funktion ist hilfreich wenn zum Beispiel die Zeit im 15 Minutentakt ausgegeben werden soll.
	*
	*	\param $time int UnixTimestamp
	*	\param $rN_sec int Numerator in Sekunden 
	*	\return int gerundete Zeit als UnixTimestamp
	*/
	public function getRoundedTime($time,$rN_sec)
	{
		if(!is_numeric($time)) return false;
		if(!is_numeric($rN_sec)) return false;
		
		return (round(time()/$rN_sec)*$rN_sec);	
	}
	
	
	/*!	\brief Gibt den gekürzten deutschen Wochentag aus dem übergebenen UnixTimestamp zurück. 
	*
	*	Falls $short auf false (standard ist true) gesetzt ist, wird der Wochentag voll ausgeschrieben.
	*
	*	\param $time int UnixTimestamp
	*	\param $short bool kurze Wochentage 
	*	\return string Name des Wochentages
	*/
	public function getGermanDayName($time,$short=true)
	{
	
		$z = $this->_date('D',$ti);
		
		if($z=="Mon"){
			if($short) return "Mo";
			else return "Montag";	
			
		}else if($z=="Tue"){
			if($short) return "Di";
			else return "Dienstag";
			
		}else if($z=="Wed"){
			if($short) return "Mi";
			else return "Mittwoch";
			
		}else if($z=="Thu"){
			if($short) return "Do";
			else return "Donerstag";
			
		}else if($z=="Fri"){
			if($short) return "Fr";
			else return "Freitag";
			
		}else if($z=="Sat"){
			if($short) return "Sa";
			else return "Sonnabend";
			
		}else if($z=="Sun"){
			if($short) return "So";
			else return "Sonntag";
			
		}
		return false;
	}
	
	/*!	\brief Gibt den gekürzten deutschen Monatsname aus dem übergebenen UnixTimestamp zurück. 
	*
	*	Falls $short auf false (standard ist true) gesetzt ist, wird der Monatsname voll ausgeschrieben.
	*
	*	\param $ti int UnixTimestamp
	*	\param $short bool kurze Monatsnamen 
	*	\return string Name des Monats
	*/
	public function getGermanMonthName($ti,$short=true)
	{
	
		$z = $this->_date('M',$ti);

		if($z=="Jan"){
			if($short) return "Jan";
			else return "Januar";	
			
		}else if($z=="Feb"){
			if($short) return "Feb";
			else return "Februar";
			
		}else if($z=="Mar"){
			if($short) return "Mar";
			else return "März";
			
		}else if($z=="Apr"){
			if($short) return "Apr";
			else return "April";
			
		}else if($z=="May"){
			if($short) return "Mai";
			else return "Mai";
			
		}else if($z=="Jun"){
			if($short) return "Jun";
			else return "Juni";
			
		}else if($z=="Jul"){
			if($short) return "Jul";
			else return "Juli";
			
		}else if($z=="Aug"){
			if($short) return "Aug";
			else return "August";
			
		}else if($z=="Sep"){
			if($short) return "Sep";
			else return "September";
			
		}else if($z=="Oct"){
			if($short) return "Okt";
			else return "Oktober";
			
		}else if($z=="Nov"){
			if($short) return "Nov";
			else return "November";
			
		}else if($z=="Dec"){
			if($short) return "Dez";
			else return "Dezember";
			
		}
		
		return false;
	}
	
	
	/*!	\brief Erzeugt ein Array mit den deustchen gesetzlichen Feitertage (ganzes Bundesgebiet) zum Jahr zurück. 
	*
	*	Aus dem Jahr im üebrgebenen UNnixTimestamp wird ein Array nach folgeneer Regel erstellt:\nDie Schlüssel im Array ist der UnixTimestamp des Feiertages bei 00:00:01 Uhr. Der Feldwert ist der Name des Feiertages.
	*
	*	\param $time int UnixTimestamp
	*	\return array mit den Feiertagen
	*/
	public function getArrayWithHolidays($time)
	{
		$holi = array();
		$year = $this->_date("Y",$time);
	
		$key = $this->_mktime(0, 0, 1, 1, 1, $year);
		$holi[$key] = 'Neujahr';
		
		$key = $this->_mktime(0, 0, 1, 12, 25, $year);
		$holi[$key] = '1.Weihnachtsfeiertag';
		
		$key = $this->_mktime(0, 0, 1, 12, 26, $year);
		$holi[$key] = '2.Weihnachtsfeiertag';
		
		$key = $this->_mktime(0, 0, 1, 10, 3, $year);
		$holi[$key] = 'Tag der Deutschen Einheit';
		
		$key = $this->_mktime(0, 0, 1, 5, 1, $year);
		$holi[$key] = 'Maifeiertag';
		
		$key = easter_date($year);
		$holi[$key] = 'Ostersonntag';
		
		$key = $this->_mktime(0, 0, 1, $this->_date("n", easter_date($year)), $this->_date("j", easter_date($year)) + 1, $this->_date("Y", easter_date($year)));
		$holi[$key] = 'Ostermontag';

		$key = $this->_mktime(0, 0, 1, $this->_date("n", easter_date($year)), $this->_date("j", easter_date($year)) - 2, $this->_date("Y", easter_date($year)));
		$holi[$key] = 'Karfreitag';
		
		$key = $this->_mktime(0, 0, 1, $this->_date("n", easter_date($year)), $this->_date("j", easter_date($year)) + 39, $this->_date("Y", easter_date($year)));
		$holi[$key] = 'Himmelfahrt';
		
		$key = $this->_mktime(0, 0, 1, $this->_date("n", easter_date($year)), $this->_date("j", easter_date($year)) + 50, $this->_date("Y", easter_date($year)));
		$holi[$key] = 'Pfingstenmontag';
		
		$key = $this->_mktime(0, 0, 1, $this->_date("n", easter_date($year)), $this->_date("j", easter_date($year)) + 49, $this->_date("Y", easter_date($year)));
		$holi[$key] = 'Pfingstsonntag';
		

		return $holi;
	}
	
	/*!	\brief Konvertiert eine zweistellige Jahreszahl in eine Vierstellige. Es wird vom Jahr 2000 ausgegangen.
	*
	*	\param $y int Jahr
	*	\return int Jahr vierstellig
	*/
	public function convert2NumYearto4NumYear($y)
	{
		if(strlen($y)>2) return $y;
		
		return 2000+$y;
		
	}
	
	
	
	public function isLeapYear($time){
		$jahr = date("Y", $time);
		
		if((jahr%100 != 0 && jahr%4 == 0) || jahr%400 == 0) {
			return true;
		}
		return false;
	}
}

?>
