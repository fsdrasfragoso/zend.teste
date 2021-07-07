<?php
class Superativo_Services_Eventos_Kitcategoria{

//obtém kits das categorias
	public function getKitsEvento($id){
	
		  $ModalcatblocoModel = new ModalcatblocoModel();
		  $lista = $ModalcatblocoModel->getKits($id);
		  return $lista;
		 
    }
	
//obtém kit específico
	public function getKit($id){
	
		  $ModalcatblocoModel = new ModalcatblocoModel();
		  $lista = $ModalcatblocoModel->getModalcateg($id);
		  return $lista;
		 
    }
	
//obtém itens de um kit
	public function getKitItens($id){
	
		  $ModalcatkitModel = new ModalcatkitModel();
		  $lista = $ModalcatkitModel->getModalkit($id);
		  return $lista;
		 
    }	
		
	



}

?>