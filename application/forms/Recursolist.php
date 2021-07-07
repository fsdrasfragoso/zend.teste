<?php

class Application_Form_Recursolist extends Zend_Form
{

    public function init()
    {
       $this->setAction('');
	   $this->setMethod('POST');
	   $this->addElement('text', 'ds_recursos', array('label' => 'Recursos') ); 
	   $this->addElement('submit', 'submit', array('label' => 'Enviar') );
    }


}

