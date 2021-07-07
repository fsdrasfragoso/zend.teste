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
		$tb = new UsuarioCartaoModel();
		$tb->deleteByUsuarioFormadePagamento($pag->id_usuario, $pag->id_formas_pagamento);

		$uc = $tb->createRow($params);
		$uc->id_usuario = $pag->id_usuario;
		$uc->id_formas_pagamento = $pag->id_formas_pagamento;
		$uc->save();
	}//saveCartao

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
		$this->_PARCELAMENTO_TIPO = $cfg->parcelamento;
		$this->_CAPTURA = $cfg->captura;
		$this->_AUTORIZA = $cfg->autoriza;

		$this->saveCartao($pag, $params);

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
		$ped->dadosPedidoValor = $pag->getPedido()->nr_total * 100;

		$ped->urlRetorno = $this->returnURL();

		try
		{
			$resp = $ped->RequisicaoTid();

			if(!isset($resp->tid))
			{
				if(!empty($resp))$this->saveTransaction($resp, $pag);

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
				
				return;
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
		$trs->ds_pan = $resp->tid;
		$trs->ds_status = $resp->status;
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