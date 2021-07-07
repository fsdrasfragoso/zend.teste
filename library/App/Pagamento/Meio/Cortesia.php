<?php
/**
* Criada por diogo em Mar 1, 2013
*
*/

class App_Pagamento_Meio_Cortesia extends App_Pagamento_Meio_Abstract 
{
	private $_bol;
	
	/**
	 * 
	 * @return App_Pagamento_Meio_Boleto
	 */
	public function getBoleto($ds_cod) 
	{
		if($ds_cod == 'BOL_BB')
		{
			$this->_bol = new App_Pagamento_Meio_Boleto_BancoBrasil();
		}
		elseif($ds_cod == 'BOL'){
			$this->_bol = new App_Pagamento_Meio_Boleto_Santander();
		}//if !_bol
		else{
			$this->_bol = new App_Pagamento_Meio_Boleto_Santander();
		}
		return $this->_bol;
	}//getBoleto
	
	/**
	 * (non-PHPdoc)
	 * @see App_Pagamento_Meio_Abstract::_render()
	 */
	public function _render($pag, Zend_View $view)
	{		
		$ds_cod = $pag->getForma()->ds_cod;
		$res = $pag->getUltimoResultado();
		return $this->getBoleto($ds_cod)->render($res->getResposta(), $view);
	}//_render($pag)
	
	/**
	 * (non-PHPdoc)
	 * @see App_Pagamento_Meio_Abstract::_send()
	 */
	protected function _send(Row_PedidoPagamento $pag, array $params, App_Pagamento_Resultado $res)
	{
		$demonstrativo = explode('<br>', nl2br($pag->getPedido()->getDescricao()));
		
		$ds_cod = $pag->getForma()->ds_cod;
		
		$params = $this->getBoleto($ds_cod)->getParams($pag->getPedido()->nr_total, $pag->getPedido()->ds_cod, $pag->getUsuario()->ds_nome, $demonstrativo);
		
		$res->setResposta($params);
		
		$res->setStatusSucesso();
		$res->setResultadoPendente();		
	}//_send($pag, $params, $res)
}//App_Pagamento_Meio_Boleto