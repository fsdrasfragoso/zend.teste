<?php
class Superativo_Services_Eventos_Informacoes{

//obtщm informaчуo sobre um determinado evento
	public function getInfoEvento($id){
	
		  $EventoModel = new EventoModel();
		  $lista = $EventoModel->getEventosServiceInfo($id);

		  return $lista;
    }

	
//obtщm a url da imagem	
	public function getLogoEvento($id){
	
		$EventoImagemModel = new EventoImagemModel();
		$img = $EventoImagemModel->getLogo($id);
		
		return $img['ds_url_imagem'];	
    }


}

?>