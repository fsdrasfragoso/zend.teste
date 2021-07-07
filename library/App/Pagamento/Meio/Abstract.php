<?php
/**
* Criada por diogo em Mar 1, 2013
*Abs
*/

abstract class App_Pagamento_Meio_Abstract 
{
	/**
	 * 
	 * @param Row_PedidoPagamento $pag
	 * @param array $params
	 * @return App_Pagamento_Resultado
	 */
	final public function send(Row_PedidoPagamento $pag, array $params = array())
	{
		$res = new App_Pagamento_Resultado();
		$this->_send($pag, $params, $res);
		
		return $res;
	}//send
	
	/**
	 * 
	 * @param Row_PedidoPagamento $pag
	 * @param Zend_View $view
	 * @return string
	 */
	final public function render(Row_PedidoPagamento $pag, Zend_View $view)
	{
		return $this->_render($pag, $view);		
	}//render
	
	/**
	 * 
	 * @param Row_PedidoPagamento $pag
	 * @param Zend_View $view
	 * @return string
	 */
	public function _render(Row_PedidoPagamento $pag, Zend_View $view) 
	{
		return '###';
	}//_render
	
	/**
	 * 
	 * @param Row_PedidoPagamento $pag
	 * @param array $params
	 * @param App_Pagamento_Resultado $res
	 */
	abstract protected function _send(Row_PedidoPagamento $pag, array $params, App_Pagamento_Resultado $res);	
	
}//App_Pagamento_Abstract