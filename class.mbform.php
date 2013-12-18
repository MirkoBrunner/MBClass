<?php

require_once('class.mbclass.php');



/*
wir brauchen:
- text feld
- nummer feld
- datum feld
- option
- checkbox
- button
- upload feld

- useragent
*/

class MBFormElement{
	public $elemType;
	
	public $elemId;
	public $elemName;
	public $elemClass;
	public $elemStyle;
	public $elemValue;
	
	public $onMouseOver;
	public $onMouseOut;
	
	public $onClick;
	
	public $textSize;
	public $maxLength;
	public $cols;
	public $rows;
	
	function __construct(){}
	
	function __destruct(){}
	
	function setPreference($values){
		
		if(array_key_exists('type', $values)){
			$this->elemType = $values['type'];
		}
		
		if(array_key_exists('id', $values)){
			$this->elemId = $values['id'];
		}
	
		if(array_key_exists('name', $values)){
			$this->elemName = $values['name'];
		}
		
		if(array_key_exists('class', $values)){
			$this->elemClass = $values['class'];
		}
		
		if(array_key_exists('style', $values)){
			$this->elemStyle = $values['style'];
		}
		
		if(array_key_exists('size', $values)){
			$this->textSize = $value['size'];
		}
		
		if(array_key_exists('maxlength', $values)){
			$this->maxLength = $values['maxlength'];
		}
		
		if(array_key_exists('rows', $values)){
			$this->rows = $values['rows'];
		}
		
		if(array_key_exists('cols', $values)){
			$this->cols = $value['cols'];
		}
	}
	
	
	function buildElement(){
		
		if($this->elemType==NULL) return;
		
		$elem = "";
		
		$elem.= '<input type="'.$this->elemType.'" ';
		if($this->elemId!=NULL) 	$elem.= 'id="'.$this->elemId.'" ';
		if($this->elemName!=NULL) 	$elem.= 'name="'.$this->elemName.'"';
		if($this->elemClass!=NULL) 	$elem.= 'class="'.$this->elemClass.'" ';
		if($this->elemStyle!=NULL) 	$elem.= 'style="'.$this->elemStyle.'" ';
		
		if($this->elemType=='text'){
			if($this->textSize!=NULL) 	$elem.= 'size="'.$this->textSize.'" ';
			if($this->maxLength!=NULL) 	$elem.= 'maxlength="'.$this->maxLength.'" ';
		}
		
		if($this->elemType=='textarea'){
			if($this->cols!=NULL) $elem.= 'cols="'.$this->cols.'" ';
			if($this->rows!=NULL) $elem.= 'rows="'.$this->rows.'" ';
		}
		
		if($this->elemValue!=NULL){
			
			if($this->elemType=="select"){
				
				$elem.= '>';
				
				$keys = array_keys($this->elemValue);
				
				for($i=0;$i<count($keys);$i++){
					$elem.= '<option value="'.$keys[$i].'">';
					$elem.= $this->elemValue[$keys[$i]].'</option>';
				}
				
								
				$elem.= '</select>';				
				
			}else{
				
				$elem.= 'value="'.$this->elemValue.'" ';
				$elem.= '>';
			}
		}else{
			$elem.= 'value="'.$this->elemValue.'" ';
			$elem.= '>';
		}
		
		
		
		return $elem;
	}
	
	//class zurücksetzen (new) muss nicht genutzt werden
	function resetElement(){
		$refclass = new ReflectionClass($this);
		
		foreach ($refclass->getProperties() as $property){
			$name = $property->name;
			if ($property->class == $refclass->name){
				$this->$name = NULL;
			}
	  	}
	}
}

class MBForm extends MBClass{
	private $html;
	
	public $formName;
	public $formId;
	public $className;
	public $style;
	public $method;
	public $encrypt;
	public $action;
	
	
	function __construct(){
		parent::__construct();
		
	}
	
	function __destruct(){
		parent::__destruct();
		
	}
	
	//ausgabe content.
	//wahlweise ausgabe als in variable (String) oder direkt via echo(default)
	public function printContent($printSelf=true){
		$cont = "";
		
		foreach($this->html as &$row){
			if($printSelf==true){
				echo $row;
			}else{
				$cont.=$row;
			}
		}
		return $cont;
	}
	
	
	public function setFormElement($args, $cache=true){
		
		if($cache==true){
			if(is_a($args,"MBFormElement")){
				array_push($this->html,$args->buildElement());
			}else{
				array_push($this->html,$args);
			}
			
		}else{
			if(is_a($args,"MBFormElement")){
				return $args->buildElement();
			}else{
				return $args;
			}	
			
		}
	}
	
	
	public function buildFormHeader($cache=true){
		$form = "";
		$form.= '<form action="'.$this->action.'" ';
		$form.= 'method="'.$this->method.'" ';
		$form.= 'encrypt="'.$this->encrypt.'" ';
		$form.= 'id="'.$this->formId.'" ';
		$form.= 'name="'.$this->formName.'" ';
		$form.= 'class="'.$this->className.'" ';
		$form.= 'style="'.$this->style.'" ';
		$form.= '>';
		
		if($cache==true) $this->html[0] = $form;
		else return $form;
	}
	
	public function buildFormFooter($cache=true){
		$form = "";
		$form = '</form>';
		if($cache==true) array_push($this->html,$form);
		else return $form;
	}
	
	
}
?>
