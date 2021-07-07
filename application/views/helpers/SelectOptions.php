<?php
/**
* Criada por diogo em Jan 10, 2013
*
*/

class Zend_View_Helper_SelectOptions extends Zend_View_Helper_Abstract
{
	/**
	 *
	 * @param array|Zend_Db_Table_Rowset $data
	 * @param string $label_field
	 * @param string $value_field
	 *
	 * @return array
	 */
	public function selectOptions($data, $label_field = null, $value_field = null, $title_opt = null)
	{
		if(!is_null($title_opt))$opts = array(''=>$title_opt);
			else $opts = array();
		
		if(count($data) > 0 && !is_object($data[0]))
		{
			foreach($data as $item)
			{
				if(is_array($item))$opts[$item[$value_field]] = $item[$label_field];
					else $opts[$item] = $item;
			}//foreach $data as $item
		}//if array
		else if(count($data) > 0)
		{
			foreach($data as $item)
			{
				$opts[$item->$value_field] = $item->$label_field;
			}//foreach $data as $item
		}//else rowset
		
		return $opts;
	}//selectOptions
}//SelectOptions