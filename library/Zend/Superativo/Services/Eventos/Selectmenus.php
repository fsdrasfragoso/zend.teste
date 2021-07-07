<?php
class Superativo_Services_Eventos_Selectmenus{

/* TODAS AS LISTAGENS DE MENUS - COMBOS */


// cidades dos eventos
	public function listarCidades(){
      
		  $EventoModel = new EventoModel();
		  $lista = $EventoModel->getEventosCidades1();

		  return $lista;  
		  
    }

//lista todas as cidades do banco	
	public function listartodasCidades(){
      
		  $CidadeModel = new CidadeModel();
		  $lista = $CidadeModel->gettodasCidades();

		  return $lista;  
		  
    }		

//combo distância dos eventos (modalidade - depende do evento)
	public function listarDistancias($id_evento){
      
		  $ModalidadeblocoModel = new ModalidadeblocoModel();
		  $lista = $ModalidadeblocoModel->getListaModalidades($id_evento);

		  return $lista;
    }
	
//aba resultado lista de eventos	
	public function listarEventos(){
      
		  $EventoModel = new EventoModel();
		  $lista = $EventoModel->getEventoList();
		  return $lista;

	}	

//aba resultado categorias	(depende da modalidade)
	public function listarCategorias($id_modalidade){
      
		  $ModalcatblocoModel = new ModalcatblocoModel();
		  $lista = $ModalcatblocoModel->getModallists2($id_modalidade);
		  return $lista;

	}	


	
}

?>