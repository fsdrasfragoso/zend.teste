<?php
class Superativo_Services_Eventos_Resultado{

//obt�m resultado de um participante
	public function getResultado($id){
	
		  $EventoresultadoModel = new EventoresultadoModel();
		  $lista = $EventoresultadoModel->getEventoResultadoIndividual($id);

		  return $lista;
    }
	
	public function getResultadoOutros($id){
	
		  $EventoresultadooutrosModel = new EventoresultadooutrosModel();
		  $lista = $EventoresultadooutrosModel->getInfo($id);
		  return $lista;
    }	
	




}

?>