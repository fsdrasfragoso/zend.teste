<?php
class Superativo_Services_Eventos_Certificado{

//obtém certificado do evento
	public function getCertificadoEvento($id){
	
		  $EventoModel = new EventoModel();
		  $lista = $EventoModel->getEvento($id);

		  return $lista;
		  //nm_certificado_evento
    }
	
	



}

?>