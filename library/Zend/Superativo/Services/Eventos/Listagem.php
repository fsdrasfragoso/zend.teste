<?php
class Superativo_Services_Eventos_Listagem{

/* TODAS AS LISTAGENS */


//lista todos os eventos (paginaчуo no front end)	
	public function listarEventos(){
      
		  $EventoModel = new EventoModel();
		  $lista = $EventoModel->getEventosService();

		  return $lista;
    }


//lista modalidades de cada evento	
	public function listarModalidades($id_evento){ 
	
		  $ModalidadeModel = new ModalidadeModel();
		  $lista = $ModalidadeModel->getModalidadeListagem($id_evento);

		  return $lista;
	}


//lista o resultado de cada evento (lista total de participantes)	
	public function listarResultado($id_evento){ 
	
		  $EventoresultadoModel = new EventoresultadoModel();
		  $lista = $EventoresultadoModel->getEventoResultadoLista($id_evento);

		  return $lista;
	}	
	
}

?>