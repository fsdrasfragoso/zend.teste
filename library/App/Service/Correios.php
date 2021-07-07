<?php
/**
* Criada por diogo em Feb 21, 2013
*
* Baseado em http://www.pinceladasdaweb.com.br/blog/2012/01/31/webservice-consulta-de-cep-diretamente-ao-site-dos-correios/
*
*/

require_once APPLICATION_PATH . '/../library/phpQuery-onefile.php';

class App_Service_Correios 
{
	private $_url = 'http://m.correios.com.br/movel/buscaCepConfirma.do';
	private $_client;
	
	/**
	 * 
	 * @return Zend_Http_Client
	 */
	public function getClient() 
	{
		if(is_null($this->_client))
		{
			$adp = new Zend_Http_Client_Adapter_Curl();
			$adp->setCurlOption(CURLOPT_FOLLOWLOCATION, 1);
			$adp->setCurlOption(CURLOPT_POST, 1);
			$adp->setCurlOption(CURLOPT_RETURNTRANSFER, true);
			
			$cli = new Zend_Http_Client();
			$cli->setAdapter($adp);
			$cli->setUri($this->_url);
			
			$this->_client = $cli;
		}//if !_client
		return $this->_client;
	}//getClient

	/**
	 * 
	 * @param string $cep
	 * @return array
	 */
	public function getAddressByCEP($cep)
	{
		$cep = str_replace('-', '', $cep);
		$params = array('cepEntrada'=>$cep, 'tipoCep'=>'', 'cepTemp'=>'', 'metodo'=>'buscarCep');
		//$this->getClient()->getAdapter()->setCurlOption(CURLOPT_POSTFIELDS, http_build_query($params));
		$this->getClient()->setParameterPost($params);		
		$res = $this->getClient()->request('POST');
		return $this->parseAddress($res->getBody());
	}//getAddressByCEP

	/**
	 * 
	 * @param string $html
	 * @return array
	 */
	private function parseAddress($html) 
	{
		phpQuery::newDocumentHTML($html, $charset = 'utf-8');
		$data = array('ds_endereco'=> trim(pq('.caixacampobranco .resposta:contains("Logradouro: ") + .respostadestaque:eq(0)')->html()),
					  'ds_bairro'=> trim(pq('.caixacampobranco .resposta:contains("Bairro: ") + .respostadestaque:eq(0)')->html()),
					  'cidade/uf'=> trim(pq('.caixacampobranco .resposta:contains("Localidade / UF: ") + .respostadestaque:eq(0)')->html()),
					  'nr_cep'=> trim(pq('.caixacampobranco .resposta:contains("CEP: ") + .respostadestaque:eq(0)')->html()));
		$data['cidade/uf'] = explode('/',$data['cidade/uf']);
		$data['cidade'] = trim($data['cidade/uf'][0]);
		$data['estado'] = trim($data['cidade/uf'][1]);
		unset($data['cidade/uf']);
		
		$tb_ci = new CidadeModel();
		$ci = $tb_ci->getCidadeByNome($data['cidade']);
		
		if(is_null($ci))
		{
			$data['id_estado'] = $tb_ci->getEstadoIDByUF($data['estado']);
		}//if !cidade
		else
		{
			$data['id_cidade'] = $ci->id_cidade;
			$data['id_estado'] = $ci->id_estado;			
		}//else achô!		
		
		return $data;
	}//parseAddress
	
}//App_Service_Correios