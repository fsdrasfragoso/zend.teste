<?php
/**
* Criada por diogo em Feb 1, 2013
*
*/

class App_Cart
{
	/**
	 * 
	 * @param Row_Usuario $us
	 */
	public function __construct(Row_Usuario $us) 
	{
		$this->_us = $us;
		$this->loadUserCart();
	}//constructor

	const SESSION_ID = '__ATIVO_CART__';
	
	/**
	 *
	 * @var App_Cart
	 */
	private static $_inst;
	
	/**
	 *
	 * @var Row_Usuario
	 */
	private $_us;
	
	/**
	 *
	 * @var Row_Carrinho
	 */
	private $_ca;
	
	// STATIC
	
	/**
	 * 
	 * @param Row_Usuario $us
	 * @return App_Cart
	 */
	public static function setCart(Row_Usuario $us) 
	{
		if(is_null(self::$_inst))
		{
			self::$_inst = new App_Cart($us);
		}//if !inst
		
		return self::$_inst;		
	}//setCart
	
	/**
	 *
	 * @return App_Cart
	 */
	public static function getCart()
	{
		if(is_null(self::$_inst))throw new Zend_Exception('O carrinho não foi carregado', 9901);
		return self::$_inst;
	}//getCart
	
	
	protected function loadUserCart() 
	{
		$tb_ca = new CarrinhoModel();
		$this->_ca = $tb_ca->getUserCarrinhoAtivo($this->getIDUsuario());
		if(is_null($this->_ca))
		{
			$this->_ca = $tb_ca->createRow(array('id_usuario'=>$this->getIDUsuario()));
			$this->_ca->save();
		}//if !load
	}//loadUserCart	
	
	// TOTAIS E ETC
	
	/**
	 * 
	 * @param boolean $str
	 * @return float|string
	 */
	public function getTotal($str = false) 
	{
		return $this->getCarrinho()->getTotal($str);
	}//getTotal

	/**
	 * 
	 * @param boolean $str
	 * @return float|string
	 */
	public function getSubTotal($str = false)
	{
		return $this->getCarrinho()->getSubTotal($str);	
	}//getTotal	
	
	/**
	 * 
	 * @param boolean $str
	 * @return float|string
	 */
	public function getTotalDesconto($str = false) 
	{
		return $this->getCarrinho()->getDesconto($str);
	}//getTotalDesconto
	
	/**
	 * 
	 * @param boolean $str
	 * @return float|string
	 */	
	public function getTotalDelivery($str = false) 
	{
		return $this->getCarrinho()->getTaxaDelivery($str);
	}//getDeliveryTotal
	
	/**
	 * 
	 * @param boolean $str
	 * @return float|string
	 */
	public function getTotalTaxa($str = false) 
	{
		return $this->getCarrinho()->getTotalTaxa($str);
	}//getTotalTaxa
	
	/**
	 * 
	 * @return int
	 */
	public function getQtdEventos() 
	{
		return 1 + count($this->getCarrinho()->getEventoAmigos());
	}//getQtdEventos
	
	
	// USER
	
	public function getUser() 
	{
		return $this->_us;
	}//getUser
	
	/**
	 *
	 * @return int
	 */
	public function getIDUsuario()
	{
		return $this->getUser()->id_usuario;
	}//getIDUsuario

	/**
	 * 
	 * @param array $params
	 */
	public function saveUserFerramentas(array $params) 
	{		
		$this->getUser()->refresh();
		
		if($params['sms'] == 1 && !empty($params['cel']))
		{
			$this->getUser()->ds_info_sms = '1';
			$this->getUser()->nr_celular = $params['cel'];
		}//if sms
		if($params['facebook'] == 1)$this->getUser()->ds_info_facebook = '1';		
		
		$this->getUser()->save();
	}//saveUserFerramentas
	
	// DATA
	
	/**
	 * 
	 * @return Row_Carrinho
	 */
	public function getCarrinho() 
	{
		return $this->_ca;
	}//getCarrinho
	
	/**
	 * 
	 * @return Row_Evento
	 */
	public function getEvento() 
	{
		if($this->getCarrinho()->getItemEvento())return $this->getCarrinho()->getItemEvento()->getEvento();
			else return null;
	}//getEvento
	
	/**
	 *
	 * @return Row_CarrinhoEvento
	 */
	public function getEventoItem()
	{
		return $this->getCarrinho()->getItemEvento();
	}//getEventoItem

	/**
	 *
	 * @return boolean
	 */
	public function hasFotos()
	{
		return (count($this->getCarrinho()->getFotoItens()) > 0);
	}//hasFotos
	
	// eventos
	
	/**
	 * 
	 * @param int $id
	 */
	public function addEvento($id) 
	{
		if($this->getCarrinho()->getItemEvento() != null)
		{
			$this->getCarrinho()->delete();
			$this->loadUserCart();
		}//if evento
		$this->getCarrinho()->addItemEvento($id, $this->getIDUsuario());
	}//addEvento

	/**
	 *
	 * @param int $id
	 */
	public function addFotos($id, $id_foto_filtro, array $tamanhos)
	{
		try
		{
			$this->getCarrinho()->addFotos($id, $id_foto_filtro, $tamanhos);
			return true;
		}//try add
		catch(Zend_Exception $e)
		{
			return $e->getMessage();
		}//oops
	}//addFotos

	
	/**
	 * 
	 * @param array $ids
	 */
	public function massAddEventoAmigo(array $ids)
	{
		foreach($ids as $id)
		{
			$this->addEventoAmigo($id);
		}//foreach ids as id
	}//massAddEventoAmigo
	
	/**
	 * 
	 * @param int $id_usuario
	 * @return Row_CarrinhoEvento
	 */
	public function addEventoAmigo($id_usuario) 
	{
		return $this->getCarrinho()->getItemEvento()->cloneParaAmigo($id_usuario);
	}//addEventoAmigo
	
	/**
	 * 
	 * @param array $data
	 * @return boolean|string
	 */
	public function insertEventoAmigo(array $data) 
	{
		try
		{
			$data['dt_nascimento'] = "{$data['dt_nascimento_y']}-{$data['dt_nascimento_m']}-{$data['dt_nascimento_d']}";
			$us = $this->getUser()->addAmigo($data);
			$this->addEventoAmigo($us->id_usuario);
			return true;			
		}//try add
		catch(Zend_Exception $e)
		{
			return $e->getMessage();
		}//oops
	}//insertEventoAmigo
	
	/**
	 * 
	 * @param int $id_usuario
	 * @param int $id_modalidade
	 */
	public function setEventoAmigoModalidade($id_usuario, $id_modalidade) 
	{
		try
		{
			$this->getCarrinho()->getEventoAmigo($id_usuario)->setModalidade($id_modalidade);			
			return true;
		}//try add
		catch(Zend_Exception $e)
		{
			return $e->getMessage();
		}//oops
	}//setEventoAmigoModalidade
	
	/**
	 *
	 * @param int $id
	 */	
	public function removeEventoAmigo($id_usuario) 
	{
		$this->getCarrinho()->delItemEvento($id_usuario);
	}//removeEventoAmigo
	
	/**
	 *
	 * @param int $id
	 */
	public function removeEventoAny($id_usuario)
	{
		if($this->getEventoItem()->id_usuario == $id_usuario)
		{
			$this->unsetModalidade();
			return 1;
		}//if main
		else
		{
			$this->getCarrinho()->delItemEvento($id_usuario);
			return 0;
		}//else amigo
	}//removeEventoAny	
	
	public function setModalidade($id_categoria, $id_modalidade) 
	{		
		try
		{
			$this->getCarrinho()->getItemEvento()->setModalidade($id_modalidade, $id_categoria);
			foreach($this->getCarrinho()->getEventoAmigos() as $ce)
			{
				/* @var $ce Row_CarrinhoEvento */
				$ce->setCategoria($id_categoria);
			}//foreach amigos as ce
			
			$this->getCarrinho()->resetDelivery();
			
			return true;
		}//try modalidade set
		catch(Zend_Exception $e)
		{
			return $e->getMessage();
		}//oops
	}//setModalidade

	public function deleteEvento()
	{
		$this->getCarrinho()->delEvento();
	}//deleteEvento

	public function unsetModalidade()
	{		
		$this->getCarrinho()->getItemEvento()->unsetModalidade();
	}//unsetModalidade	
	
	public function clear()
	{
		self::$_inst = null;
	}//clear
	
	public function finalizar() 
	{
		$this->getCarrinho()->finalizar();
	}//finalizar
	
	// perguntaS	
	
	/**
	 * 
	 * @param array $data
	 * @return boolean|string
	 */
	public function setEventosCamiseta(array $data) 
	{
		$qtds = array();
		foreach($data as $id_usuario=>$item)
		{
			if(!isset($qtds[$item['id_tamanho_camiseta']]))$qtds[$item['id_tamanho_camiseta']] = 1;
				else $qtds[$item['id_tamanho_camiseta']]++;
		}//foreach $data as id_usuario=>$item

		$tb = new EventoCamisetaModel();
		foreach($qtds as $id_tamanho_camiseta=>$qtd)
		{
			/* @var $ec Row_EventoCamiseta */
			$ec = $tb->getByTamanhoEvento($this->getEventoItem()->id_evento, $id_tamanho_camiseta);

			if($ec->nr_quantidade - $qtd < 0)return "Quantidade insuficiente em estoque para tamanho {$ec->getTamanhoCamiseta()->ds_tamanho}";
		}//foreach($qtds as $id_tamanho_camiseta=>$qtd)

		foreach($data as $id_usuario=>$item)
		{
			$this->setEventoItemCamiseta($id_usuario, $item['id_tamanho_camiseta']);
		}//foreach $data as id_usuario=>$item

		return true;
	}//setEventosCamiseta
	
	/**
	 * 
	 * @param int $id_usuario
	 * @param int $id_tamanho_camiseta
	 * @return boolean|string
	 */	
	public function setEventoItemCamiseta($id_usuario, $id_tamanho_camiseta) 
	{
		try
		{
			if($this->getEventoItem()->id_usuario == $id_usuario)$this->getEventoItem()->setTamanhoCamiseta($id_tamanho_camiseta);
				else $this->getCarrinho()->getEventoAmigo($id_usuario)->setTamanhoCamiseta($id_tamanho_camiseta);
			
			return true;
		}//try modalidade set
		catch(Zend_Exception $e)
		{
			return $e->getMessage();
		}//oops		
	}//setEventoItemCamiseta
		
	/**
	 * 
	 * @param int $id_produto
	 * @param array|null $params
	 * @return boolean
	 */	
	public function addProduto($id_produto, $params = null)
	{
		try
		{
			$this->getCarrinho()->addProduto($id_produto, $params);				
			return true;
		}//try modalidade set
		catch(Zend_Exception $e)
		{
			//echo $e->getTraceAsString();
			return $e->getMessage();
		}//oops		
	}//addProduto
	
	/**
	 * 
	 * @param int $id_produto
	 * @param int $qtd
	 * @return boolean|string
	 */	
	public function setProdutoQtd($id_produto, $qtd) 
	{
		try
		{
			$this->getCarrinho()->setProdutoQtd($id_produto, $qtd);
			return true;
		}//try modalidade set
		catch(Zend_Exception $e)
		{
			//echo $e->getTraceAsString();
			return $e->getMessage();
		}//oops
	}//setProdutoQtd
	
	/**
	 * 
	 * @param int $id_carrinho_produto
	 * @return boolean|string
	 */	
	public function removeProduto($id_carrinho_produto) 
	{
		try
		{
			$this->getCarrinho()->removeProdutoItem($id_carrinho_produto);
			return true;
		}//try modalidade set
		catch(Zend_Exception $e)
		{
			return $e->getMessage();
		}//oops		
	}//removeProduto

	/**
	 *
	 * @param int $id_carrinho_foto
	 * @return boolean|string
	 */
	public function removeFoto($id_carrinho_foto)
	{
		try
		{
			$this->getCarrinho()->removeFotoItem($id_carrinho_foto);
			return true;
		}//try modalidade set
		catch(Zend_Exception $e)
		{
			return $e->getMessage();
		}//oops
	}//removeFoto

	
	// perguntas
	
	public function savePerguntas(array $qst) 
	{
		$this->getCarrinho()->clearQuestionario();
		foreach($qst as $id_questionario=>$item)
		{
			if(!empty($item['id_questionario_item']))$this->getCarrinho()->saveQuestionario($id_questionario, $item['id_questionario_item']);
		}//foreach $data as value
	}//savePerguntas
	
	// regulamento
	
	public function addRegulamento() 
	{		
		if($this->isRegulamentoAceito())return;		
		$this->getCarrinho()->setRegulamentoAceito();
	}//addRegulamento
	
	public function isRegulamentoAceito() 
	{
		return ($this->getCarrinho()->getRegulamentoAceito() != null);
	}//isRegulamentoAceito
	
	// cupão
	
	/**
	 * 
	 * @param string $cod
	 * @return boolean|string
	 */
	public function setCupom($cod) 
	{
		try
		{
			$this->getCarrinho()->setCupom($cod);
			return true;
		}//try modalidade set
		catch(Zend_Exception $e)
		{
			return $e->getMessage();
		}//oops		
	}//setCupom
	
	// endereço
	
	/**
	 *
	 * @param array $data
	 * @return boolean|string
	 */
	public function setCarrinhoEndereco(array $data)
	{
            die("cinthia");	
		try
		{
			if(!$data['id_carrinho_endereco'])$this->getCarrinho()->addEndereco($data);
				else $this->getCarrinho()->changeEndereco($data['id_carrinho_endereco'], $data);
			return true;
		}//try add
		catch(Zend_Exception $e)
		{
			return $e->getMessage();
		}//oops
	}//addCarrinhoEndereco
	
	public function setHasDelivery($flag) 
	{
		try
		{
			$this->getCarrinho()->setDelivery($flag);
			return true;
		}//try add
		catch(Zend_Exception $e)
		{
			return $e->getMessage();
		}//oops
	}//setHasDelivery
	
	/**
	 * 
	 * @param array $params
	 * @return Row_PedidoPagamento
	 */
	/*public function createPedido(array $params) 
	{
		try
		{
			$tb = new PedidoModel();
			
			@var $pe Row_Pedido
			$pe = $tb->createRow();
			$pe->saveFromCarrinho($this->getCarrinho());
			
			$pp = $pe->addPagamento($params['pag']);
			$pp->sendPagamento($params);
			
			return $pp;
		}//try add
		catch(Zend_Exception $e)
		{
			if(!is_null($pe))$pe->delete();

			//echo $e->getTraceAsString();

			return $e->getMessage();
		}//oops		
	}*/
	public function createPedido(array $params) {
		try {
			if($params['ped']){    

				$tb = new PedidoModel();
				/* @var $pe Row_Pedido */
				$pe = $tb->getPedido($params['ped']);
				
				$pp = $pe->addPagamento($params['pag'], $params['parc']);
				$pp->sendPagamento($params);
			   
			}else{
				$tb = new PedidoModel();
				/* @var $pe Row_Pedido */
				
				$pe = $tb->createRow();
				$pe->saveFromCarrinho($this->getCarrinho());
				
				$pp = $pe->addPagamento($params['pag'], $params['parc']);
				$pp->sendPagamento($params);
			}
			return $pp;
		}//try add
		catch(Zend_Exception $e) {
			if(!is_null($pe))$pe->delete();
			//echo $e->getTraceAsString();
			return $e->getMessage();
		}
	}
	
}//App_Cart