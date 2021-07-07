<?php
// SOAP_CLIENT_BASEDIR - folder that contains the PHP Toolkit and your WSDL
// $USERNAME - variable that contains your Salesforce.com username (must be in the form of an email)
// $PASSWORD - variable that contains your Salesforce.com password
define("SOAP_CLIENT_BASEDIR", "../soapclient");
require_once (SOAP_CLIENT_BASEDIR.'/SforceEnterpriseClient.php');
require_once ('userAuth.php');
echo("<pre>");',
'$query = "SELECT * FROM usuarios LIMIT 0,1";
$resultado = mysql_query($query);',
'
try {
  $mySforceConnection = new SforceEnterpriseClient();
  $mySoapClient = $mySforceConnection->createConnection(SOAP_CLIENT_BASEDIR.'/enterprise.wsdl.xml');
  $mylogin = $mySforceConnection->login($USERNAME, $PASSWORD);
  $campos = array();
  $i = 0;
  while($linha = mysql_fetch_array($resultado))
  {
	//Facebook
	if($linha['facebook'] == 'S')
		$linha['facebook'] = 1;
	else
		$linha['facebook'] = 0;
	
	$nome = explode(' ',$linha['nome']);
	if($nome[1] == '')
	{
		$nome[1] = "-";
	}
	
	array_push($campos, array('LastName' => str_replace(',','',$nome[1]),
     'FirstName' => str_replace(',','',$nome[0]),
     'RecordTypeId' => '012d00000003kyhAAA',
	 'Amigo__pc' => 0,
	 'Aventura__pc' => 0,
	 'Caminhada__pc' => 0,
	 'Ciclismo__pc' => 0,
	 'Corrida__pc' => 0,
	 'Educacao__pc' => 0,
	 'Feira__pc' => 0,
	 'Fitness__pc' => 0,
	 'Futebol__pc' => 0,
	 'Natacao__pc' => 0,
	 'Optin__pc' => 0,
	 'Optin_parceiro__pc' => 0,
	 'Bairro_cobranca__c' => str_replace(',','',$linha['endereco']),
	 'PersonMobilePhone' => str_replace(',','',$linha['tel']),
	 'Celular_emergencia__pc' => str_replace(',','',$linha['emergencia_celular']),
	 'BillingPostalCode' => ' ',
	 'PersonMailingPostalCode' => str_replace(',','',$linha['cep']),
	 'ShippingPostalCode' => ' ',
	 'BillingCity' => ' ',
	 'PersonMailingCity' => str_replace(',','',$linha['cidade']),
	 'ShippingCity' => ' ',
	 'Codigo_conta__c' => 'SI'.$linha['id_usuario'],
	 'Complemento_cobranca__c' => str_replace(',','',$linha['complemento']),
	 'Complemento_entrega__c' => str_replace(',','',$linha['complemento']),
	 'PersonBirthdate' => str_replace(',','',$linha['data_nascimento']),
	 'PersonEmail' => str_replace(',','',$linha['email']),
	 'Email_de_emergencia__pc' => str_replace(',','',$linha['emergencia_email']),
	 'Email_II__pc' => ' ',
	 'Email_III__pc' => ' ',
	 'Equipe__pc' => str_replace(',','',$linha['equipe']),
	 'BillingState' => str_replace(',','',$linha['estado']),
	 'PersonMailingState' => str_replace(',','',$linha['estado']),
	 'ShippingState' => str_replace(',','',$linha['estado']),
	 'Interagir_com_Facebook__pc' => str_replace(',','',$linha['facebook']),
	 'Marca_preferida_de_tenis__pc' => str_replace(',','',$linha['marca_tenis']),
	 'Marca_preferida_de_vestu_rio__pc' => str_replace(',','',$linha['marca_vestuario']),
	 'Nome_emergencia__pc' => str_replace(',','',$linha['emergencia_nome']),
	 'Numero_calcado__pc' => str_replace(',','',$linha['num_tenis']),
	 'Numero_documento__pc' => str_replace(',','',$linha['doc']),
	 'PersonOtherPhone' => ' ',
	 'BillingCountry' => str_replace(',','',$linha['pais']),
	 'PersonMailingCountry' => str_replace(',','',$linha['pais']),
	 'Pais_origem__pc' => str_replace(',','',$linha['pais_origem']),
	 'Pergunta_1__pc' => ' ',
	 'Pergunta_10__pc' => ' ',
	 'Pergunta_11__pc' => ' ',
	 'Pergunta_12__pc' => ' ',
	 'Pergunta_2__pc' => ' ',
	 'Pergunta_3__pc' => ' ',
	 'Pergunta_4__pc' => ' ',
	 'Pergunta_5__pc' => ' ',
	 'Pergunta_6__pc' => ' ',
	 'Pergunta_7__pc' => ' ',
	 'Pergunta_8__pc' => ' ',
	 'Pergunta_9__pc' => ' ',
	 'BillingStreet' => str_replace(',','',$linha['endereco']),
	 'PersonMailingStreet' => str_replace(',','',$linha['endereco']),
	 'ShippingStreet' => str_replace(',','',$linha['endereco']),
	 'Sexo__pc' => str_replace(',','',$linha['sexo']),
	 'Tam_Camiseta__pc' => ' ',
	 'Phone' => str_replace(',','',$linha['tel']),
	 'PersonAssistantPhone' => str_replace(',','',$linha['emergencia_telefone']),
	 'Tenis__pc' => 0,
	 'Tipo_cadastro__pc' => str_replace(',','',$linha['tipo_usuario']),
	 'Tipo_de_pisada__pc' => str_replace(',','',$linha['tipo_pisada']),
	 'Tipo_documento__pc' => str_replace(',','',$linha['tipo_doc'])));',
'	 foreach($campos[$i] as $key => $name) {
		 if($campos[$i][$key] == '')
		   unset($campos[$i][$key]);
	 }
	 $i++;
/*	 if(count($campos[$i++]) == 0)
		  unset($campos[$i++]);*/
  }
  
/*  print_r($campos);
  exit(0);*/
   
 //print_r($mySforceConnection->create($campos, 'Account'));
 
/* $query = "SELECT PersonEmail FROM Acoount WHERE PersonEmail IN('" . implode("','", array()) . "')";
 $response = $mySforceConnection->query(($query));
 print_r($response);*/',
'  /*echo "***** Get User Info*****\n";
  $response = $mySforceConnection->getUserInfo();
  print_r($response);*/
  
  //$query = 'SELECT Id,Name from Account Order By Name limit 5';
  //$query = "SELECT Id,Name FROM PriceBookEntry ORDER BY Name LIMIT 5";
  //$response = $mySforceConnection->query(($query));
 
  /*foreach ($response->records as $record) {
    print_r($record);
    print_r("<br>");
  }*/
} catch (Exception $e) {
echo "a";
}',
'?>