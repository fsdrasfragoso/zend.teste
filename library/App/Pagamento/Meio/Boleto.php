<?php
/**
* Criada por diogo em Mar 1, 2013
*
*/

class App_Pagamento_Meio_Boleto extends App_Pagamento_Meio_Abstract 
{
	private $_bol;
	
	/**
	 * 
	 * @return App_Pagamento_Meio_Boleto_Santander
	 */
	public function getBoleto($fl_bradesco = 0)
	{
		if(!$fl_bradesco){
			 $this->_bol = new App_Pagamento_Meio_Boleto_Santander();
		}//if !_bol
		else{
			//$this->_bol = new App_Pagamento_Meio_Boleto_Bradesco();
			return 'Problemas com sua forma de pagamento'; 
		}
		return $this->_bol;
	}//getBoleto
	
	/**
	 * (non-PHPdoc)
	 * @see App_Pagamento_Meio_Abstract::_render()
	 */
	public function _render($pag, Zend_View $view) 
	{
		$res = $pag->getUltimoResultado();
		return $this->getBoleto()->render($res->getResposta(), $view);				
	}//_render($pag)
	
	/**
	 * (non-PHPdoc)
	 * @see App_Pagamento_Meio_Abstract::_send()
	 */
	protected function _send(Row_PedidoPagamento $pag, array $params, App_Pagamento_Resultado $res)
	{
		$demonstrativo = explode('<br>', nl2br($pag->getPedido()->getDescricao()));	
		$params = $this->getBoleto($pag->fl_bradesco)->getParams($pag->getPedido()->nr_total, $pag->getPedido()->ds_cod, $pag->getUsuario()->ds_nome, $demonstrativo);
		
		$res->setResposta($params);
		
		$res->setStatusSucesso();
		$res->setResultadoPendente();		
	}//_send($pag, $params, $res)
}//App_Pagamento_Meio_Boleto