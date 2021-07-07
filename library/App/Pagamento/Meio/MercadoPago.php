<?php
require_once __DIR__ . "/..//..//..//../vendor/autoload.php";
 
 /* require_once 'vendor/autoload.php';
 
 MercadoPago\SDK::setClientId("8220017516726143");
 MercadoPago\SDK::setClientSecret("fBf0QEnFo8mvajGL2ybA5hCJUATMLyWY");
  $nr_total =  $pag->getPedido()->nr_total; 
           
            MercadoPago\SDK::setAccessToken("MERCADOPAGO_TOKEN");
Administrativo1 - Casa

Modo Sandbox
Public key: TEST-526ba8f2-57eb-4e23-b78f-0c35d8767794
Access token: TEST-8220017516726143-083113-10150b521f10a8ebd6ee96cd79f0cebd-340843342

Modo Produção
Public key: APP_USR-abfdd44b-cb5d-4316-a4ae-df7f251585a4
            APP_USR-abfdd44b-cb5d-4316-a4ae-df7f251585a4 
Access token: APP_USR-8220017516726143-083113-e3551f5d3fb38c1b7aaea77fc826ab94-340843342

Administrativo - Terceiros

Modo Sandbox
Public key: TEST-eb1af6ce-d695-4540-84da-3e1dfa52ac14
Access token: TEST-3015381941302824-082910-3f7a831cff674476f02a7dd23d00029c-340810242

Modo Produção
Public key: APP_USR-d6b1e917-b9d4-4989-ad9d-5384e87a976f
            
Access token: APP_USR-3015381941302824-082910-c379dcb5e7474f7a3439bf258f29e5ec-340810242

Administrativo2 - Casa Cielo

Modo Sandbox
Public key: TEST-7ac08fdb-4588-4e12-8c5c-11b5a4a3bcd0
Access token: TEST-7758529681158774-083113-1fc9daed9f7ab260255989dd7de099cd-340843243

Modo Produção
Public key: APP_USR-1a771d0c-75c6-4cdb-a67f-074e43ddc504
Access token: APP_USR-7758529681158774-083113-2eb75176e8ab31dab0f4c98033c2d84b-340843243

 */

class App_Pagamento_Meio_mercadopago extends App_Pagamento_Meio_Abstract{
    public function _send(Row_PedidoPagamento $pag, array $params, App_Pagamento_Resultado $res){
     if($_SERVER['HTTP_HOST']  == 'dev.adm.ativo.com'){
        MercadoPago\SDK::setAccessToken("TEST-740669582304309-062410-25ad63e40af041b43c9b586440f2bc6c-135511714");
    }else if($params['fl_evento_da_casa']==2){
            MercadoPago\SDK::setAccessToken("APP_USR-7758529681158774-083113-2eb75176e8ab31dab0f4c98033c2d84b-340843243");
        }else{
            MercadoPago\SDK::setAccessToken("APP_USR-7758529681158774-083113-2eb75176e8ab31dab0f4c98033c2d84b-340843243");
        }  

        
        $valorTotal = $pag->getPedido()->nr_total;         
        $payment = new MercadoPago\Payment();
 
        $payment->transaction_amount = (float) $valorTotal;
        $payment->token = $params['token'];
        $payment->description =  $params['description'];
        $payment->installments = $params['parcela'];
        $payment->payment_method_id = $params['paymentMethodId'];
        $payment->payer = array(
            "email" => $params['email']
        );
 
        $payment->save();
        $pagamento = new PedidoPagamentoModel();
        $rowPagamento = $pagamento->fetchRow($pagamento->select()->where('id_pedido = ?', $pag->getPedido()->id_pedido));
        $dataTransaction = array(
            "id_pedido_pagamento" => $rowPagamento->id_pedido_pagamento,
            'nr_tid'    =>  $payment->id,
            "nr_nsu"    =>  $payment->identification->number,
            "nr_codigo_autorizacao" =>  $payment->authorization_code,
            "ds_status" =>  $payment->status,
            "nr_parcelas"   =>  1,
            "dt_cadastro" => date('Y-m-d H:i:s'),
            "fl_cielo" => 0,
            "txt_resposta"  => json_encode($payment),
            "ds_adquirente" => 'mercadopago'
          );
          $this->saveTransaction($dataTransaction);
        if($payment->status == 'approved'){
            $res->setResultadoConfirmado();
            $this->updateStatusPedido($pag->getPedido()->id_pedido, 'CONFIRMADO');            
        }else{
            $res->setResultadoRecusado();
			$this->updateStatusPedido($pag->getPedido()->id_pedido, 'RECUSADO');
        }
        
 
    
           // $res->setResultadoConfirmado();

    
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

private function saveTransaction($dados) {

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
  

}
    
