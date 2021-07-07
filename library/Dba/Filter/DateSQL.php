<?php
require_once 'Zend/Filter/Interface.php';

class Dba_Filter_DateSQL implements Zend_Filter_Interface
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
        if(!empty($value))
        {
        	$match = array();

        	//arruma datas brasileiras
			if(preg_match('/^\s*(\d\d?)[^\w](\d\d?)[^\w](\d{1,4}\s*$)/', $value, $match))
			{
				$value = "{$match[2]}/{$match[1]}/{$match[3]}";
			}//if match

        	$date_sql = date('Y-m-d', strtotime($value));
        	return $date_sql;
        }//if value
        else return '';
    }//filter
}//Dba_Filter_DateSQL
