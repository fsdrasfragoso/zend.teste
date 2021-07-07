<?php
	require_once ('../soapclient/SforceEnterpriseClient.php');
	require_once ('userAuth.php');',
'	$mySforceConnection = new SforceEnterpriseClient();
	$mySoapClient = $mySforceConnection->createConnection('../soapclient/enterprise.wsdl.xml');
	$mylogin = $mySforceConnection->login($USERNAME, $PASSWORD);	',
'	//CRIANDO REGISTROS
	/*$records = array();
	$records[0] = new stdclass();
	$records[0]->FirstName = 'Jean Farrell';
	$records[0]->LastName = 'Vidal da Silva';
	$records[0]->Phone = '(11) 1111-1111';
	$records[0]->BirthDate = '1986-03-07';
	$response = $mySforceConnection->create($records, 'Contact');
	$ids = array();
	echo '<table border="1" width="500">';
	echo '<tr><td colspan="4"><center>CRIANDO REGISTROS</center></td></tr>';
	echo '<tr><td>Id</td><td>FirstName</td><td>LastName</td><td>Phone</td></tr>';		
	foreach ($response as $i => $result) 
	{
		echo '<tr>';
		echo '<td>'.$result->id.'</td>';
		echo '<td>'.$records[$i]->FirstName.'</td>';
		echo '<td>'.$records[$i]->LastName.'</td>';
		echo '<td>'.$records[$i]->Phone.'</td>';
		echo '</tr>';
		array_push($ids, $result->id);',
'	}
	echo '</table>';
	exit;*/
	',
'	//LISTANDO REGISTROS
	$query = "SELECT Id, FirstName, LastName, Phone from Contact where FirstName like '%jean farrell%'";
	$lista_registros = $mySforceConnection->query($query);
	echo '<table border="1" width="500">';
	echo '<tr><td colspan="4"><center>EXECUTAR UMA CONSULTA</center></td></tr>';
	echo '<tr><td>Id</td><td>FirstName</td><td>LastName</td><td>Phone</td></tr>';
	$lista = array();		
	foreach ($lista_registros->records as $linha) 
	{
		echo '<tr>';
		echo '<td>'.$linha->Id.'</td>';
		echo '<td>'.$linha->FirstName.'</td>';
		echo '<td>'.$linha->LastName.'</td>';
		echo '<td>'.$linha->Phone.'</td>';
		echo '</tr>';
		array_push($lista, $linha->Id);
	}
	echo '</table>';	',
'	
	//RECUPERANDOS REGISTROS		
	$recuperando = $mySforceConnection->retrieve('Id, FirstName, LastName, Phone','Contact', $lista);
	echo '<table border="1" width="500">';
	echo '<tr><td colspan="4"><center>RECUPERANDOS REGISTROS</center></td></tr>';
	echo '<tr><td>Id</td><td>FirstName</td><td>LastName</td><td>Phone</td></tr>';	
	foreach ($recuperando as $linha)
	{
		echo '<tr>';
		echo '<td>'.$linha->Id.'</td>';
		echo '<td>'.$linha->FirstName.'</td>';
		echo '<td>'.$linha->LastName.'</td>';
		echo '<td>'.$linha->Phone.'</td>';
		echo '</tr>';
	}
	echo '</table>';
	
	
	//ATUALIZANDO REGISTROS
	/*$records[0] = new stdclass();
	$records[0]->Id = $lista[0];
	$records[0]->LastName = 'Vidal da Silva3';
	$records[0]->Phone = '(11) 7777-7777';',
'	$response = $mySforceConnection->update($records, 'Contact');
	echo '<table border="1" width="500">';
	echo '<tr><td><center>ATUALIZANDO REGISTROS</center></td></tr>';
	echo '<tr><td>Id</td></tr>';	
	foreach ($response as $result)
	{
		echo '<tr><td>'.$result->id.'updated</td></tr>';
	}
	echo '</table>';*/
	',
'	//DELETANDO REGISTROS
	/*$response = $mySforceConnection->delete('003K000000KnpjOIAR');
	echo '<table border="1" width="500">';
	echo '<tr><td><center>DELETANDO REGISTROS</center></td></tr>';
	echo '<tr><td>Id</td></tr>';	
	foreach ($response as $result) {
		echo '<tr><td>'.$result->id.' updated</td></tr>';
	}
	echo '</table>';*/',
'
?>