<?php

class App_Pagamento_Meio_Cielo extends App_Pagamento_Meio_Abstract
{
	
	public function _send(Row_PedidoPagamento $pag, array $params, App_Pagamento_Resultado $res){

        $nr_total =  $pag->getPedido()->nr_total * 100;
        $estabelecimento = 1044690680;
		
        $bandeira = $pag->getForma()->ds_cod;
        if(strtolower($bandeira) == "mastercard"){
            $bandeira = 'Master';
        }
       
        $data = array(
            "estabelecimento" => $estabelecimento,
            "MerchantOrderId" => $pag->getPedido()->id_pedido,
            "Customer" => array(
                "Name" => $params['ds_nome']
            ),
            "Payment" => array( 
                "Type" => 'CreditCard',
                "Amount" => $nr_total,
                "Installments" => $params['parc'],
                "Interest" => "ByMerchant",
                "Capture" => true,
                'CreditCard' => array(  
                    "CardNumber" => str_replace(array(' ', '-'), '', $params['ds_numero']),
                    "Holder" => $params['ds_nome'],
                    "ExpirationDate" => $params['ds_validade'],
                    "SecurityCode" => $params['cod'],
                    "Brand" => strtoupper($bandeira)
                )
            )
        );

        try{

            $response = json_decode($this->sendCURL($data, '/1/sales/', 'r','POST'));

            if(isset($response->Payment->Tid)){
                $dataTransaction = array(
                    "id_pedido_pagamento" => $pag->id_pedido_pagamento,
                    "nr_estabelecimento" => $estabelecimento,
                    'nr_tid'    =>  $response->Payment->Tid,
                    "nr_nsu"    =>  $response->Payment->ProofOfSale,
                    "nr_codigo_autorizacao" =>  $response->Payment->AuthorizationCode,
                    "ds_status" =>  $response->Payment->ReturnCode,
                    "nr_parcelas"   =>  $params['parc'],
                    "dt_cadastro" => date('Y-m-d H:i:s'),
                    "fl_cielo" => 1,
                    "txt_resposta"  => $response->Payment->ReturnMessage
                );
            }else{
                $dataTransaction = array(
                    "id_pedido_pagamento" => $pag->id_pedido_pagamento,
                    "nr_estabelecimento" => $estabelecimento,
                    "ds_status" =>  5,
                    "nr_parcelas"   =>  $params['parc'],
                    "dt_cadastro" => date('Y-m-d H:i:s'),
                    "fl_cielo" => 1,
                    "txt_resposta"  => json_encode($response)
                );
            }

            $this->saveTransaction($dataTransaction);

            if($response->Payment->ReturnCode  == '00'){
                $res->setResultadoConfirmado();
				$this->updateStatusPedido($pag->getPedido()->id_pedido, 'CONFIRMADO');
				if (isset($params['fl_memorizar']))
					$this->saveCartao($pag, $params);
            }else{
                $res->setResultadoRecusado();
				$this->updateStatusPedido($pag->getPedido()->id_pedido, 'RECUSADO');
            }

        }catch(\Exception $e){
            var_dump($e->getMessage());
        }
    }
	
	public function sendCURL($params = array(), $api, $url_type, $type){
        
        $ambiente = 'PRODUCTION';

        $merchantId = '4b6f1b0d-4078-458c-8098-99273eeaa8da';
        $merchantKey = 'r0O65THfKcoY4pizG5xIfU8XbxhfOXyZJ2CXtV9Q';
        
        $header = array(                                                                          
            'Content-Type: application/json',
            'MerchantId: '.$merchantId,
            'MerchantKey: '.$merchantKey
        );
        
        if($ambiente == 'PRODUCTION'){
            if($url_type == 'r'){
                $url = 'https://api.cieloecommerce.cielo.com.br';
            }else{
                $url = 'https://apiquery.cieloecommerce.cielo.com.br';
            }
        }else{
            if($url_type == 'r'){
                $url = 'https://apisandbox.cieloecommerce.cielo.com.br';
            }else{
                $url = 'https://apiquerysandbox.cieloecommerce.cielo.com.br';
            }
        }
        
        $data = json_encode($params);
        
        $ch = curl_init($url.$api);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CAINFO, dirname(dirname(__DIR__)).'/../../application/ssl/Raiz.crt');
        //curl_setopt($ch, CURLOPT_CAINFO, env('APP_BASE_PATH').'/ssl/Intermediaria.crt');
        //curl_setopt($ch, CURLOPT_CAINFO, env('APP_BASE_PATH').'/ssl/cieloecommerce.cielo.com.br.crt');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $result = curl_exec($ch);
        echo curl_error($ch);
        
        return $result;
    }    
	
	private function saveTransaction($dataTransaction) {
        $tb = new TransacaoModel();

		$trs = $tb->createRow();
		$tb->getAdapter()->query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");

		$trs->id_pedido_pagamento = $dataTransaction['id_pedido_pagamento'];
		$trs->nr_tid = $dataTransaction['nr_tid'];
		$trs->nr_nsu = $dataTransaction['nr_nsu'];
		$trs->nr_codigo_autorizacao = $dataTransaction['nr_codigo_autorizacao'];
		$trs->ds_status = $dataTransaction['ds_status'];
		$trs->fl_cielo = 1;
		$trs->nr_parcelas = $dataTransaction['nr_parcelas'];
		$trs->txt_resposta = $dataTransaction['txt_resposta'];

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