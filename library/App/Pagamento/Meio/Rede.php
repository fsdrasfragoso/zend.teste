<?php
use Rede\Authorization;
use erede\model\AuthorizationResponse;
use erede\model\AuthorizedCreditRequest;
use erede\model\TransactionRequest;
use erede\model\TransactionResponse;


require_once __DIR__ . "/..//..//..//../vendor/erede/Classloader.php";

//require_once __DIR__ . "/..//..//..//../vendor/autoload.php";
class App_Pagamento_Meio_Rede extends App_Pagamento_Meio_Abstract{ 

    public function _send(Row_PedidoPagamento $pag, array $params, App_Pagamento_Resultado $res){ 
        $estabelecimento = $params['fl_evento_da_casa'] == 1 ? '30314666' : '73046426';
        $chave =$params['fl_evento_da_casa'] == 1 ? '4570dbbddacf4ddea7b00a69efb9c3b5' : '738cd451f3364c2a8ec6e9c4635ef1d4'; 
        $varr = explode('/', $params['ds_validade']);
     
        $acquirer = new Acquirer($estabelecimento,$chave, 1);
        $request = new TransactionRequest();
        $nr_total =  $pag->getPedido()->nr_total * 100;
      
        $request->setCapture(TRUE);
        $request->setAmount($nr_total);
        $request->setReference($pag->getPedido()->id_pedido);
        $request->setCardNumber($params['ds_numero']);
        $request->setSecurityCode($params['cod']);
        $request->setExpirationMonth($varr[0]);
        $request->setExpirationYear($varr[1]);
        $request->setCardHolderName("'".$params['ds_nome']."'");
        $request->setInstallments($params['parc']);
        $response = $acquirer->authorize($request); 
       
        $this->saveTransaction($response, $pag->id_pedido_pagamento, $estabelecimento,$params);
        echo $response->getReturnCode();
        echo '<br/>';
        if($response->getReturnCode() == '00'){
            $res->setResultadoConfirmado();
            if (isset($params['fl_memorizar']))
				$this->saveCartao($pag, $params);
            $this->updateStatusPedido($pag->getPedido()->id_pedido, 'CONFIRMADO');
        }else{
            $res->setResultadoRecusado();
            $this->updateStatusPedido($pag->getPedido()->id_pedido, 'RECUSADO');
        }    
        
    
    }
    
    private function saveTransaction($retorno, $pagamento, $nr_estabelecimento,$params) {
        
        $array = (array) $retorno;
        
        $dados = array(
            "id_pedido_pagamento" => $pagamento,
            "nr_estabelecimento" => $nr_estabelecimento,
            "nr_tid" => $retorno->getTid(),
            "ds_pan" => '',
            "nr_nsu" => $retorno->getNsu(),
            "nr_codigo_autorizacao" => '',
            "ds_status" => $retorno->getReturnCode(),
            "ds_adquirente" => 'Rede',
            "fl_cielo" => 0,
            "fl_mundipagg" => 0,
            "dt_cadastro" => date('Y-m-d H:i:s'),
            "nr_parcelas" => $params["parc"],
            "txt_resposta" => utf8_encode(json_encode($array))
        );
        
        $tb = new TransacaoModel();

		$trs = $tb->createRow();
        $tb->getAdapter()->query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'"); 
        
        $trs->id_pedido_pagamento = $dados['id_pedido_pagamento'];
		$trs->nr_tid =  $dados['nr_tid'];
		$trs->nr_nsu =  $dados['nr_nsu'];
		$trs->nr_codigo_autorizacao =  $dados['nr_codigo_autorizacao'];
		$trs->ds_status = $dados['ds_status'];
		$trs->fl_cielo = 0;
		$trs->nr_parcelas =  $dados['nr_parcelas'];
		$trs->txt_resposta =  $dados['txt_resposta'];
        $trs->fl_mundipagg = 0;
        $trs->ds_adquirente = $dados['ds_adquirente'];

		$trs->save();
    }

    private function updateStatusPedido($id_pedido, $status_retorno){
		$pedido = new PedidoModel();
		$pagamento = new PedidoPagamentoModel();
		
		$rowPedido = $pedido->fetchRow($pedido->select()->where('id_pedido = ?', $id_pedido));
		$rowPagamento = $pagamento->fetchRow($pagamento->select()->where('id_pedido = ?', $id_pedido));
		
		// Change the value of one or more columns
		if($status_retorno=="CONFIRMADO"){
			$rowPedido->id_pedido_status = 2;
			$rowPagamento->fl_status = 'CONFIRMADO';
		}else{
			$rowPedido->id_pedido_status = 5;
			$rowPagamento->fl_status = 'RECUSADO';
		}
		 
		// UPDATE the row in the database with new values
		$rowPedido->save();
		$rowPagamento->save();
		
    }
    
    private function saveCartao(Row_PedidoPagamento $pag, array $params)
	{
		$cartao = str_replace(array('-', '.', '_'), '', $params['ds_numero']);
	
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
	}

}