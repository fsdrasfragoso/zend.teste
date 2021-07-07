<?php
class Dba_Filter_BrMoney implements Zend_Filter_Interface
{
    /**
     *
     * Retorna data em formato SQL
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        if(!empty($value) && strpos($value, ',') !== false)
        {
			$value = str_replace(array('.', ','), array('', '.'), $value);
        	return $value;
        }//if value
        else return $value;
    }//filter

	public function unfilter($value)
	{
		return number_format($value, 2, ',', '.');
	}//unfilter

}//Dba_Filter_BrMoney
