<?php

class Zend_View_Helper_Menu extends Zend_View_Helper_Abstract
{
	public function menu()
	{
		
		$MenuModel = new MenuModel();
		$this->view->menu_principal = $MenuModel->getMenuPrincipal();
		$this->view->menu_filho = $MenuModel->getMenus();
		
		
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
		
		if($sessao->id_grupo == 2 && $sessao->faturamento != 1)
			unset($permissoes_array[158]);
		
		$this->view->permissoes = $permissoes_array;
		$this->view->menu_rapido = $GrupoPermissaoModel->getMenuRapido($sessao->id_grupo);
		
		$this->view->id_menu = Zend_Controller_Front::getInstance()->getRequest()->getParam('id_menu');;
		
	}
	
}
	