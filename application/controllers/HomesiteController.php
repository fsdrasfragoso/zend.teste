<?php

class HomesiteController extends Zend_Controller_Action
{
	public function init()
	{
		$sessao = new Zend_Session_Namespace('autenticacao');
		if($sessao->username == null)
			$this->_helper->redirector->gotoSimple("index", "login");
		
		$this->view->menu();
	}
	public function permissoes()
	{
		$UsuarioGrupoModel = new UsuarioGrupoModel();
		$GrupoPermissaoModel = new GrupoPermissaoModel();
		$sessao = new Zend_Session_Namespace('autenticacao');
		$grupos = $UsuarioGrupoModel->getGrupoByUsuario($sessao->id_usuario);
		$permissoes_array;
		foreach($grupos as $grupo):
			$permissoes = $GrupoPermissaoModel->getPermissaoMenuGrupo($grupo["id_grupo"]);
			foreach($permissoes as $menu => $permissao):
				$permissoes_array[$menu] = $permissao;
			endforeach;
		endforeach;
		return $permissoes_array[$this->_request->getParam("id_menu")];

	}

	public function verificaPermissao()
	{

		if(!in_array($this->_request->getParam("id_permissao"), $this->permissoes())){
			return "0";
		}

		$this->_helper->redirector->gotoSimple("acessonegado", "home");
	}
        public function indexAction()
	{ 
                $NoticiasModel = new NoticiasModel();
                $EventoModel = new EventosModel();
                $WebdoorModel = new WebdoorModel();
                $EnqueteModel = new EnqueteModel(); 
                $HomesiteModel = new HomesiteModel();
                $Homesiteposicao = new HomesiteposicaoModel();
                
                $this->view->posicao = $Homesiteposicao->getTudo();
                $this->view->dados = $HomesiteModel->getHomeSite();
                $this->view->dadosb1 = $HomesiteModel->getbyPosicao("bloco1");
                $this->view->dadosb2 = $HomesiteModel->getbyPosicao("bloco2");
                $this->view->dadosb3 = $HomesiteModel->getbyPosicao("bloco3");
                $this->view->dadosb4 = $HomesiteModel->getbyPosicao("bloco4");
                $this->view->dadosb5 = $HomesiteModel->getbyPosicao("bloco5");
                
                $this->view->webdoors = $WebdoorModel->getWebdoorHomeSite(2); 

                      
                $enquete = $EnqueteModel->getEnqueteHomesite(); 
                $this->view->enquetepergunta = $enquete[0]["ds_enquete"];
                $this->view->id_enquetepergunta = $enquete[0]["id_enquete"];
                $this->view->enquete = $enquete;

                $this->view->mlidas = $NoticiasModel->getNoticiaMaisLidas("");
                $this->view->outrasNot = $NoticiasModel->getNoticia();
        
        
                $this->view->eventosHome = $EventoModel->getHomeUltimos();

                $image = $video = $NoticiasModel->getMidiaNoticia("2", 0);
                $this->view->galeriaImagem = $image;

                $video = $this->view->videoOX = $NoticiasModel->getMidiaNoticia("3", 0);
                $this->view->videoDestaque = $video[0];
                unset($video[0]);
                $this->view->galeriaVideo = $video;     

//                $noticiaDestaque = $NoticiasModel->getNoticiaDestaque(3);
//                $this->view->noticiaDestaque1 = $noticiaDestaque[0];
//                unset($noticiaDestaque[0]);
//                $this->view->noticiaDestaque2 = $noticiaDestaque; 
//
//                $this->view->conteudoTre = $NoticiasModel->getNoticiaHome(3, "treinamento");
//                $this->view->conteudoSau = $NoticiasModel->getNoticiaHome(3, "saude");
//                $this->view->conteudoNut = $NoticiasModel->getNoticiaHome(3, "nutricao");
//                $this->view->conteudoNot = $NoticiasModel->getNoticiaHome(3, "noticia");
//
//                $this->view->conteudo2 = $NoticiasModel->getNoticiaHome(5, "");
                
	}
        
	public function novoAction()
	{
            
                $this->_helper->layout()->disableLayout();
                                
                $MenuSelectModel = new MenuSelectModel();
                $HomesiteModel = new HomesiteModel();
                
		$id = $this->_request->getParam("id");
		$posicao = $this->_request->getParam("posicao");
		$ordem = $this->_request->getParam("ordem");
                                
                $this->view->dataNoticia = $HomesiteModel->getDadosHomeSite($id, $posicao, $ordem);
               
		$this->view->menu_list = $MenuSelectModel->getMenuList();
		$this->view->id_menu = $this->_request->getParam("id_menu");

                $ColunistaModel = new ColunistaModel();
                $this->view->list_colunista = $ColunistaModel->getListColunista();

		$this->render('formulario');
	} 
        
        public function saveAction()
	{

                $HomesiteModel = new HomesiteModel();
		
		if($_POST["id_menu_contexto"] == ""){
			$_POST["id_menu_contexto"] = 0;
		}
		if($_POST["id_home_site_posicao"] == ""){
			$_POST["id_home_site_posicao"] = 0;
		}
		if($_POST["nr_ordem"] == ""){
			$_POST["nr_ordem"] = 0;
		}
                
		$data = array(
			"id_contexto" => $_POST['id_contexto'],
			"id_menu_contexto" => $_POST['id_menu_contexto'],
			"id_noticia" => $_POST['id_noticia'],
			"ds_titulo" => $_POST['ds_titulo'],
			"ds_subtitulo" => $_POST['ds_subtitulo'],
			"id_situacao_cadastro" => 1,
			"id_home_site_posicao" => $_POST['id_home_site_posicao'],
			"nr_ordem" => $_POST['nr_ordem']
		);

		if($_POST['id_home_site'] != null || $_POST['id_home_site'] != 0)
		{
                
                    $id = $_POST['id_home_site'];
                
			try{
				$sessao = new Zend_Session_Namespace('autenticacao');
                                $data["dt_cadastro"] = date('Y-m-d H:i:s');
				$HomesiteModel->update($data, "id_home_site = $id");
				$this->_helper->redirector->gotoSimple("index", "homesite", null, array("id_menu" => $this->_request->getParam("id_menu")));
			}catch(Exception $e){
				echo $e->getMessage();
				die();
			}
		}else{
			try{
				$sessao = new Zend_Session_Namespace('autenticacao');
                                $data["dt_cadastro"] = date('Y-m-d H:i:s');
				$id = $HomesiteModel->insert($data);
				$this->_helper->redirector->gotoSimple("index", "homesite", null, array("id_menu" => $this->_request->getParam("id_menu")));
			}catch(Exception $e)
			{
				echo $e->getMessage();
				die();
			}
		}
	}
        public function mudasituacaoAction()
	{
             $this->_helper->layout()->disableLayout();

                $HomesiteposicaoModel = new HomesiteposicaoModel();
		
                $data = array("id_situacao_cadastro" => $_POST['id_situacao_cadastro']);
                
		$id = $_POST['id_home_site_posicao'];
                
			try{
                               $HomesiteposicaoModel->update($data, "id_home_site_posicao = $id");
                               die("TRUE");
                               
			}catch(Exception $e){
				echo $e->getMessage();
				die($e);
                        }
	}    
        public function mudaordemAction()
	{
            $this->_helper->layout()->disableLayout();

            $HomesiteModel = new HomesiteModel();

            $id = $this->_request->getParam("id_noticia");
            $nr_ordem = $this->_request->getParam("nr_ordem");
            
            $data = array("nr_ordem" => $nr_ordem);

                    try{
                           $HomesiteModel->update($data, "id_noticia = $id");
                           die("TRUE");

                    }catch(Exception $e){
                            echo $e->getMessage();
                            die($e);
                    }
	}        
        
        public function mudaposicaoAction()
	{
            $this->_helper->layout()->disableLayout();

            $HomesiteposicaoModel = new HomesiteposicaoModel();

            $id_posicao = $this->_request->getParam("id_posicao");
            $ds_div = $this->_request->getParam("id_nome");
            
            switch ($id_posicao) {
                case 1: $id= 3;
                    break;
                case 2: $id= 6;
                    break;
                case 3: $id= 8;
                    break;
                case 4: $id= 10;
                    break;
                case 5: $id= 11;
                    break;
                default:
                    break;
            }
                     
//            $consultaposicao = $HomesiteModel->getbyPosicao($ds_div);
//
//            foreach ($consultaposicao as $lista) {
//                if($lista["id_home_site_posicao"] != $id){                    
//
//                    $dados = array("id_home_site_posicao" => $id);
//
//                    $HomesiteModel->update($dados, "id_home_site = ".$lista["id_home_site"]);
//                    
//                }
//            }   
            
            $data = array("nr_home_site_posicao" => $id);

                    try{
                           
                           $HomesiteposicaoModel->update($data, "ds_div = '".$ds_div."'");
                           die("TRUE");

                    }catch(Exception $e){
                            echo $e->getMessage();
                            die($e);
                    }
	} 
} 
?>