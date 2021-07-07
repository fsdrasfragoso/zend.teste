<?php
include 'conn/config.php';
include 'conn/conecta.php';


class Sms2 /*extends Zend_Controller_Action*/{
    

	public function EnviaSms($params, $type = false) {
		
		$usuario        = "championchip.rest";           // Usuário Comunika
        $senha          = "d1dBX6xN7R";                // Senha Comunika
        
        $autorizacao = base64_encode("$usuario:$senha");

		$remetente = $params['ds_nome'];       	
		$destinatario = '55'.$params['telefone'];   
		$agendamento = "";    					
		$identificador = $params['id_usuario']; 
		$modoTeste = 0; // 0 = modo normal: envia a mensagem, 1 = modo teste: não envia e a mensagem não aparece no painel de controle
		
		if($type){
			$mensagem = "Acesse o link para visualizar as informacoes do evento: http://www.ativo.com/recibo/".$params['code'];
                        $params['code'] = base64_decode($params['code']);
                        $tipo='Pedido Confirmado';
                } else {
		$mensagem = "Seu codigo para alterar sua senha: ".$params['code'];
                $tipo='Esqueci minha Senha';
                }

                
		/*///// monta o conteúdo do parâmetro "messages" (não alterar)
		$codedMsg       = $remetente."\t".$destinatario."\t".$agendamento."\t".$mensagem."\t".$identificador;


		///// configura parâmetros de conexão (não alterar)
		$path           = "/3.0/user_message_send.php";
		$parametros     = $path."?testmode=".$modoTeste."&linesep=0&user=".urlencode($usuario)."&pass=".urlencode($senha)."&messages=".urlencode($codedMsg);*/
		$url            = "https://api-rest.zenvia360.com.br/services/send-sms";


        ///// realiza a conexão
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{
        \"sendSmsRequest\": {
          \"from\": \"$remetente\",
          \"to\": \"$destinatario\",
          \"schedule\": \"$agendamento\",
          \"msg\": \"$mensagem\",
          \"callbackOption\": \"NONE\",
          \"id\": \"$identificador\"
        }
      }");
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Basic ".$autorizacao,
            "Accept: application/json"
        ));

        $result = curl_exec ($ch);


		///// verifica o resultado
		$error      = explode("\n",urldecode($result));
		$error[0]   = (int)trim($error[0]);

		if($error[0] != 0){
			///// para o caso de um erro genérico
			$errorCode  = $error[0];
		} else {
			///// para o caso de erro específico
			$errorPhone	= explode(" ",urldecode($error[1]));
			$errorCode  = $errorPhone[0];
		}

		switch($errorCode) {
			case 0   : $msg = "Mensagem enviada com sucesso"; break;
			case 1   : $msg = "Problemas de conexão"; break;
			case 10  : $msg = "Username e/ou Senha inválido(s)"; break;
			case 11  : $msg = "Parâmetro(s) inválido(s) ou faltando"; break;
			case 12  : $msg = "Número de telefone inválido ou não coberto pelo Comunika"; break;
			case 13  : $msg = "Operadora desativada para envio de mensagens"; break;
			case 14  : $msg = "Usuário não pode enviar mensagens para esta operadora"; break;
			case 15  : $msg = "Créditos insuficientes";	break;
			case 16  : $msg = "Tempo mínimo entre duas requisições em andamento"; break;
			case 17  : $msg = "Permissão negada para a utilização do CGI/Produtos Comunika"; break;
			case 18  : $msg = "Operadora Offline"; break;
			case 19  : $msg = "IP de origem negado"; break;
			case 404 : $msg = "Página não encontrada"; break;
		}
           
		if($errorCode==0)
        $this->LogSMS($remetente, $destinatario, $identificador, $tipo, $params['code'], $mensagem, $errorCode, $msg);
	
		echo($errorCode.":".$msg);
	}
        
        public function LogSMS($remetente, $destinatario, $identificador, $tipo, $pedido, $mensagem, $errorCode, $msg)
        {
            #echo $remetente." | ".$destinatario." | ".$identificador." | ".$mensagem." | ".$errorCode." | ".$msg;
            
            $sql="INSERT INTO sa_log_sms (remetente, destinatario, identificador, tipo, pedido,  mensagem, errorCode, msg) 
            VALUES ('".$remetente."', '".$destinatario."', '".$identificador."', '".$tipo."', '".$pedido."', '".$mensagem."', '".$errorCode."', '".$msg."')";
            
            $resultado = mysql_query($sql)/* or die ("Error in query: $sql. ".mysql_error())*/;
            
        }
		
}
?>