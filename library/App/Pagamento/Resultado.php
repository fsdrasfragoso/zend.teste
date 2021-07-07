<?php
/**
* Criada por diogo em Mar 1, 2013
*
*/

class App_Pagamento_Resultado
{
	private $_status;
	private $_msg;	
	private $_res;
	
	private $_resp;
	
	private $_pag_url = '';	
	private $_redir_url = '';
	private $_redir = false;
	
	const RES_CONFIRMADO = 'CONFIRMADO';
	const RES_PENDENTE = 'PENDENTE';
	const RES_RECUSADO = 'RECUSADO';
	
	/**
	 *
	 * @return boolean
	 */
	public function isSucesso()
	{
		return $this->_status;
	}//isSucesso
	
	/**
	 *
	 * @return string
	 */
	public function getResultado()
	{
		return $this->_res;
	}//getResultado	
	
	/**
	 * 
	 * @return multi
	 */
	public function getResposta() 
	{
		return $this->_resp;
	}//getResultado
	
	/**
	 * 
	 * @return string
	 */
	public function getMensagem() 
	{
		return $this->_msg;
	}//getMensagem
	
	/**
	 * 
	 * @return boolean
	 */
	public function hasRedirecionamento() 
	{
		return $this->_redir;
	}//hasRedirecionamento
	
	/**
	 * 
	 * @return string
	 */
	public function getRedirURL() 
	{
		return $this->_redir_url;
	}//getRedirURL
	
	/**
	 * 
	 * @return string
	 */
	public function getPagamentoURL() 
	{
		return $this->_pag_url;
	}//getPagamentoURL
	
	// sets
	
	public function setStatusSucesso()
	{
		$this->_status = true;
	}//setStatusSucesso
	
	public function setStatusErro()
	{
		$this->_status = false;
	}//setStatusErro	
	
	/**
	 * 
	 * @param string $str
	 */
	public function setMensagem($str) 
	{
		$this->_msg = $str;
	}//setMensagem
	
	/**
	 * 
	 * @param multi $resp
	 */
	public function setResposta($resp) 
	{
		$this->_resp = $resp;
	}//setResposta
	
	public function setResultadoPendente()
	{
		$this->_res = self::RES_PENDENTE;
	}//setResultadoPendente
	
	public function setResultadoConfirmado() 
	{
		$this->_res = self::RES_CONFIRMADO;
	}//setResultadoConfirmado
	
	public function setResultadoRecusado()
	{
		$this->_res = self::RES_RECUSADO;
	}//setResultadoRecusado
	
	public function setRedirecionamento() 
	{
		$this->_redir = true;
	}//setRedirecionamento
	
	/**
	 * 
	 * @param string $url
	 */
	public function setRedirURL($url) 
	{
		$this->_redir_url = $url;
	}//setRedirURL

	/**
	 *
	 * @param string $url
	 */	
	public function setPagURL($url) 
	{
		$this->_pag_url = $url;
	}//setPagURL
	
}//self