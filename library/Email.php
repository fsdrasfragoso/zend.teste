<?php

class Email extends Zend_Controller_Action{
	public static function envialEmail($ds_email, $mensagem, $assunto, $tipo, $id_user, $id)
	{
		$emailmanager = new Emailmanager('087039-MTE0OTkuYjhmOWZhOWI4YmNjYjI4YWNiMTA4ZTFkMDdhMw-MS5hNDI-MC5kMjA', 'info@ativo.com');
        $retorno_emailmananger = $emailmanager->to(strtolower($ds_email))->subject(utf8_encode($assunto))->html_message($mensagem)->send();

		$tb_log = new LogprocessamentoModel();

		$dataI = array(
			"dt_processamento" => date('Y-m-d H:i:s'),
			"nr_pedido" => $id,
			"fl_tipo_log" => $tipo,
			"ds_corpo_email" => addslashes($mensagem),
			"id_usuario" => $id_user,
			"retorno_emailmanager" => $retorno_emailmananger,
			"ds_rota" => print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true)
		);

		$tb_log->insert($dataI);

	}

	public function enviaEmailAtivo($id, $parametro) {
		$tb_pedido = new PedidoModel();
		$tb_pedidop = new PedidoPagamentoModel();
		$pedidoEnderecoModel = new PedidoEnderecoModel();
        $tb_usuario = new UsuarioModel();

		$view = new Zend_View();

		$user = $tb_usuario->getUsuarioByPedido($id);
		$pedido = $tb_pedido->getPedido($id);

        $view->id_combo = $pedido['id_combo'];
		$view->id_user = $user["id_usuario"];
		$view->ds_nome = $user["ds_nome"];
		$view->ds_email = $user["ds_email"];

		$view->id = $id;

        try {

			if($parametro=='emailConfirmacao.phtml') {
				$view->assunto = "Ativo.com - Confirmação de Pagamento do Pedido";
				$tipo = 1;
			} else if($parametro=='emailBoleto.phtml') {
				$view->assunto = "Ativo.com - Novo boleto para pagamento";
				$tipo = 3;
			}
			else if($parametro=='emailConfirmacao_balcao.phtml') {
				$view->assunto = "Ativo.com - Confirmação de Pagamento do Pedido";
				$tipo = 1;
			}else if($parametro=='emailAbandonoCarrinho') {
				$view->assunto = "Ativo.com - Não Fique de Fora";
				$tipo = 1;	
			}else if($parametro=='emailAbandonoCarrinho') {
				$view->assunto = "Ativo.com - Não Fique de Fora";
				$tipo = 1;	
			} else {
				$view->assunto = "Ativo.com - Pedido Cancelado";
				$tipo = 2;
			}

			$view->evento = $tb_pedido->getPedidoByEvento2($id);
			$view->produto = $tb_pedido->getPedidoByProduto($id);
			$view->foto = $tb_pedido->getPedidoByFoto($id);
			$view->kit = $pedidoEnderecoModel->getPedidoEnderecoByPedido2($id);
			$view->url = "http://www.ativo.com";
			$view->url_ativo = "http://www.ativo.com";

			// perguntas limites
			$view->questionario_limite = $tb_pedido->getPerguntaLimite($id);

			$view->setScriptPath(APPLICATION_PATH . '/views/scripts/email/');
			$mensagem = $view->render($parametro);

			if	($view->evento[0]['fl_email_inscricao'] == 1)
				self::envialEmail($view->ds_email, $mensagem, $view->assunto, $tipo, $view->id_user, $view->id);

        }//try it
        catch (Zend_Exception $e) {
            echo $e->getMessage() . '<br/>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
            die();
        }//catch
    }//setstatuspedido

	public function enviaEmailAtivo2($id, $parametro, $balcao=null, $fl_envia=1)
	{

		if($fl_envia==1){
			
			$tb_pedido = new PedidoModel();
			$tb_pedidop = new PedidoPagamentoModel();
			$pedidoEnderecoModel = new PedidoEnderecoModel();
			$tb_usuario = new UsuarioModel();

			$view = new Zend_View();

			$pedido = $tb_pedido->getPedido($id);
			$user = $tb_usuario->getUsuarioByPedido($id);
			$orderDetail = $tb_pedido->getOrderDetail($id);

			if( $orderDetail['id_formas_pagamento'] !== "4" && $orderDetail['nr_total'] > 0 ){
				$view->orderTotal = " no valor de R$" . number_format( $pedido['nr_total'], 2, ',', '.' );
			}else{
				$view->orderTotal = "";
			}			

			$view->id_combo = $pedido['id_combo'];
			$view->ds_codigo_validacao = $pedido['ds_codigo_validacao'];
			$view->nr_total = number_format($pedido['nr_total'], 2, ',', '.');
			$view->dt_pedido = date("d/m/Y", strtotime($pedido['dt_pedido']));

			$view->id_user = $user["id_usuario"];
			$view->ds_nome = $user["ds_nome"];
			$view->ds_email = $user["ds_email"];
			$view->pelotao = $user["pelotao"];

			$view->id = $id;

			try {
				$view->evento = $tb_pedido->getPedidoByEvento2($id);

				// parametros: emailPagamento / emailReativar / emailBoleto / emailCancelamento
				if($parametro=='emailConfirmacao') 
					$tipo = 1;				
				else if($parametro=='emailBoleto' || $parametro=='emailCancelamento' || $parametro=='emailCancelamentoBoleto')
					$tipo = 3;
				else
					$tipo = 2;

				if(!empty($view->evento))
					$view->kit = $pedidoEnderecoModel->getPedidoEnderecoByPedido2($id);
				$view->produto = $tb_pedido->getPedidoByProduto($id);
				$view->foto = $tb_pedido->getPedidoByFoto($id);
				$view->url_ativo = "http://carrinho.ativo.com/";
				$view->url_admin = 'http://adm.ativo.com/';
				$view->url = URL_SITE;
				$view->setScriptPath(APPLICATION_PATH . '/views/scripts/email/');
				$view->setHelperPath(APPLICATION_PATH . '/views/helpers/', 'SetTranslation');
-				$view->helper = $view->getHelper('SetTranslation');

				// perguntas limites
				$view->questionario_limite = $tb_pedido->getPerguntaLimite($id);

				if($balcao == 1)
					$view->balcao = true;

				$mensagem = $view->render($parametro.'.phtml');
				//$mensagem = htmlspecialchars_decode(htmlentities(utf8_decode($mensagem), ENT_NOQUOTES), ENT_NOQUOTES);

				//costumerkey exacttarget
				if($parametro == 'emailCancelamentoBoleto')
					$parametro = 'emailCancelamento';
				if($parametro == 'emailConfirmacao')
					$parametro = 'emailPagamento';

				$parametro = str_replace('email', '', $parametro);

				

				

				self::exactEmail($view->ds_email, $mensagem, $parametro, $tipo, $view->id_user, $view->id);

				

			}//try it
			catch (Zend_Exception $e) {
				echo $e->getMessage() . '<br/>';
				echo '<pre>' . $e->getTraceAsString() . '</pre>';
				die();
			}//catch
			
		}
		
		
    }//enviaEmailAtivo2

	public function enviaEmailBalcao($id, $fl_envia=1)
	{
		if($fl_envia==1){			
			
			$tb_pedido = new PedidoModel();
			$tb_usuario = new UsuariobalcaoModel();

			$view = new Zend_View();

			$pedido = $tb_pedido->getPedido($id);
			$view->id_combo = $pedido['id_combo'];
			//$usuario_balcao = $tb_usuario->getUsuarioBalcaoByPedido($id);
			
			$view->id = $id;
           
			try {
				$eventos = $tb_pedido->getPedidoByEvento2($id);
				foreach ($eventos as $evento) {
					//cria array para não modificar loop do layout
					$array_evento = array();
					$array_evento[] = $evento;
					$view->evento = $array_evento;
					$view->id_user = $evento['id_usuario_balcao'];
					$view->ds_nome = $evento['ds_nome_balcao'];
					$view->ds_email = $evento['ds_email_balcao'];
					$view->url_ativo = $_SERVER['SCRIPT_NAME'];

					$view->url = "http://carrinho.ativo.com/";
					$view->setScriptPath(APPLICATION_PATH . '/views/scripts/email/');

					$mensagem = $view->render('emailPagamento.phtml'); 
                   
					self::exactEmail($view->ds_email, $mensagem, 'Pagamento', 1, $view->id_user, $view->id,NULL);
				}
			}//try it
			catch (Zend_Exception $e) {
				echo $e->getMessage() . '<br/>';
				echo '<pre>' . $e->getTraceAsString() . '</pre>';
				die();
			}//catch
			
		}
		
    }//enviaEmailBalcao
    public function enviaEmailAbandono($html,$email,$id_usuario,$assunto){
		try {
		//	$mensagem = htmlspecialchars_decode(htmlentities(utf8_decode($html), ENT_NOQUOTES), ENT_NOQUOTES);
			self::exactEmail($email, $html, 'AbandonoCombo', 1, $id_usuario, 1, $assunto);	
			
		}catch (Zend_Exception $e) {
				echo $e->getMessage() . '<br/>';
				echo '<pre>' . $e->getTraceAsString() . '</pre>';
				die();
		}//catch	
	}

	public function enviaEmailResultadoCorridaVirtual($html,$email,$id_usuario,$assunto){
		try 
		{
			//$mensagem = htmlspecialchars_decode(htmlentities(utf8_decode($html), ENT_NOQUOTES), ENT_NOQUOTES);
			self::exactEmail($email, $html, 'ResultadoCorridaVirtual', 1, $id_usuario, 1, $assunto);	
			echo $html; 			
		}catch (Zend_Exception $e) {
				echo $e->getMessage() . '<br/>';
				echo '<pre>' . $e->getTraceAsString() . '</pre>';
				die();
		}//catch
		
		return true; 
	}
	public function enviaEmailBalcaoEventos($id_usuario, $id_evento, $ds_evento, $ds_email, $ds_nomebalcao) {

		$tb_pedido = new PedidoModel();
        $tb_usuario = new UsuariobalcaoModel();

		$view = new Zend_View();
		$view->ds_evento = $ds_evento;
		$view->ds_nome = $ds_nomebalcao;
		$view->ds_email = $ds_email;
		$view->id_user = $id_usuario;
		$view->url_ativo = "http://carrinho.ativo.com";

        try {
			// Inscrições de Ontem
				$sel = $tb_pedido->select()
						->setIntegrityCheck(false)
						->from(array("pe" => "sa_pedido_evento"), array())
						->joinInner(array('pd' => 'sa_pedido'), 'pd.id_pedido = pe.id_pedido', array('id_pedido_status','dt_pedido'))
						->joinInner(array('ub' => 'sa_usuario_balcao'), 'ub.id_usuario = pe.id_usuario_balcao', array('ds_nome'))
						->joinInner(array('ps' => 'sa_pedido_status'), 'ps.id_pedido_status = pd.id_pedido_status', array('ds_status'))
						->joinInner(array('mo' => 'sa_evento_modalidade'), 'mo.id_modalidade = pe.id_modalidade', array('nm_modalidade'))
						->joinInner(array('ca' => 'sa_modalidade_categoria'), 'ca.id_categoria = pe.id_categoria', array('ds_categoria'))
						->joinInner(array('tc' => 'sa_tamanho_camiseta'), 'tc.id_tamanho_camiseta = pe.id_tamanho_camiseta', array('ds_tamanho'))
						->where('pe.id_evento = '.$id_evento)
						->where('pd.id_usuario = '.$id_usuario)
						->group('pe.id_pedido_evento')
						->order('pd.id_pedido_status ASC');

				$result = $tb_pedido->fetchAll($sel);
				$view->result = $result;

				//Dispara email
				$assunto = "Ativo.com - Resumo Inscrições - ".$ds_evento;

				$view->setScriptPath(APPLICATION_PATH . '/views/scripts/email/');
				if(count($view->result) > 0)
				{
					$mensagem = $view->render('emailBalcaoEventos.phtml');
					self::exactEmail($view->ds_email, $mensagem, 'RotinasProcessamento', 1, $view->id_user, 0, $assunto);
				}

        }//try it
        catch (Zend_Exception $e) {
            echo $e->getMessage() . '<br/>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
            die();
        }//catch
    }//enviaEmailBalcao

	public function enviaEmailFaturar($id, $id_usuario, $ds_email) {
        try {

			$cliente = 'emailFaturar';
			$financeiro = 'emailFaturarFinanceiro';

			$EmailModel = new EmailModel();
			if(!empty($cliente)) {
				$email_cliente = array("id_usuario"  	=> $id_usuario,
										"id_pedido"		=> $id,
										"ds_email"		=> $ds_email,
										"corpo_email"	=> $cliente,
										"ds_assunto"	=> 'Ativo.com - Solicitação de Faturamento',
										"costumerkey"	=> 'Faturar',
										"dt_criacao"	=> date("Y-m-d H:i:s"));
				$EmailModel->insert($email_cliente);
				unset($email_cliente);
			}
			if(!empty($financeiro)) {
				$email_grupos = array("id_usuario"  	=> $id_usuario,
											"id_pedido"		=> $id,
											"ds_email"		=> 'grupos@ativo.com',
											"corpo_email"	=> $financeiro,
											"ds_assunto"	=> 'Ativo.com - Solicitação de Faturamento',
											"costumerkey"	=> 'Faturar',
											"dt_criacao"	=> date("Y-m-d H:i:s"));
				$EmailModel->insert($email_grupos);
				unset($email_grupos);

				$email_financeiro = array("id_usuario"  	=> $id_usuario,
											"id_pedido"		=> $id,
											"ds_email"		=> 'faturamentogrupos@ativo.com',
											"corpo_email"	=> $financeiro,
											"ds_assunto"	=> 'Ativo.com - Solicitação de Faturamento',
											"costumerkey"	=> 'Faturar',
											"dt_criacao"	=> date("Y-m-d H:i:s"));
				$EmailModel->insert($email_financeiro);
				unset($email_financeiro);
			}
		}

		catch (Zend_Exception $e) {
            echo $e->getMessage() . '<br/>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
            die();
        }
    }//enviaEmailFaturar
    function enviarEmail($ds_email, $mensagem, $assunto) {
		
		include_once(dirname(__FILE__).'/exacttarget/exacttarget_soap_client.php');
		$wsdl = 'https://webservice.s6.exacttarget.com/etframework.wsdl';
		$client = new ExactTargetSoapClient($wsdl, array('trace'=>1));     
		$client->username = 'AdmNorte'; 
		$client->password = 'Esfera@2020';
		
		$ts = new ExactTarget_TriggeredSend();
		$tsd = new ExactTarget_TriggeredSendDefinition();
		$tsd->CustomerKey = 'AbandonoCombo';
		$tsd->Priority = "High";

		$sub = new ExactTarget_Subscriber();    
		$sub->EmailAddress = $ds_email;
		$sub->SubscriberKey = $ds_email;

		$html =  new ExactTarget_Attribute();  
		$html->Name = "CUPOM";  
		$html->Value = $mensagem;
		
		$subject =  new ExactTarget_Attribute();  
		$subject->Name = "Nome";  
		$subject->Value = $assunto; 

		$sub->Attributes = array($html, $subject);
		$ts->Subscribers = array();
		$ts->Subscribers = $sub;    
		$ts->TriggeredSendDefinition = $tsd;
		
		$object = new SoapVar($ts, SOAP_ENC_OBJECT, 'TriggeredSend', "http://exacttarget.com/wsdl/partnerAPI");
		$request = new ExactTarget_CreateRequest();
		$request->Options = NULL;
		$request->Objects = array($object);
		$retorno = $client->Create($request);
		
		return $retorno;
}
	public function exactEmail($ds_email, $mensagem, $parametro, $tipo, $id_user, $id, $assunto = null) {

		$id_user = empty($id_user) ? 1 : $id_user;
		include_once(dirname(__FILE__).'/exacttarget/exacttarget_soap_client.php');
		$wsdl = 'https://webservice.s6.exacttarget.com/etframework.wsdl';
		$client = new ExactTargetSoapClient($wsdl, array('trace'=>1));
		$client->username = 'AdmNorte';
		$client->password = 'Esfera@2020';
		
		$ts = new ExactTarget_TriggeredSend();
		$tsd = new ExactTarget_TriggeredSendDefinition();
		$tsd->CustomerKey = $parametro;

		$sub = new ExactTarget_Subscriber();
		$sub->EmailAddress = $ds_email;
		$sub->SubscriberKey = $ds_email;

		$html =  new ExactTarget_Attribute();		
		$html->Name = "CUPOM";
		$html->Value = $mensagem;

		if($assunto != null){
			$subject =  new ExactTarget_Attribute();			
			$subject->Name = "Nome";
			$subject->Value = $assunto;
			$sub->Attributes = array($html, $subject);
		}else{
			$sub->Attributes = array($html);
		}

		$ts->Subscribers = array();
		$ts->Subscribers = $sub;
		$ts->TriggeredSendDefinition = $tsd;

		$object = new SoapVar($ts, SOAP_ENC_OBJECT, 'TriggeredSend', "http://exacttarget.com/wsdl/partnerAPI");
		$request = new ExactTarget_CreateRequest();
		$request->Options = NULL;
		$request->Objects = array($object);
		$retorno = $client->Create($request);

		$tb_log = new LogprocessamentoModel();
		$dataI = array(
			"dt_processamento" => date('Y-m-d H:i:s'),
			"nr_pedido" => $id,
			"fl_tipo_log" => $tipo,
			"ds_corpo_email" => addslashes($mensagem),
			"id_usuario" => $id_user,
			"retorno_emailmanager" => print_r($retorno, true),
			"ds_rota" => print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true)
		);

		$tb_log->insert($dataI);

		return $retorno;

	}

	public function GerarNrPeito($pedido) {

		try {
			$tb_pe = new PedidoEventoModel();
			$atletas = $tb_pe->getNrPeitoEvento($pedido);

			//gerar número de peito
			if(!empty($atletas)) {
				for($x=0;$x<count($atletas);$x++) {
					if(($atletas[$x]['num_peito']+$x) >= $atletas[$x]['nr_peito_de'] && ($atletas[$x]['num_peito']+$x) < $atletas[$x]['nr_peito_ate']) {
						$nr_peito = $atletas[$x]['num_peito']+1;
						$tb_pe->update(array("nr_peito" => $nr_peito), "id_pedido_evento = " . $atletas[$x]["id_pedido_evento"]);
					}
				}
				return 'nr peitos cadastrados';
			}
			else
				return 'nr peitos não cadastrados';
		}
		catch (Zend_Exception $e) {
            echo $e->getMessage();
        }

	}

	public function enviaEmailDevolucaoChip($id_usuario, $nome, $email, $evento_planilha, $evento)
	{
            #echo $id_usuario."<br>";
            #echo $nome."<br>";
            #echo $email."<br>";
            #echo $id_evento."<br>";
            #echo $evento."<br>";
            $assunto="Devolução do CHIP";
            #echo "<br>";
            /*$mensagem="
            Sr.(a) ".$nome."<br><br>
            Não foi registrado em nosso sistema a devolução do chip de cronometragem usado por você em <b>".$evento."</b><br><br>

            Favor clicar no link abaixo:<br><br>

            <a href='http://sac.ativo.com'>sac.ativo.com</a><br><br><br>";*/

            $mensagem='
                    <table width="600" align="center" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                    <td width="600" height="27" align="center">&nbsp;</td>
                    </tr>
                    </table>
                    <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" id="Table_01">
                    <tr>
                    <td><img src="http://www.porminuto.com.br/ativo/2015/chip/images/devolucao_chip_01.jpg" border="0" alt="Ativo" style="display:block; border:0px;"></td> </tr> </table>
                    <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" id="Table_01">
                    <tr>
                    <td valign="top" bgcolor="#DBDBDB" style="padding:20px 28px;">
                    <font style="font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:20px; color:#000; text-decoration:none; line-height:15px">
                    <b>Prezado (a) '.$nome.',</b> </font><br> <br>
                    <font style="font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:16px; color:#000; text-decoration:none; line-height:22px">
                    Até o momento não consta em nosso sistema a devolução do chip utilizado na prova '.$evento.'. <br> <br>
                    A não devolução do chip poderá implicar no bloqueio de seu cadastro e cobrança referente ao valor do chip. <br> <br>
                    <a href="http://saceventos.ativo.com/salesforce/php/entrega_chip.php?motivo=correios" target="_blank" style="color:#F00">Clique aqui</a> para fazer a devolução do chip <br>
                    <a href="http://saceventos.ativo.com/salesforce/php/entrega_chip.php?motivo=pagamento" target="_blank" style="color:#F00">Clique aqui</a> aqui para efetuar pagamento do chip </font>
                    </td>
                    </tr> </table>
                    <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                    <tbody>

                    <tr>
                    <td width="100%" height="5"></td>
                    </tr>

                    <tr>
                    <td align="center" valign="middle" style="font-family: Helvetica, arial, sans-serif; font-size: 13px;color: #000000" st-content="preheader">Não deseja mais receber nossos emails? <a href="%%unsub_center_url%%" target="_blank" style="text-decoration: none; color: #541849;">Cancele aqui</a> <br>
                    <br>
                    <span style="font-family: Helvetica, arial, sans-serif; font-size: 13px;color: #000;">Adicione  ativo@pratique.ativo.com  aos seus contatos para receber<br>
                    com sucesso nossos e-mails </span></td>
                    </tr>

                    <tr>
                    <td width="100%" height="5"></td>
                    </tr>

                    </tbody>
                    </table>';

            #echo $mensagem;
            #exit;
            $exact = Email::exactEmail(strtolower($email), $mensagem, 'RotinasProcessamento', 1, 0, 0, $assunto);

            if(!$exact){
                echo "Ocorreu algum erro!<br>";
            }


	}

	public function enviaEmailCustomizado($id_usuario, $nome, $email, $evento_planilha, $evento, $conteudo, $assunto)
	{

		$mensagem='
                    <table width="600" align="center" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                    <td width="600" height="27" align="center">&nbsp;</td>
                    </tr>
                    </table>
                    <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" id="Table_01">
                    <tr>
                    <td><img src="http://www.porminuto.com.br/ativo/2015/chip/images/devolucao_chip_01.jpg" border="0" alt="Ativo" style="display:block; border:0px;"></td> </tr> </table>
                    <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" id="Table_01">
                    <tr>
                    <td valign="top" bgcolor="#DBDBDB" style="padding:20px 28px;">
                    <font style="font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:20px; color:#000; text-decoration:none; line-height:15px">
                    <b>Prezado (a) '.$nome.',</b> </font><br> <br>
                    '.$conteudo.'
                    </td>
                    </tr> </table>
                    <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                    <tbody>

                    <tr>
                    <td width="100%" height="5"></td>
                    </tr>

                    <tr>
                    <td align="center" valign="middle" style="font-family: Helvetica, arial, sans-serif; font-size: 13px;color: #000000" st-content="preheader">Não deseja mais receber nossos emails? <a href="%%unsub_center_url%%" target="_blank" style="text-decoration: none; color: #541849;">Cancele aqui</a> <br>
                    <br>
                    <span style="font-family: Helvetica, arial, sans-serif; font-size: 13px;color: #000;">Adicione  ativo@pratique.ativo.com  aos seus contatos para receber<br>
                    com sucesso nossos e-mails </span></td>
                    </tr>

                    <tr>
                    <td width="100%" height="5"></td>
                    </tr>

                    </tbody>
                    </table>';

		#echo $mensagem;
		#exit;
		$exact = Email::exactEmail(strtolower($email), $mensagem, 'RotinasProcessamento', 1, 0, 0, $assunto);

		if(!$exact){
			echo "Ocorreu algum erro!<br>";
		}


	}
	public static function enviaXML($xml)
	{
		$response = self::aknaIntegracao($xml);
		$env = $response;


		// AVALIA RETORNO DO DISPARO
		$env = explode("\n", $env);
		foreach ($env as $env) {
		   if (trim($env) == '<RETURN ID="00">Ok</RETURN>') {
		       $env = 'Ok';
		       break;
		   }
		}


		// RETORNO DESTA FUNÇÃO
		if ('Ok' == $env){
			return true;
		}else{
			return false;
		}

	}

	public static function aknaEmail($ds_email, $mensagem, $parametro, $tipo, $id_user, $id, $assunto = null) 
	{

		// SETA TIPO DE AÇÃO TRANSACIONAL PADRÃO SE NECESSÁRIO
		$ds_tipo = $parametro;
		$consultaAcoesXml = "<main><emkt trans='19.03'></emkt></main>";
		$response = self::aknaIntegracao($consultaAcoesXml);
			// converte retorno de XML para ARRAY
			$response = simplexml_load_string($response);
			$response = json_encode($response);
			$response = json_decode($response, true);		
		$acoes = $response['EMKT']['ACAO'];
		$tipos = array();
		foreach ($acoes as $tipo) {
			array_push($tipos, substr($tipo, 4));
		}
		if (!in_array($ds_tipo, $tipos)) {
			$ds_tipo = "Geral";
		}


		// TORNA ASPAS SIMPLES O PADRÃO, E NÃO ASPAS DUPLA
		$ds_html = $mensagem;
		$ds_html = str_replace(array("'"), array("&apos;"), $ds_html);
		$ds_html = str_replace(array('"'), array("'"), $ds_html);
		$ds_html = str_replace(array("&apos;"), array('"'), $ds_html);
		$ds_html = str_replace(array('&nbsp;'), array(" "), $ds_html);


		// MONTA XML DO DISPARO
		$xml = "<main>
		<emkt trans='20.05'>
		<acao>Acao$ds_tipo</acao>
		<envio_minutos>0.01</envio_minutos>
		<destinatario>
		  <email>$ds_email</email>
		  <infoadc20><![CDATA[$ds_html]]></infoadc20>
		</destinatario>
		</emkt>
		</main>";
	   
		
		// EFETUA DISPARO
		$response = self::aknaIntegracao($xml);
		$env = $response;


		// AVALIA RETORNO DO DISPARO
		$env = explode("\n", $env);
		foreach ($env as $env) {
		   if (trim($env) == '<RETURN ID="00">Ok</RETURN>') {
		       $env = 'Ok';
		       break;
		   }
		}


		// RETORNO DESTA FUNÇÃO
		if ('Ok' == $env){
			return 1;
		}else{
			throw new Exception("Erro com o disparo de e-mail da AKNA. Retorno: " . $response."email:". $ds_email);
		}
		
	}

	public static function aknaIntegracao($xml)
	{

		// EFETUA DISPARO
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => "http://app.akna.com.br/emkt/int/integracao.php",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "UTF-8",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => array(
		  'User' => 'menon.machado@ativo.com',
		  'Pass' => '06c5f00994474aba39d3b8d2298cf571',
		  'Client' => '26326',
		  'XML' => $xml),
		));
		$response = curl_exec($curl);
		curl_close($curl); 

		return $response;

	}
}
?>
