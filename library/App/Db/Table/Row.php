<?php 
/**
* Criada por diogo em Jan 7, 2013
*
*/

class App_Db_Table_Row extends Zend_Db_Table_Row 
{	
	public function dateStr($field, $format = 'd/m/Y')
    {
    	return date($format, strtotime($this->$field));
    }//dateStr
    
    public function moneyStr($field) 
    {
    	return 'R$ '.number_format($this->$field, 2, ',', '.');
    }//moneyStr

	protected function getAdminBaseURL() 
	{
		return '';
	}//getAdminBaseURL
	
    public function __wakeup()
    {
		$class = $this->getTableClass();
		$this->setTable(new $class);
    }//__wakeup	
	
}//App_Db_Table_Row
