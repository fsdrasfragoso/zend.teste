<?php
class Superativo_Services_Eventos_Cobertura{

//obtém cobertura do evento - vídeos e imagens
	public function getCobertura($id){
	
		  $EventocoberturaModel = new EventocoberturaModel();
		  $lista = $EventocoberturaModel->getBlocos($id);

		  return $lista;
    }
	
	public function getVideos($id){
	
		  $EventocoberturaModel = new EventocoberturaModel();
		  $lista = $EventocoberturaModel->getTodosVideos($id);

		  return $lista;
    }

	public function getFotos($id){
	
		  $EventocoberturaModel = new EventocoberturaModel();
		  $lista = $EventocoberturaModel->getTodasFotos($id);

		  return $lista;
    }	
	
	
	




}

?>