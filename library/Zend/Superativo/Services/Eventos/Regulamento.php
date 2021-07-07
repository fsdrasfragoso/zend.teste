<?php
class Superativo_Services_Eventos_Regulamento{

//obtém regulamento
	public function getRegulamento($id,$tipo){
	
		  $EventoModel = new EventoModel();
		  $lista = $EventoModel->getEvento($id);
		  
		  if($tipo == "pdf"){
			return $lista['ds_regulamento'];
		  }else{
			return $lista['ds_regulamento_txt'];
		  }

		 // return $lista;
    }
	
	
	




}

?>