<?php
class Superativo_Services_Eventos_Kitcategoria{

//obt�m kits das categorias
	public function getKitsEvento($id){
	
		  $ModalcatblocoModel = new ModalcatblocoModel();
		  $lista = $ModalcatblocoModel->getKits($id);
		  return $lista;
		 
    }
	
//obt�m kit espec�fico
	public function getKit($id){
	
		  $ModalcatblocoModel = new ModalcatblocoModel();
		  $lista = $ModalcatblocoModel->getModalcateg($id);
		  return $lista;
		 
    }
	
//obt�m itens de um kit
	public function getKitItens($id){
	
		  $ModalcatkitModel = new ModalcatkitModel();
		  $lista = $ModalcatkitModel->getModalkit($id);
		  return $lista;
		 
    }	
		
	



}

?>