<?php
class Superativo_Services_Eventos_Percurso{

//obt�m percurso de um evento - modalidade
	public function getPercurso($id_modalidade){
	
		  $ModalidadeblocoModel = new ModalidadeblocoModel();
		  $lista = $ModalidadeblocoModel->getModalidades1($id_modalidade);

		  return $lista;
    }

//obt�m nome das modalidades (percursos)	
	public function getPercursosTodos($id_evento){
	
		  $ModalidadeblocoModel = new ModalidadeblocoModel();
		  $lista = $ModalidadeblocoModel->getModalidades($id_evento);

		  return $lista;
    }	
	
	
	
	




}

?>