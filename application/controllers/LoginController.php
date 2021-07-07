<?php

class LoginController extends Zend_Controller_Action {

    public function init() {
        $PaisOrigemModel = new PaisOrigemModel();
        $paisOrigem = $PaisOrigemModel->getPaisOrigemBySigla();
        if (count($paisOrigem) > 0) {
            $this->view->esqueci_senha = 'https://'.$paisOrigem['ds_url'].'/user/login'; 
            $this->view->sem_cadastro = 'https://'.$paisOrigem['ds_url'].'/user/signup/'; 
        } else {
            $this->view->esqueci_senha = "#";
            $this->view->sem_cadastro = "#";
        }
    }

    public function indexAction() {
        $MenuModel = new MenuModel();
        $this->view->menu_principal = $MenuModel->getMenuPrincipal();
        $this->view->menu_filho = $MenuModel->getMenuFilho(1);

        $this->view->erro = $this->_request->getParam("erro");
        $this->_helper->layout->setLayout('login');
        // action body
    }
	
    /*public function indexAction() {
        echo "Em manutenção.. Voltamos em breve.";
		exit;
		
    }*/

    public function validaAction() {
        $LoginModel = new LoginModel();
        $LogAcessoModel = new LogAcessoModel();
        $usuario = $LoginModel->validaLogin($_POST['nome'], md5($_POST['senha']));
        $origemValida=0;
        
        if (count($usuario) > 0) {
            //Verifico a origem do acesso
            $localDomain = array("localhost", "com", "br", "local");
            $serverName = $_SERVER['SERVER_NAME'];
            if ($serverName!='localhost') {
                $vetorServer = explode(".", $serverName);
                $totalVetor = count($vetorServer);
                $domain=$vetorServer[$totalVetor-1];
            }
            $domain=(in_array($domain, $localDomain) || empty($domain)) ? 'br' : strtolower($domain);
            $usuario['pa_sigla']=($usuario['pa_sigla']=='') ? 'br' : $usuario['pa_sigla'];
            //Nega acesso se o dominio da URL for diferente do pais de origem do usuario
            $origemValida=($domain!=strtolower($usuario['pa_sigla'])) ? '0' : '1';
        }

        if (count($usuario) > 0 && $origemValida) {
            $sessao = new Zend_Session_Namespace('autenticacao');
            $sessao->username = $usuario['ds_email'];
            $sessao->id_usuario = $usuario["id_usuario"];
            $sessao->id_tipo_usuario = $usuario["id_tipo_usuario"];
            $sessao->nome = $usuario['ds_nome'] . " " . $usuario['ds_sobrenome'];
            $sessao->nomebalcao = $usuario['ds_nomebalcao'];
            $ultimo_acesso = $LogAcessoModel->getUltimoAcesso($usuario['id_usuario']);
            $sessao->ultimo_acesso = $ultimo_acesso;
            $sessao->id_grupo = $usuario["id_grupo"];
            $sessao->id_pais_origem_cadastro = $usuario["id_pais_origem_cadastro"];
            $sessao->faturamento = $usuario["faturamento"];

            if ($ultimo_acesso == false) {
                $LogAcessoModel->insert(array('id_usuario' => $usuario['id_usuario'], 'dt_acesso' => date('Y-m-d H:i:s')));
            } else {
                $id_usuario = $usuario['id_usuario'];
                $LogAcessoModel->update(array("dt_acesso" => date('Y-m-d H:i:s')), "id_usuario = $id_usuario");
            }
			
			@session_start("redir_adm");
			if(isset($_SESSION["redir_adm"])){
				if(substr($_SESSION["redir_adm"], -6) != 'valida')
					$this->_redirect($_SESSION["redir_adm"]);
			}
			
            if($usuario["id_grupo"] == '4')
                $this->_helper->redirector->gotoSimple("painel", "graficos");
			else if($usuario["id_grupo"] == '14')
                $this->_helper->redirector->gotoSimple("fotosvendidasfotografos", "relatorios");
            else
                $this->_helper->redirector->gotoSimple("index", "home");
        } else {
            $this->_helper->redirector->gotoSimple("index", "login", null, array("erro" => true));
        }
    }

    public function destroiAction() {
        Zend_Session::destroy(true);
        $this->_helper->redirector->gotoSimple("index", "login");
    }

}