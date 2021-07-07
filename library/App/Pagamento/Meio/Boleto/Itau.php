<?php
/**
* Criada por diogo em Mar 13, 2013
*
*/

class App_Pagamento_Meio_Boleto_Itau
{
	private $_PRAZO = 5;
	private $_TAXA = 0;
	
	private $_EMPRESA = 'VILLA OLIMPICA SERVIÇO LTDA';
	private $_RAZAO_SOCIAL = 'VILLA OLIMPICA SERVIÇO LTDA';
	private $_ENDERECO = 'Av. Doutor  Yojiro Takaoka, 4384 – 2º and – sl 211 – Centro de Apoio Um – CEP 06541-038';
	private $_CIDADE_UF = 'São Paulo / SP';
	private $_CNPJ = '03.787.821/0001-08';
	
	private $_INSTRUCOES = array(   'SR. CAIXA NÃO RECEBER APOS O VENCIMENTO', 
                                        '', 
                                        '');

	private $_AG = '1608';
	private $_CC = '15728';
        private $_CC_DV = '3';
	private $_CART = '175';
	private $_CART_DESC = 'COBRANÇA SIMPLES - CSR';
	
	public function getParams($valor, $pedido, $nome, $vencimento, $evento_casa = NULL, $evento) 
	{
            
            $dias_de_prazo_para_pagamento = $this->_PRAZO;
            $taxa_boleto = $this->_TAXA;
                
            //$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006";
            
            if($vencimento == "" || $vencimento == 0 ){
                $data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006";
            }else{
                $data_venc = date("d/m/Y", $vencimento);

            }
            
            $valor_cobrado = $valor; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
            $valor_cobrado = str_replace(",", ".", $valor_cobrado);
            $valor_boleto = number_format($valor_cobrado + $taxa_boleto, 2, ',', '');

            $params["nosso_numero"] = $pedido;  // Nosso numero sem o DV - REGRA: Máximo de 7 caracteres!
            $params["numero_documento"] = $pedido;	// Num do pedido ou nosso numero
            $params["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
            $params["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
            $params["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
            $params["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

            // DADOS DO SEU CLIENTE
            $params["sacado"] = $nome;
// 		$params["endereco1"] = $endereco1;
// 		$params["endereco2"] = $endereco2;

            // INFORMACOES PARA O CLIENTE
            $demonstrativo[] = "Pagamento referente ao Pedido: ".$pedido;
            $demonstrativo[] = "Pagamento de inscrição no evento ".$evento;
            
            for($i = 0; $i < count($demonstrativo); $i++) 
            {
                    $params['demonstrativo'.($i + 1)] = $demonstrativo[$i];
            }//for i

            for($i = 0; $i < count($this->_INSTRUCOES); $i++)
            {
                    $params['instrucoes'.($i + 1)] = $this->_INSTRUCOES[$i];
            }//for i		

            // DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
            $params["quantidade"] = "";
            $params["valor_unitario"] = "";
            $params["aceite"] = "N";
            $params["especie"] = "R$";
            $params["especie_doc"] = "OU";

            // ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

            // DADOS DA SUA CONTA
            $params["agencia"] = $this->_AG; // Num da agencia, sem digito
            $params["conta"] = $this->_CC; 	// Num da conta, sem digito
            $params["conta_dv"] = $this->_CC_DV; 	// Digito do Num da conta

            $params["carteira"] = $this->_CART;  // Código da Carteira

            // SEUS DADOS
            $params["identificacao"] = $this->_EMPRESA;
            $params["cpf_cnpj"] = $this->_CNPJ;
            $params["endereco"] = $this->_ENDERECO;
            $params["cidade_uf"] = $this->_CIDADE_UF;
            $params["cedente"] = $this->_RAZAO_SOCIAL;
            
		/*$cfg = Zend_Registry::get('config')->santander;

		$this->_PRAZO = $cfg->prazo;
		$this->_TAXA = $cfg->taxa;
		$this->_EMPRESA =$cfg->empresa;
		$this->_RAZAO_SOCIAL =$cfg->razaosocial;
		$this->_ENDERECO = $cfg->endereco;
		$this->_CIDADE_UF = $cfg->cidade;
		$this->_CNPJ = $cfg->cnpj;

		$this->_INSTRUCOES = array();
		$this->_INSTRUCOES[] = $cfg->instrucoes1;
		$this->_INSTRUCOES[] = $demonstrativo[0];
		$this->_INSTRUCOES[] = $cfg->instrucoes3;

		$this->_AG = $cfg->agencia;
		$this->_CC = $cfg->conta;
		$this->_COD = $cfg->cod;
		
		if($evento_casa == 1){
			$this->_COD = $cfg->codcasa;
		}
		
		$this->_CART = $cfg->carteira;
		$this->_CART_DESC = $cfg->carteiradescricao;

		$dias_de_prazo_para_pagamento = $this->_PRAZO;
		$taxa_boleto = $this->_TAXA;
                
//                if($vencimento == "" || $vencimento == 0 ){
//                    $data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006";
//                }else{
//                    $data_venc = date("d/m/Y", $vencimento);
//                    
//                }
                
		if($vencimento == "" || $vencimento == 0 ){
			$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006";
		}else{
			$data_venc = date("d/m/Y", $vencimento);
			
		}
		$valor_cobrado = $valor; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
		$valor_cobrado = str_replace(",", ".", $valor_cobrado);
		$valor_boleto = number_format($valor_cobrado + $taxa_boleto, 2, ',', '');
		
		$params["nosso_numero"] = $pedido;  // Nosso numero sem o DV - REGRA: Máximo de 7 caracteres!
		$params["numero_documento"] = $pedido;	// Num do pedido ou nosso numero
		$params["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
		$params["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
		$params["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
		$params["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula
		
		// DADOS DO SEU CLIENTE
		$params["sacado"] = $nome;
// 		$params["endereco1"] = $endereco1;
// 		$params["endereco2"] = $endereco2;
		
		// INFORMACOES PARA O CLIENTE
		$demonstrativo[] = "Pagamento referente ao Pedido: ".$pedido;
		for($i = 0; $i < count($demonstrativo); $i++) 
		{
			$params['demonstrativo'.($i + 1)] = $demonstrativo[$i];
		}//for i		

		for($i = 0; $i < count($this->_INSTRUCOES); $i++)
		{
			$params['instrucoes'.($i + 1)] = $this->_INSTRUCOES[$i];
		}//for i		
		
		// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
		$params["quantidade"] = "";
		$params["valor_unitario"] = "";
		$params["aceite"] = "N";
		$params["especie"] = "R$";
		$params["especie_doc"] = "OU";
		
		
		// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //
		
		
		// DADOS PERSONALIZADOS - SANTANDER BANESPA
//		$params["codigo_cliente"] = $this->_COD; // Código do Cliente (PSK) (Somente 7 digitos)
//		$params["ponto_venda"] = $this->_AG; // Ponto de Venda = Agencia
//		$params["carteira"] = $this->_CART;  // Cobrança Simples - SEM Registro
//		$params["carteira_descricao"] = $this->_CART_DESC;  // Descrição da Carteira
		
		// DADOS DA SUA CONTA - Bradesco
		$params["agencia"] = "0516"; // Num da agencia, sem digito
		$params["agencia_dv"] = "9"; // Digito do Num da agencia
		$params["conta"] = "0095333"; 	// Num da conta, sem digito
		$params["conta_dv"] = "4"; 	// Digito do Num da conta
		
		// DADOS PERSONALIZADOS - Bradesco
		$params["conta_cedente"] = "0095333"; // ContaCedente do Cliente, sem digito (Somente Números)
		$params["conta_cedente_dv"] = "4"; // Digito da ContaCedente do Cliente
		$params["carteira"] = "25";  // Código da Carteira: pode ser 06 ou 03
		
		// SEUS DADOS
		$params["identificacao"] = $this->_EMPRESA;
		$params["cpf_cnpj"] = $this->_CNPJ;
		$params["endereco"] = $this->_ENDERECO;
		$params["cidade_uf"] = $this->_CIDADE_UF;
		$params["cedente"] = $this->_RAZAO_SOCIAL;*/
		
		return $params;
	}//getParams
	
	/**
	 * !!! USA OS PARÂMETROS GERADOS NO MÉTODO getParams para renderizar o boleto
	 *
	 * @param array $params
	 * @param Zend_View $view
	 * @return string
	 */
	public function render(array $params, Zend_View $view) 
	{
		$dadosboleto = $params;
		$baseURL = "{$view->baseUrl()}/interface/img/boleto";
		
		ob_start();
		include('boletophp/funcoes_itau.php');
		include('boletophp/layout_itau.php');
		return ob_get_clean();		
	}//render
	
}//App_Pagamento_Meio_Boleto_Santander