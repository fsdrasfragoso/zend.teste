<?php
class Superativo_Services_Eventos_Informacoes{

//obt�m informa��o sobre um determinado evento
	public function getInfoEvento($id){
	
		  $EventoModel = new EventoModel();
		  $lista = $EventoModel->getEventosServiceInfo($id);

		  return $lista;
    }

	
//obt�m a url da imagem	
	public function getLogoEvento($id){
	
		$EventoImagemModel = new EventoImagemModel();
		$img = $EventoImagemModel->getLogo($id);
		
		return $img['ds_url_imagem'];	
    }


}

?>