<?php
/**
* Criada por diogo em Mar 1, 2013
*
* Afiliação: 1006424773
* Chave: 56fa5171d57825381a2cd1d27ccc1cad4e4d75b140d79fa62363d4e87f56aa22
*
*/

class App_Pagamento_Meio_Cielo extends App_Pagamento_Meio_Abstract
{
	private $_LOJA = '';
	private $_LOJA_CHAVE = '';

	private $_PARCELAMENTO_TIPO = '';
	private $_CAPTURA = '';
	private $_AUTORIZA = '';

	private $_RETURN_URL = '/carrinho/passo4/finalizado';

	/**
	 *
	 * @param Row_PedidoPagamento $pag
	 * @param array $params
	 */
	private function saveCartao(Row_PedidoPagamento $pag, array $params)
	{
		$cartao = str_replace(array('-', '.', '_'), '', $params['ds_numero']);
		
		//if ($cartao == '5491670250510141'){
			$tb = new UsuarioCartaoModel();
			$PedidoModel = new PedidoModel();
	
			$key = PedidoModel::CardSecurityKey;
		    $db = Zend_Registry::get('db');
	
			$sql = "SELECT AES_ENCRYPT(?, ?) as ds_numero, AES_ENCRYPT(?, ?) as ds_nome, AES_ENCRYPT(?, ?) as ds_cod ";
			$stmt = $db->query($sql, array($cartao, $key, $params['ds_nome'], $key, $params['cod'], $key));
			
			$encrypt = $stmt->fetchAll();
	
			$num = $encrypt[0]['ds_numero'];
			$tosave = $encrypt[0];
			
			// checar se cartão existe
			$sel = $PedidoModel->select()->setIntegrityCheck(false)
					->from(array('c' => 'sa_usuario_cartao'), array('*'))
					->where('id_usuario = ?', $pag->id_usuario)
					->where('ds_numero = ?', $num);
			$card = $PedidoModel->fetchRow($sel);
			
			if (!$card){
				$arr = explode('/', $params['ds_validade']);
				$tosave['ds_validade'] = $params['ds_validade'];
				$tosave['dt_validade'] = $arr[1].'-'.$arr[0].'-01';
				$tosave['nr_ult4num'] = substr($params['ds_numero'], strlen($params['ds_numero']) - 4);
				$tosave['en_bandeira'] = $pag['id_formas_pagamento'] == 1 ? 'VISA' : 'MASTERCARD';
				$tosave['id_usuario'] = $pag['id_usuario'];
				
				$uc = $tb->createRow($tosave);
				$uc->save();
			}
		//}

		/*$uc = $tb->createRow($params);
		$uc->id_usuario = $pag->id_usuario;
		$uc->id_formas_pagamento = $pag->id_formas_pagamento;
		$uc->save();*/
	}//saveCartao

	private function onlyNumbers($string)
	{
		//return eregi_replace("([^0-9])","", $string);
		$result = preg_replace("/[^0-9]/","", $string); 
		return $result;
	}

	private function GerarTid ($shopid,$pagamento) {
	
	    if(strlen($shopid) != 10) {
	        echo "Tamanho do shopid deve ser 10 dígitos";
	        exit;
		}
		
		if(is_numeric($shopid) != 1) {
	        echo "Shopid deve ser numérico";
	        exit;
		}
	
	    if(strlen($pagamento) != 4) {
	        echo "Tamanho do código de pagamento deve ser 4 dígitos.";
	        exit;
		}
	
	    //Número da Maquineta
	    $shopid_formatado = substr($shopid, 4, 5);
	
	    //Hora Minuto Segundo e Décimo de Segundo
	    $hhmmssd = date("His").substr(sprintf("%0.1f",microtime()),-1);
	
	    //Obter Data Juliana
	    $datajuliana = sprintf("%03d",(date("z")+1));
	
		//Último dígito do ano
	    $dig_ano = substr(date("y"), 1, 1);
	
	
	    return $shopid_formatado.$dig_ano.$datajuliana.$hhmmssd.$pagamento;
	}

	/**
	 * (non-PHPdoc)
	 * @see App_Pagamento_Meio_Abstract::_send()
	 */
	protected function _send(Row_PedidoPagamento $pag, array $params, App_Pagamento_Resultado $res)
	{
		$cfg = Zend_Registry::get('config')->cielo;

		error_reporting(0);

		$this->_LOJA = $cfg->loja;
		$this->_LOJA_CHAVE = $cfg->chave;
		$this->_PARCELAMENTO_TIPO = 2;
		$this->_CAPTURA = $cfg->captura;
		$this->_AUTORIZA = $cfg->autoriza;

		$ped = new Cielo_Pedido();

		$ped->formaPagamentoBandeira = strtolower($pag->getForma()->ds_cod);
		if($params['parc'] != 'A' && $params['parc'] != '1')
		{
			$ped->formaPagamentoProduto = $this->_PARCELAMENTO_TIPO;
			$ped->formaPagamentoParcelas = $params['parc'];
			
		}//if parcelamento
		else
		{
			$ped->formaPagamentoProduto = $params['parc'];
			$ped->formaPagamentoParcelas = 1;
		}//else à vista

		if($pag->getPedido()->fl_evento_da_casa == 1){
			$this->_LOJA = 1044690680;
            $this->_LOJA_CHAVE = "7b111ed75f52cf270d8e37290b124b00de239fd40821ad9fdfd2d2c80d4ebc32";
		}
		
		$ped->dadosEcNumero = $this->_LOJA;
		$ped->dadosEcChave = $this->_LOJA_CHAVE;

		$ped->capturar = $this->_CAPTURA;
		$ped->autorizar = $this->_AUTORIZA;

		$ped->dadosPortadorNumero = str_replace(array(' ', '-'), '', $params['ds_numero']);

		$varr = explode('/', $params['ds_validade']);
		$ped->dadosPortadorVal = $varr[1].$varr[0];

		$ped->dadosPortadorInd = "1";
		$ped->dadosPortadorCodSeg = $params['cod'];

		$ped->dadosPedidoNumero = $pag->getPedido()->ds_cod;
		$ped->dadosPortadorNome = $params['ds_nome'];
		$ped->dadosPedidoValor = $pag->getPedido()->nr_total * 100;
		
		$tb_evento = new PedidoEventoModel();
		$id_pedido = $pag->getPedido()->id_pedido;
		$id_evento = $tb_evento->getEventoByPedido($id_pedido)->id_evento;
		
		$id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
		
		/*if ($id_usuario == 16192){
			print_r($params);
			exit;
		}*/
		
		//if($id_usuario != '809738')
		//return;
		
		/*if($id_evento != 20062 && $id_evento != 20061)
		{
			return;
		}*/
		
		$ped->urlRetorno = $this->returnURL();
		
		try
		{
			$resp = $ped->RequisicaoTid();
			
			if(!isset($resp->tid))
			{
				if(!empty($resp))
					$this->saveTransaction($resp, $pag);

				$res->setStatusErro();
				if(!empty($resp))
				{
					$res->setResposta(utf8_decode($resp->asXML()));
					$res->setMensagem((string) $resp->mensagem);
				}//if
				else
				{
					$res->setResposta(array());
					$res->setMensagem('Erro na conexão ao Cielo');
				}//else
		
				/*if($id_evento != 20062 && $id_evento != 20061)
				{
					return;
				}*/
				//if($id_usuario != '809738')
				//return;
			}//if !tid

			$ped->tid = $resp->tid;
			$ped->pan = $resp->pan;
			$ped->status = $resp->status;

			$resp = $ped->RequisicaoAutorizacaoPortador();

			$ped->tid = $resp->tid;
			$ped->pan = $resp->pan;
			$ped->status = $resp->status;

			$this->saveTransaction($resp, $pag);

			$res->setStatusSucesso();
			$res->setResposta(utf8_decode($resp->asXML()));
			$res->setMensagem($ped->getStatus());

			if($resp->status == '4' || $resp->status == '6')
			{
				$res->setResultadoConfirmado();
				if (isset($params['fl_memorizar']))
					$this->saveCartao($pag, $params);
			}//if oka
			else
			{
				$res->setResultadoRecusado();
			}//else nope
		}//try resquest
		catch(Exception $e)
		{
			$this->saveTransaction($resp, $pag);

			$res->setStatusErro();
			$res->setResposta(utf8_decode($resp->asXML()));
			$res->setMensagem((string) $resp->autorizacao->mensagem);
		}//erro
	}//_send

	/**
	 *
	 * @param SimpleXMLElement $resp
	 * @param Row_PedidoPagamento $pag
	 */
	private function saveTransaction($resp, Row_PedidoPagamento $pag)
	{
		$tb = new TransacaoModel();

		$trs = $tb->createRow();
		$tb->getAdapter()->query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");

		$trs->id_pedido_pagamento = $pag->id_pedido_pagamento;
		$trs->nr_tid = $resp->tid;
		$trs->ds_pan = $resp->pan;
		$trs->nr_nsu = $resp->autorizacao->nsu;
		$trs->nr_codigo_autorizacao = $resp->autorizacao->arp;
		$trs->ds_status = $resp->status;
		$trs->fl_cielo = 1;
		if($resp->{'forma-pagamento'})$trs->nr_parcelas = $resp->{'forma-pagamento'}->parcelas;
		$trs->txt_resposta = utf8_decode($resp->asXML());

		$trs->save();
	}//saveTransaction

	private function returnURL()
	{
		$pageURL = 'http';
		if($_SERVER["SERVER_PORT"] == 443)$pageURL .= 's';
		$pageURL .= "://";
		if($_SERVER["SERVER_PORT"] != "80")$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			else $pageURL .= $_SERVER["SERVER_NAME"]. substr($_SERVER["REQUEST_URI"], 0);

		$pageURL .= $this->_RETURN_URL;
		return $pageURL;
	}//returnURL
}//App_Pagamento_Meio_Cielo