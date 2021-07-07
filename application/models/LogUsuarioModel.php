<?php
class LogUsuarioModel extends Zend_Db_Table_Abstract
{
	protected $_name = 'log_usuarios';
    
    public function getAll()
	{
		
		
		$select = $this->select()->from(array('ul'=>$this->_name),'ul.*')
                            ->joinInner(array('u'=>'usuarios'),'ul.id_usuario = u.id',array())
                            ->where('u.`status` = 1'); 

		return $this->fetchAll($select);

    }

    public function getById($id)
	{
		$select = $this->select()->from(array('u'=>$this->_name),'u.*')->where("`id_usuario` = $id"); 

		return $this->fetchAll($select);
	}
        
}