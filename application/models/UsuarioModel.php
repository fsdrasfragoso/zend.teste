<?php
class UsuarioModel extends Zend_Db_Table_Abstract
{
	protected $_name = 'usuarios';

	public function save($data)
	{
		return $this->insert($data);
	}

	public function edit($data, $id)
	{
		return $this->update($data, "id = $id");
	}

	public function getById($id)
	{
		$select = $this->select()->from(array('u'=>$this->_name),'u.*')->where("`id` = $id"); 

		return $this->fetchRow($select);
	}

	

	public function remove($id)
	{
		return $this->delete("id = $id");
	}

	public function getAll()
	{
		/* 
			EU gosto de usar SQL puro, mas como o objetivo do teste é medir o conhecimento 
			nos recursos da Framework, optei pelo metodo eloquent. 
			
			Vantagens do metodo Eloquent:
				* Não precisa refazer o codigo se decidir mudar de Banco de Dados, 
			tornado assim o codigo mais duravel e solido. 
			    * Simples implementação
				* Simplifica a busca de dados
			
			Desvantagens do metodo Eloquent:
				* Tem um desempenho menor do que uma solicitação em SQL puro
				* Não tem todos os recursos do Banco de Dados
				* Ao passo que Limita erros de Desenvolvedores junior, também limita Sênios com
			habilidades de DBA que conseguem extrair mais performace do Banco de Dados. 
		*/
		
		$select = $this->select()->from(array('u'=>$this->_name),'u.*')->where('`status` = 1'); 

		return $this->fetchAll($select);
	}

	
   
}