<?php
class App_Service_DirectLog
{
	private $_cli;

	private function xml2array($xml)
	{
	  $arr = array();
	  foreach ($xml as $element) {
		$tag = $element->getName();
		$e = get_object_vars($element);
		if (!empty($e)) {
		  $arr[$tag] = $element instanceof SimpleXMLElement ? $this->xml2array($element) : $e;
		}
		else {
		  $arr[$tag] = trim($element);
		}
	  }
	  return $arr;
	}

	private function getClient()
	{
		if(is_null($this->_cli))
		{
			$cfg = Zend_Registry::get('config')->directlog;

			$params = array();
			//$params['login'] = $cfg->login;
			//$params['password'] = $cfg->password;
			//$params['uri'] = 'http://wsdirect.directlog.com.br/v2/wsdirectlog.asmx?op=';
			//$params['location'] = 'http://wsdirect.directlog.com.br/v2/wsdirectlog.asmx?op=';
			//print_r($params);

			$this->_cli = new Zend_Soap_Client('http://wsdirect.directlog.com.br/v2/wsdirectlog.asmx?WSDL', $params);
			//$this->_cli = new Zend_Soap_Client(null, $params);
			$this->_cli->setSoapVersion(SOAP_1_1);
		}//if !cli
		return $this->_cli;
	}//getClient

	public function getStatus($pedido)
	{
		try
		{
			$cfg = Zend_Registry::get('config')->directlog;

			$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>
					<directlog>
						<parametros>
							<parametro name=\"pedido\">$pedido</parametro>
						</parametros>
					</directlog>";

			$res = $this->getClient()->ConsultaStatusEntrega(array('login'=>$cfg->login, 'password'=>$cfg->password, 'xml'=>$xml, 'type'=>'DEFAULT'));
			$xml = new SimpleXMLElement($res->xml);
			/*
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="ISO-8859-1" ?>
											<directlog>
												<tracking>
													<protocolo>201202959811</protocolo>
													<remessas>
														<remessa>
															<nrremessa>07640000334934</nrremessa>
															<nrpedido></nrpedido>
															<nrnotafiscal>474941R</nrnotafiscal>
															<historicos>
																<status>
																	<codstatus>15</codstatus>
																	<descricaostatus>Expedição</descricaostatus>
																	<dtstatus>19/01/2012 21:34</dtstatus>
																	<ocorrencias>
																		<ocorrencia>
																			<codocorrencia></codocorrencia>
																			 <descricaoocorrencia></descricaoocorrencia>
																		</ocorrencia>
																	</ocorrencias>
																</status>
																<status>
																	<codstatus>14</codstatus>
																	<descricaostatus>Consolidada</descricaostatus>
																	<dtstatus>19/01/2012 23:55</dtstatus>
																	<ocorrencias>
																		<ocorrencia>
																			<codocorrencia></codocorrencia>
																			 <descricaoocorrencia></descricaoocorrencia>
																		</ocorrencia>
																	</ocorrencias>
																</status>
																<status>
																	<codstatus>04</codstatus>
																	<descricaostatus>Pendente</descricaostatus>
																	<dtstatus>20/01/2012 14:06</dtstatus>
																	<ocorrencias>
																		<ocorrencia>
																			<codocorrencia>009</codocorrencia>
																			 <descricaoocorrencia>PEDIDO CANCELADO</descricaoocorrencia>
																		</ocorrencia>
																	</ocorrencias>
																</status>
															</historicos>
														</remessa>
													</remessas>
												</tracking>
											</directlog>');

			*/
			if($xml->tracking)
			{
				$hist = $xml->tracking->remessas->remessa->historicos;
				return $this->getHistoricos($hist);
			}//if ok
			else
			{
				return utf8_decode($xml->retorno->mensagens->mensagem);
			}//else
		}//try request
		catch(Exception $e)
		{
			echo $this->getClient()->getLastMethod()."\n";
			echo $this->getClient()->getLastRequestHeaders()."\n";
			echo $this->getClient()->getLastRequest()."\n";
			echo $this->getClient()->getLastResponse()."\n";
			echo $e->getMessage()."\n";
			die();
		}//catch excetp
	}//getStatus

	/**
	 *
	 * @param SimpleXMLElement $hist
	 */
	private function getHistoricos($hist)
	{
		$arr = json_decode(json_encode($hist) , 1);
		return $arr['status'];
	}//getHistoricos

}//App_Service_DirectLog
