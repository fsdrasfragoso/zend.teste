<?php
class TipoUsuarioModel extends Zend_Db_Table_Abstract 
{
	protected $_name = 'sa_tipo_usuario';
	
	public function getTipoUsuario()
	{
		$data = array("" => "Selecione");
		
		$result = $this->fetchAll()->toArray();
		foreach($result as $row){
			if($row['id_situacao_cadastro']==1)
			$data[$row[id_tipo_usuario]] = $row['ds_tipo_usuario'];
		}
		return $data;
	}
}

?>

